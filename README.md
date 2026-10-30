# LIGHTHOUSE — Milestone One

Server-rendered PHP 8.2 + MySQL/MariaDB private admin application. No client-side credentials or public registration are present.

## Local setup

1. Copy `.env.example` to `.env` and set database values, `APP_KEY`, and `INITIAL_SUPER_ADMIN_PASSWORD`. For the required first account, use `lighthouse@yr43` only in your private `.env`.
2. Create the database named by `DB_DATABASE`.
3. Run `php lighthouse migrate` then `php lighthouse seed` from the project directory.
4. Ensure the web-service user can write `storage/logs` and `storage/sessions`, `chmod -R +w storage/` for local, `chmod -R g+rwX storage/` for production then serve the `public` folder: `php -S localhost:8000 -t public`, UPDATED: `php -S localhost:8000 server.php`.
5. Visit `/login` and sign in using `INITIAL_SUPER_ADMIN_EMAIL` and the password supplied in `.env`.

Configure the documented SMTP environment variables before requesting a password reset; reset links are deliberately never written to application logs. Production must also set `SESSION_SECURE_COOKIE=true`, `APP_ENV=production`, HTTPS, and `APP_DEBUG=false`.

## Bulk user upload

After signing in as the Super Admin, open **User Management** (`/users`), download the XLSX template, fill the header row without changing its columns, then upload the `.xlsx` file. `serial_no` and `name` are required; birthdays accept Excel date cells or `YYYY-MM-DD`, `MM/DD/YYYY`, and `DD/MM/YYYY`. Each new serial number gets username `serial_no`, temporary password `lighthouse@mop`, Admin role, and a required password change. Rows matching an existing serial number update profile fields without changing that account's password. Email addresses may be shared. New users can sign in with their serial number; email/password login continues to work when that combination identifies one account. Manual registration also requires a serial number and accepts an optional email.

The upload uses PHP's `zip` and `simplexml` extensions to read XLSX files; make sure both are enabled. Migration `016_add_user_upload_fields.sql` adds the user profile fields and makes email optional for serial-number accounts.

Bulk user rows are staged in `bulk_user_upload_rows` and processed in batches of 50. The existing upload activity panel tracks progress and resumes processing when the user opens another LIGHTHOUSE page. Completed jobs link to their created/updated/failed-row summary. Migration `017_extend_upload_jobs_for_bulk_users.sql` adds the progress counters and staging table.

Migration `018_allow_duplicate_user_emails.sql` removes the unique email index and keeps a non-unique lookup index. If an email and password match multiple accounts, email login is rejected as ambiguous; sign in with the account's serial number. Password reset links by email are issued only when that email belongs to exactly one active account.

## Notes

- `php lighthouse migrate` is idempotent; `php lighthouse seed` creates the initial Super Admin only if absent.
- Passwords and reset tokens are Argon2id hashes. Reset tokens expire, are single-use, and never appear in logs.
- Configure web-server rewrites so all requests are routed to `public/index.php` (Apache `.htaccess`/Nginx equivalent).
