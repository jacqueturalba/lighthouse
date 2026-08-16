# LIGHTHOUSE — Milestone One

Server-rendered PHP 8.2 + MySQL/MariaDB private admin application. No client-side credentials or public registration are present.

## Local setup

1. Copy `.env.example` to `.env` and set database values, `APP_KEY`, and `INITIAL_SUPER_ADMIN_PASSWORD`. For the required first account, use `lighthouse@yr43` only in your private `.env`.
2. Create the database named by `DB_DATABASE`.
3. Run `php lighthouse migrate` then `php lighthouse seed` from the project directory.
4. Ensure the web-service user can write `storage/logs` and `storage/sessions`, `chmod -R +w storage/` for local, `chmod -R g+rwX storage/` for production then serve the `public` folder: `php -S localhost:8000 -t public`, UPDATED: `php -S localhost:8000 server.php`.
5. Visit `/login` and sign in using `INITIAL_SUPER_ADMIN_EMAIL` and the password supplied in `.env`.

Configure the documented SMTP environment variables before requesting a password reset; reset links are deliberately never written to application logs. Production must also set `SESSION_SECURE_COOKIE=true`, `APP_ENV=production`, HTTPS, and `APP_DEBUG=false`.

## Notes

- `php lighthouse migrate` is idempotent; `php lighthouse seed` creates the initial Super Admin only if absent.
- Passwords and reset tokens are Argon2id hashes. Reset tokens expire, are single-use, and never appear in logs.
- Configure web-server rewrites so all requests are routed to `public/index.php` (Apache `.htaccess`/Nginx equivalent).
