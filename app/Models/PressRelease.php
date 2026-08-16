<?php
declare(strict_types=1);

final class PressRelease
{
    public static function recordPR(array $arguments, array $user): ?array
    {
        try {

            db()->beginTransaction();

            $statement = db()->prepare(
                'INSERT INTO press_releases
                    (title, description, event_date, cover_photo)
                VALUES
                    (?, ?, ?, ?)'
            );

            $statement->execute([
                trim($arguments['title'] ?? ''),
                trim($arguments['description'] ?? ''),
                trim($arguments['event_date'] ?? ''),
                trim($arguments['cover_photo'] ?? ''),
            ]);

            $id = (int) db()->lastInsertId();

            log_event(
                'press_release_created',
                ['pr_id' => $id]
            );

            $links = $arguments['links'] ?? [];

            foreach ($links as $k => $link) {

                if ($k === 0) {
                    $link['is_primary'] = (int) 1;
                } else {
                    $link['is_primary'] = (int) 0;
                }

                db()->prepare(
                    'INSERT INTO press_release_links
                        (
                            press_release_id,
                            news_source,
                            news_content_type,
                            date_released,
                            media_logo,
                            media_outlet,
                            link,
                            is_primary
                        )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                )->execute([
                    $id,
                    trim($link['news_source'] ?? ''),
                    trim($link['news_content_type'] ?? ''),
                    trim($link['date_released'] ?? ''),
                    trim($link['media_logo'] ?? ''),
                    trim($link['media_outlet'] ?? ''),
                    trim($link['link'] ?? ''),
                    $link['is_primary'],
                ]);
            }

            db()->commit();

            return [
                'id' => $id,
            ];

        } catch (PDOException $e) {

            if (db()->inTransaction()) {
                db()->rollBack();
            }

            throw $e;
        }
    }

    public static function updatePR(
        int $id,
        array $arguments,
        array $user
    ): bool {
        try {

            db()->beginTransaction();

            $statement = db()->prepare(
                'UPDATE press_releases
                SET title = ?,
                    description = ?,
                    event_date = ?,
                    cover_photo = ?
                WHERE id = ?'
            );

            $statement->execute([
                trim($arguments['title'] ?? ''),
                trim($arguments['description'] ?? ''),
                trim($arguments['event_date'] ?? ''),
                trim($arguments['cover_photo'] ?? ''),
                $id,
            ]);

            /*
            * Remove existing links first.
            * They will be recreated from the submitted form.
            */
            db()->prepare(
                'DELETE FROM press_release_links
                WHERE press_release_id = ?'
            )->execute([$id]);

            $links = $arguments['links'] ?? [];

            foreach ($links as $link) {

                if (empty($link['link'])) {
                    continue;
                }

                $isPrimary = !empty($link['is_primary'])
                    ? 1
                    : 0;

                db()->prepare(
                    'INSERT INTO press_release_links
                        (
                            press_release_id,
                            news_source,
                            news_content_type,
                            date_released,
                            media_logo,
                            media_outlet,
                            link,
                            is_primary
                        )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                )->execute([
                    $id,
                    trim($link['news_source'] ?? ''),
                    trim($link['news_content_type'] ?? ''),
                    trim($link['date_released'] ?? ''),
                    trim($link['media_logo'] ?? ''),
                    trim($link['media_outlet'] ?? ''),
                    trim($link['link'] ?? ''),
                    $isPrimary,
                ]);
            }

            db()->commit();

            log_event(
                'press_release_updated',
                ['pr_id' => $id]
            );

            return true;

        } catch (PDOException $e) {

            if (db()->inTransaction()) {
                db()->rollBack();
            }

            throw $e;
        }
    }

