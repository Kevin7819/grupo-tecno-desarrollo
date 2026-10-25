# Deployment checklist — Namecheap shared hosting

**Status: not deployed.** No Namecheap plan has been purchased and no production configuration has been applied. This document lists what must be confirmed before deploying; it is not a promise that the current plan supports it.

## 1. Confirm the plan before writing any production configuration

Ask the hosting provider (or verify in cPanel) for each item. Do not assume it exists.

| Capability | Why it matters | Confirmed? |
|---|---|---|
| PHP 8.4 selectable | Laravel 13 requires PHP 8.3+; the project targets 8.4 | ☐ |
| PHP extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `bcmath` | Framework and database | ☐ |
| SSH access | `composer install`, `artisan migrate`, `artisan optimize` | ☐ |
| Composer available or installable | Production dependencies | ☐ |
| Document root configurable to `public/` | Never expose `app/`, `config/`, `.env`, `storage/` | ☐ |
| MySQL database + user | Production persistence | ☐ |
| HTTPS with a valid certificate | Secure cookies, CSP, listener trust | ☐ |
| SMTP credentials | Password reset email | ☐ |
| Cron jobs (if queues are used) | `queue:work` or `schedule:run` | ☐ |
| Writable `storage/` and `bootstrap/cache/` | Logs, sessions, compiled views | ☐ |
| Backup policy | Database and uploaded files | ☐ |

If SSH or a configurable document root is unavailable, the deployment model must be reviewed before proceeding. Do not put the Laravel root inside `public_html`.

## 2. Streaming provider

The player only needs a public HTTP(S) audio URL.

- Confirm the provider, the stream URL and the format (`mp3`, `aac` or `hls`).
- Confirm CORS is not required for native playback (it is not for `<audio>`), and that the URL is reachable over HTTPS.
- Do not proxy or store audio in Laravel.
- Leave `RADIO_STREAM_URL` empty until the URL is confirmed; the site then shows «Transmisión no configurada».
- HLS is native on Safari/iOS. Other browsers show an explicit unsupported state until `hls.js` is approved and its CSP impact is reviewed.

## 3. Build and upload

Build assets where the toolchain is available, then upload the result:

```powershell
corepack pnpm install --frozen-lockfile
corepack pnpm run build
```

`public/build/` is generated and git-ignored; it must exist in production.

On the server (with SSH):

```bash
composer install --no-dev --optimize-autoloader --no-interaction
cp .env.example .env
php artisan key:generate
```

Set the production `.env` values (never commit them):

```dotenv
APP_NAME="Grupo Tecno Desarrollo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="${APP_NAME}"

RADIO_STREAM_URL=
RADIO_STREAM_FORMAT=mp3
RADIO_STREAM_NAME=
```

Then:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan radio:create-admin
```

Do not run `migrate:fresh` against a database that may contain data.

## 4. Web server and permissions

- Point the document root to `public/`.
- Force HTTPS and redirect HTTP to HTTPS.
- Ensure `storage/` and `bootstrap/cache/` are writable by the PHP user.
- Do not grant world-writable permissions (`chmod 777`).
- Keep `.env`, `composer.lock`, `storage/` and the Laravel root outside the web root.
- The application sends `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` and, outside `local`/`testing`, a Content-Security-Policy. Confirm the host does not strip them.

## 5. Cookies and sessions

In production, `config/session.php` must use secure cookies. Confirm:

- `SESSION_SECURE_COOKIE=true` (or the equivalent HTTPS-only setting).
- `SESSION_SAME_SITE=lax`.
- `SESSION_ENCRYPT` may be enabled after confirming the session driver.
- `APP_URL` uses `https://`.

## 6. Mail

Password reset depends on working SMTP.

1. Configure SMTP.
2. Register a test listener.
3. Request a reset link.
4. Confirm delivery, link host, expiry and single use.
5. Only then consider enforcing email verification.

## 7. Queues and scheduled work

The current configuration uses the database queue and cache drivers, and no scheduled jobs are required by v1. If a queue is enabled later:

- Add a cron entry for `php artisan queue:work --stop-when-empty` or a supervised worker.
- Add `php artisan schedule:run` if scheduled tasks are introduced.
- Confirm the `jobs`, `job_batches` and `failed_jobs` tables are migrated.

## 8. Post-deploy verification

- [ ] `https://your-domain.example/` returns 200 over HTTPS.
- [ ] The player shows the configured stream or the explicit unconfigured state.
- [ ] `http://` redirects to `https://`.
- [ ] `/admin` redirects guests to `/admin/login`.
- [ ] A listener cannot access `/admin`.
- [ ] An administrator can log in and log out.
- [ ] Registration, login, logout and password reset work with real SMTP.
- [ ] A POST without a CSRF token returns 419.
- [ ] Security headers are present in the response.
- [ ] Logs contain no passwords, tokens or secrets.
- [ ] `storage/logs/laravel.log` is writable and rotated.
- [ ] A database backup exists and has been restored in a test environment.

## 9. Rollback

1. Keep the previous release directory and database dump.
2. Put the site in maintenance mode: `php artisan down`.
3. Restore the previous code and, only if the migration is reversible and data loss is understood, roll back the migration.
4. Clear caches: `php artisan optimize:clear`.
5. Bring the site back: `php artisan up`.
6. Verify the checklist above.

## 10. Open decisions

- Namecheap plan and its real capabilities.
- Production database engine and backup policy.
- SMTP provider and sender domain.
- Streaming provider, URL and format.
- Whether `hls.js` is needed for non-Safari browsers.
- Administrator password recovery flow.
- Whether email verification becomes mandatory.
