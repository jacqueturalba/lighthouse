<?php
declare(strict_types=1);

final class PressRelease
{
    public static function recordPressRelease(array $arguments): ?array
    {
        try{
            $id = db()->prepare('INSERT INTO press_releases (title,description,event_date, cover_photo) VALUES (?,?,?,?,?,?,?,?,?)')->execute([trim($arguments['title']??''),trim($arguments['description']??''),trim($arguments['event_date']??''),trim($arguments['cover_photo']??'')]);
            log_event('press_release_created',['pr_id'=>$id]);flash('success','Press release created.');

            $links = $arguments['links'] ?? [];
            foreach ($links as $link) {
                if (empty($link['url'])) {
                    continue;
                }

                $isPrimary = !empty($link['is_primary']) ? 1 : 0;

                db()->prepare('INSERT INTO press_release_links (press_release_id, news_source, news_content_type, date_released, media_logo, media_outlet, link, is_primary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')->execute([$id, trim($link['news_source']??''), trim($link['news_content_type']??''), trim($link['date_released']??''), trim($link['media_logo']??''), trim($link['media_outlet']??''), trim($link['link']), $isPrimary]);
            }

        }catch(PDOException $e){
            flash('error','Press release failed to create.');
        } 
    }

    public static function getAllPressReleases(): array
    {
        $statement = db()->query(
            'SELECT *
            FROM press_releases
            join press_release_links on press_releases.id = press_release_links.press_release_id
            where press_release_links.is_primary = 1
            ORDER BY date_released DESC, press_releases.id DESC'
        );

        return $statement->fetchAll() ?: [];
    }

    public static function countPressReleases(): int
    {
        $statement = db()->query(
            'SELECT COUNT(*) FROM press_releases'
        );

        return (int) $statement->fetchColumn();
    }

    public static function getPressReleases(
        int $limit,
        int $offset
    ): array {
        $statement = db()->prepare(
            'SELECT *
            FROM press_releases
            join press_release_links on press_releases.id = press_release_links.press_release_id
            where press_release_links.is_primary = 1
            ORDER BY date_released DESC, press_releases.id DESC
            LIMIT :limit OFFSET :offset'
        );

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll() ?: [];
    }

    public static function getPressReleaseById(int $id): ?array
    {   
        if($id <= 0) {
            $statement = db()->prepare('SELECT * FROM press_releases 
                                        join press_release_links on press_releases.id = press_release_links.press_release_id
                                        where press_release_links.is_primary = 1
                                        ORDER BY date_released DESC, press_releases.id DESC LIMIT 1');
            $statement->execute();
            
        } else {
            $statement = db()->prepare('SELECT * FROM press_releases 
                                        join press_release_links on press_releases.id = press_release_links.press_release_id
                                        where press_release_links.is_primary = 1 AND press_releases.id = ?
                                        ORDER BY date_released DESC, press_releases.id DESC LIMIT 1');
            $statement->execute([$id]);
        }
        return $statement->fetch() ?: null;

    }

    public static function getPaginatedPressReleases(
        int $page = 1,
        int $perPage = 4
    ): array {
        
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        // Keep the latest release separate.
        $latestStatement = db()->query(
            'SELECT *, press_releases.id as pr_id,  press_release_links.id as prl_id
            FROM press_releases 
            join press_release_links on press_releases.id = press_release_links.press_release_id
            where press_release_links.is_primary = 1
            ORDER BY date_released DESC, press_releases.id DESC 
            LIMIT 1'
        );

        $latest = $latestStatement->fetch() ?: null;

        $latestLinks = db()->prepare(
            'SELECT *
            FROM press_release_links
            WHERE press_release_id = :press_release_id'
        );

        $latestLinks->bindValue(
            ':press_release_id',
            $latest['pr_id'] ?? null,
            PDO::PARAM_INT
        );

        $latestLinks->execute();
        $latestLinksData = $latestLinks->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Count releases excluding the latest one.
        $countStatement = db()->query(
            'SELECT COUNT(*) 
            FROM press_releases 
            join press_release_links on press_releases.id = press_release_links.press_release_id
            where press_release_links.is_primary = 1
            AND press_releases.id != (
                SELECT press_releases.id
                FROM press_releases
                join press_release_links on press_releases.id = press_release_links.press_release_id
                where press_release_links.is_primary = 1
                ORDER BY date_released DESC, press_releases.id DESC
                LIMIT 1
            )'
        );

        $totalOlder = (int) $countStatement->fetchColumn();

        $totalPages = max(1, (int) ceil($totalOlder / $perPage));

        $page = min($page, $totalPages);

        $offset = ($page - 1) * $perPage;

        $statement = db()->prepare(
            'SELECT *
            FROM press_releases
            JOIN press_release_links ON press_releases.id = press_release_links.press_release_id
            WHERE press_releases.id != (
                SELECT press_releases.id
                FROM press_releases
                join press_release_links on press_releases.id = press_release_links.press_release_id
                where press_release_links.is_primary = 1
                ORDER BY date_released DESC, press_releases.id DESC
                LIMIT 1
            )
            AND press_release_links.is_primary = 1
            ORDER BY date_released DESC, press_releases.id DESC
            LIMIT :limit OFFSET :offset'
        );

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();

        return [
            'latest' => $latest,
            'latestLinks' => $latestLinksData,
            'items' => $statement->fetchAll() ?: [],
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalOlder,
            'totalPages' => $totalPages,
        ];
    }
}