    public static function deletePR(
        int $id,
        array $user
    ): bool {
        try {

            db()->beginTransaction();

            /*
            * Get the cover photo before deleting the record.
            */
            $statement = db()->prepare(
                'SELECT cover_photo
                FROM press_releases
                WHERE id = ?'
            );

            $statement->execute([$id]);

            $pressRelease = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$pressRelease) {
                db()->rollBack();
                return false;
            }

            /*
            * Delete links first.
            */
            db()->prepare(
                'DELETE FROM press_release_links
                WHERE press_release_id = ?'
            )->execute([$id]);

            /*
            * Delete press release.
            */
            db()->prepare(
                'DELETE FROM press_releases
                WHERE id = ?'
            )->execute([$id]);

            db()->commit();

            /*
            * Delete physical cover photo.
            */
            if (!empty($pressRelease['cover_photo'])) {

                $root = config(
                    'STORAGE_PATH',
                    dirname(__DIR__, 2) . '/storage'
                );

                $filePath = rtrim($root, '/\\')
                    . DIRECTORY_SEPARATOR
                    . str_replace(
                        ['/', '\\'],
                        DIRECTORY_SEPARATOR,
                        $pressRelease['cover_photo']
                    );

                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            log_event(
                'press_release_deleted',
                ['pr_id' => $id]
            );

            return true;

        } catch (PDOException $e) {

            if (db()->inTransaction()) {
                db()->rollBack();
            }

            throw $e;
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
        if($id == 0) {
            $statement = db()->prepare('SELECT *, 
                                            press_releases.id AS pr_id,
                                            press_release_links.id AS prl_id
                                        FROM press_releases 
                                        join press_release_links on press_releases.id = press_release_links.press_release_id
                                        where press_release_links.is_primary = 1
                                        ORDER BY date_released DESC, press_releases.id DESC LIMIT 1');
            $statement->execute();
            return $statement->fetch() ?: null;
            
        } else {

            $statement = db()->prepare(
                'SELECT *,
                    press_releases.id AS pr_id
                FROM press_releases
                WHERE press_releases.id = ?'
            );

            $statement->execute([$id]);
            $pressRelease = $statement->fetch(PDO::FETCH_ASSOC);

            $linksStatement = db()->prepare(
                'SELECT *
                FROM press_release_links
                WHERE press_release_id = ?
                ORDER BY is_primary DESC, id ASC'
            );

            $linksStatement->execute([$id]);
            $links = $linksStatement->fetchAll(PDO::FETCH_ASSOC);

            $pressRelease['links'] = $links;
            return $pressRelease;
        }
        

    }

    public static function getPaginatedPressReleases(
        int $page = 1,
        int $perPage = 4,
        int $prid = 0
    ): array {

        $page = max(1, $page);
        $perPage = max(1, $perPage);

        /*
        * Count the actual press releases.
        *
        * We count press_releases directly because pagination
        * is based on press releases, not individual links.
        */
        $countStatement = db()->query(
            'SELECT COUNT(*)
            FROM press_releases'
        );

        $totalItems = (int) $countStatement->fetchColumn();

        $totalPages = max(
            1,
            (int) ceil($totalItems / $perPage)
        );

        /*
        * If a specific press release was requested,
        * make sure it is included in the result.
        */
        if ($prid > 0) {

            $statement = db()->prepare(
                'SELECT *
                FROM press_releases
                WHERE id = :id
                LIMIT 1'
            );

            $statement->execute([
                'id' => $prid
            ]);

            $selectedRelease = $statement->fetch(PDO::FETCH_ASSOC);

            if ($selectedRelease) {

                /*
                * Get all links for this press release.
                */
                $linksStatement = db()->prepare(
                    'SELECT *
                    FROM press_release_links
                    WHERE press_release_id = :press_release_id
                    ORDER BY date_released DESC, id ASC'
                );

                $linksStatement->execute([
                    'press_release_id' => $prid
                ]);

                $selectedRelease['links'] =
                    $linksStatement->fetchAll(PDO::FETCH_ASSOC) ?: [];

                /*
                * Get the normal paginated releases as well.
                */
                $offset = ($page - 1) * $perPage;

                $statement = db()->prepare(
                    'SELECT *
                    FROM press_releases
                    WHERE id != :id
                    ORDER BY event_date DESC, id DESC
                    LIMIT :limit OFFSET :offset'
                );

                $statement->bindValue(
                    ':id',
                    $prid,
                    PDO::PARAM_INT
                );

                $statement->bindValue(
                    ':limit',
                    $perPage,
                    PDO::PARAM_INT
                );

                $statement->bindValue(
                    ':offset',
                    $offset,
                    PDO::PARAM_INT
                );

                $statement->execute();

                $rows = $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];

                /*
                * Get links for every returned press release.
                */
                foreach ($rows as &$row) {

                    $linksStatement = db()->prepare(
                        'SELECT *
                        FROM press_release_links
                        WHERE press_release_id = :press_release_id
                        ORDER BY date_released DESC, id ASC'
                    );

                    $linksStatement->execute([
                        'press_release_id' => $row['id']
                    ]);

                    $row['links'] =
                        $linksStatement->fetchAll(PDO::FETCH_ASSOC) ?: [];
                }

                unset($row);

                /*
                * Put the selected press release first.
                */
                array_unshift($rows, $selectedRelease);

                return [
                    'items' => $rows,
                    'selected' => $selectedRelease,
                    'currentPage' => $page,
                    'perPage' => $perPage,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                ];
            }
        }

        /*
        * Normal first page / normal pagination.
        */
        $offset = ($page - 1) * $perPage;

        $statement = db()->prepare(
            'SELECT *
            FROM press_releases
            ORDER BY event_date DESC, id DESC
            LIMIT :limit OFFSET :offset'
        );

        $statement->bindValue(
            ':limit',
            $perPage,
            PDO::PARAM_INT
        );

        $statement->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];

        /*
        * Attach all links to each press release.
        */
        foreach ($rows as &$row) {

            $linksStatement = db()->prepare(
                'SELECT *
                FROM press_release_links
                WHERE press_release_id = :press_release_id
                ORDER BY date_released DESC, id ASC'
            );

            $linksStatement->execute([
                'press_release_id' => $row['id']
            ]);

            $row['links'] =
                $linksStatement->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        unset($row);

        return [
            'items' => $rows,
            'selected' => null,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}
