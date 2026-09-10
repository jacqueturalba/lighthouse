<?php

declare(strict_types=1);

final class SFlexPost
{
    private static function select(): string
    {
        return "SELECT p.*, u.name author, " .
               "(SELECT COUNT(*) FROM sflex_comments c WHERE c.post_id = p.id AND c.hidden_at IS NULL) comments, " .
               "(SELECT reaction FROM sflex_reactions r WHERE r.post_id = p.id AND r.user_id = ?) mine " .
               "FROM sflex_posts p " .
               "JOIN users u ON u.id = p.user_id";
    }

    public static function feed(int $user, int $offset): array
    {
        $s = db()->prepare(self::select() . " WHERE p.status = 'approved' ORDER BY p.created_at DESC LIMIT 5 OFFSET ?");
        $s->bindValue(1, $user, PDO::PARAM_INT);
        $s->bindValue(2, $offset, PDO::PARAM_INT);
        $s->execute();
        $rows = $s->fetchAll();

        foreach ($rows as &$r) {
            $q = db()->prepare('SELECT reaction, COUNT(*) total FROM sflex_reactions WHERE post_id = ? GROUP BY reaction');
            $q->execute([$r['id']]);
            $r['counts'] = $q->fetchAll();
        }

        return $rows;
    }

    /**
     * Return one feed batch plus whether another batch is available.  Fetching
     * one extra row avoids a separate COUNT query on every infinite-scroll hit.
     */
    public static function feedPage(int $user, int $page, int $perPage = 5): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $s = db()->prepare(
            self::select()
            . " WHERE p.status = 'approved' ORDER BY p.created_at DESC LIMIT "
            . ($perPage + 1)
            . " OFFSET ?"
        );
        $s->bindValue(1, $user, PDO::PARAM_INT);
        $s->bindValue(2, $offset, PDO::PARAM_INT);
        $s->execute();
        $rows = $s->fetchAll();

        $hasMore = count($rows) > $perPage;
        if ($hasMore) {
            array_pop($rows);
        }

        foreach ($rows as &$r) {
            $q = db()->prepare('SELECT reaction, COUNT(*) total FROM sflex_reactions WHERE post_id = ? GROUP BY reaction');
            $q->execute([$r['id']]);
            $r['counts'] = $q->fetchAll();
        }
        unset($r);

        return [
            'posts' => $rows,
            'has_more' => $hasMore,
            'next_page' => $hasMore ? $page + 1 : null,
        ];
    }

    public static function create(int $user, string $caption, ?string $path, ?string $type, string $status): void
    {
        db()->prepare('INSERT INTO sflex_posts(user_id, caption, media_path, media_type, status) VALUES(?, ?, ?, ?, ?)')
          ->execute([$user, $caption, $path, $type, $status]);
    }

    public static function rejected(int $user, bool $isSuperAdmin): array
    {
        $sql = self::select() . " WHERE p.status = 'rejected'";

        $params = [$user]; // Required by self::select()

        if (!$isSuperAdmin) {
            $sql .= " AND p.user_id = ?";
            $params[] = $user;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $s = db()->prepare($sql);
        $s->execute($params);

        $rows = $s->fetchAll();

        foreach ($rows as &$r) {
            $q = db()->prepare(
                'SELECT reaction, COUNT(*) total
                FROM sflex_reactions
                WHERE post_id = ?
                GROUP BY reaction'
            );

            $q->execute([$r['id']]);
            $r['counts'] = $q->fetchAll();
        }

        unset($r);

        return $rows;
    }

    public static function pending(int $user): array
    {
        $sql = self::select()
            . " WHERE p.status = 'pending'
                ORDER BY p.created_at ASC";

        $s = db()->prepare($sql);
        $s->execute([$user]);

        $rows = $s->fetchAll();

        foreach ($rows as &$r) {
            $q = db()->prepare(
                'SELECT reaction, COUNT(*) total
                FROM sflex_reactions
                WHERE post_id = ?
                GROUP BY reaction'
            );

            $q->execute([$r['id']]);
            $r['counts'] = $q->fetchAll();
        }

        unset($r);

        return $rows;
    }

    public static function review(int $postId, string $status): void
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new InvalidArgumentException('Invalid review status.');
        }

        $s = db()->prepare(
            "UPDATE sflex_posts
            SET status = ?
            WHERE id = ?
            AND status = 'pending'"
        );

        $s->execute([$status, $postId]);
    }

    public static function react(int $post, int $user, string $reaction): void
    {
        $s = db()->prepare('SELECT reaction FROM sflex_reactions WHERE post_id = ? AND user_id = ?');
        $s->execute([$post, $user]);

        if ($s->fetchColumn() === $reaction) {
            db()->prepare('DELETE FROM sflex_reactions WHERE post_id = ? AND user_id = ?')
              ->execute([$post, $user]);
            return;
        }

        db()->prepare('INSERT INTO sflex_reactions(post_id, user_id, reaction) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE reaction = VALUES(reaction), created_at = NOW()')
          ->execute([$post, $user, $reaction]);
    }

    public static function comments(int $post, bool $admin): array
    {
        $q = 'SELECT c.*, u.name author FROM sflex_comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = ?' . 
             ($admin ? '' : ' AND c.hidden_at IS NULL') . 
             ' ORDER BY c.created_at ASC';
        
        $s = db()->prepare($q);
        $s->execute([$post]);
        
        return $s->fetchAll();
    }

    public static function comment(int $post, int $user, string $body): void
    {
        db()->prepare('INSERT INTO sflex_comments(post_id, user_id, body) VALUES(?, ?, ?)')
          ->execute([$post, $user, $body]);
    }

    public static function find(int $id, int $userId): ?array
    {
        $s = db()->prepare(
            "SELECT p.*, u.name author,
                    (
                        SELECT reaction
                        FROM sflex_reactions r
                        WHERE r.post_id = p.id
                        AND r.user_id = ?
                    ) mine
            FROM sflex_posts p
            JOIN users u ON u.id = p.user_id
            WHERE p.id = ?"
        );

        $s->execute([$userId, $id]);

        $row = $s->fetch();

        if (!$row) {
            return null;
        }

        $q = db()->prepare(
            "SELECT reaction, COUNT(*) total
            FROM sflex_reactions
            WHERE post_id = ?
            GROUP BY reaction"
        );

        $q->execute([$id]);
        $row['counts'] = $q->fetchAll();

        $q = db()->prepare(
            "SELECT c.*, u.name author
            FROM sflex_comments c
            JOIN users u ON u.id = c.user_id
            WHERE c.post_id = ?
            AND c.hidden_at IS NULL
            ORDER BY c.created_at ASC"
        );

        $q->execute([$id]);
        $row['comments'] = $q->fetchAll();

        return $row;
    }
}
