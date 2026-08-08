<?php
declare(strict_types=1);

final class PressRelease
{
    public static function recordPressRelease(array $arguments): ?array
    {
        try{
            $id = db()->prepare('INSERT INTO press_releases (title,description,news_source,news_content_type,date_released, cover_photo, media_logo, link, media_outlet) VALUES (?,?,?,?,?,?,?,?,?)')->execute([trim($arguments['title']??''),trim($arguments['description']??''),trim($arguments['news_source']??''),trim($arguments['news_content_type']??''),trim($arguments['date_released']??''),trim($arguments['cover_photo']??''),trim($arguments['media_logo']??''),trim($arguments['link']??''),trim($arguments['media_outlet']??'')]);
            log_event('press_release_created',['pr_id'=>$id]);flash('success','Press release created.');

        }catch(PDOException $e){
            flash('error','Press release failed to create.');
        } 
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
            ORDER BY date_released DESC, id DESC
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
            $statement = db()->prepare('SELECT * FROM press_releases ORDER BY date_released DESC, id DESC LIMIT 1');
            $statement->execute();
            
        } else {
            $statement = db()->prepare('SELECT * FROM press_releases WHERE id=? ORDER BY date_released DESC, id DESC LIMIT 1');
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
            'SELECT * 
            FROM press_releases 
            ORDER BY date_released DESC, id DESC 
            LIMIT 1'
        );

        $latest = $latestStatement->fetch() ?: null;

        // Count releases excluding the latest one.
        $countStatement = db()->query(
            'SELECT COUNT(*) 
            FROM press_releases 
            WHERE id != (
                SELECT id
                FROM press_releases
                ORDER BY date_released DESC, id DESC
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
            WHERE id != (
                SELECT id
                FROM press_releases
                ORDER BY date_released DESC, id DESC
                LIMIT 1
            )
            ORDER BY date_released DESC, id DESC
            LIMIT :limit OFFSET :offset'
        );

        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();

        return [
            'latest' => $latest,
            'items' => $statement->fetchAll() ?: [],
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalOlder,
            'totalPages' => $totalPages,
        ];
    }
}
