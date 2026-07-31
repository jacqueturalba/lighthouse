<?php
declare(strict_types=1);

final class User
{
    public static function findByEmail(string $email): ?array
    {
        $statement = db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $statement->execute([$email]);
        return $statement->fetch() ?: null;
    }

    public static function findActiveById(int $id): ?array
    {
        $statement = db()->prepare('SELECT id,name,email,role,status,last_login_at,must_change_password FROM users WHERE id=? AND status="active"');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public static function recordLogin(int $id): void
    {
        db()->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$id]);
    }
}
