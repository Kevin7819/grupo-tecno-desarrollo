# Grupo Tecno Desarrollo — Radio

Web application for an internet radio station: public site, configurable player, listener accounts and a separate administrator panel.

The station name, slogan, schedule, hosts and contact details are **placeholders**. Do not add content that has not been approved.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Tailwind CSS 4, Vite 8, vanilla JavaScript |
| Database (local) | SQLite |
| Database (production) | MySQL (to be confirmed with the hosting plan) |
| Tests | PHPUnit 12, SQLite in memory |

## Requirements

- PHP **8.4** with `pdo_sqlite` (local) and the usual Laravel extensions.
- Composer 2.
- Node.js 24 and pnpm (the repository ships `pnpm-lock.yaml`).

On this machine PHP 8.4 lives outside `PATH`:

```powershell
C:\php84\php.exe -v
C:\php84\php.exe C:\composer\composer.phar --version
```

Do not use the XAMPP PHP 8.2 binary for this project.

## Local setup

```powershell
# 1. Dependencies
C:\php84\php.exe C:\composer\composer.phar install
corepack pnpm install --frozen-lockfile

# 2. Environment
Copy-Item .env.example .env
C:\php84\php.exe artisan key:generate

# 3. Database
C:\php84\php.exe artisan migrate

# 4. First administrator (interactive, no default password)
C:\php84\php.exe artisan radio:create-admin

# 5. Assets
corepack pnpm run build

# 6. Development server
C:\php84\php.exe artisan serve
```

The application is then available at `http://127.0.0.1:8000`.

For frontend hot reload, run `corepack pnpm run dev` in a second terminal.

## Environment variables

The stream is configuration, not code. Add this block to `.env`:

```dotenv
APP_NAME="Grupo Tecno Desarrollo"

RADIO_STREAM_URL=
RADIO_STREAM_FORMAT=mp3
RADIO_STREAM_NAME=

RADIO_CONTACT_EMAIL=

RADIO_SOCIAL_INSTAGRAM=
RADIO_SOCIAL_FACEBOOK=
RADIO_SOCIAL_TIKTOK=
RADIO_SOCIAL_YOUTUBE=
RADIO_SOCIAL_WHATSAPP=

LOG_LEVEL=warning
```

| Variable | Meaning |
|---|---|
| `RADIO_STREAM_URL` | Public HTTP(S) URL of the audio stream. **Leave empty** until a provider is confirmed. |
| `RADIO_STREAM_FORMAT` | `mp3`, `aac` or `hls`. Unknown values fall back to `mp3`. |
| `RADIO_STREAM_NAME` | Optional display name for the current stream. |
| `RADIO_CONTACT_EMAIL` | Public contact address. Invalid values are ignored. |
| `RADIO_SOCIAL_*` | Official profiles, one per variable. Only `https://` URLs are published. |
| `LOG_LEVEL` | Laravel log threshold. `warning` keeps debug noise out of normal logs; use `debug` only while diagnosing. |

`APP_URL` must match the origin you open in the browser. `artisan serve` prints `http://127.0.0.1:8000`; if you open that address while `APP_URL` says `http://localhost:8000`, generated links and the session cookie use different hosts. Keep both values identical in local development.

An empty or invalid URL renders the explicit **«Transmisión no configurada»** state: no audio element, no dead button and no console error. Laravel never serves audio.

> The local agent policy in this environment denies edits to `.env` and `.env.example`. `.env.example` therefore keeps the framework defaults; the variables above are documented here instead of being pre-filled. Add them manually before configuring a stream.

Production values must be set in the hosting panel or the server `.env`. Never commit `.env` or real credentials.

### Temporary test stream: Radio Swiss Jazz

The player can be tested with a real external station. Radio Swiss Jazz is **not** the station's own stream, and it must never be described as its own content or programming.

```dotenv
RADIO_STREAM_URL=https://stream.srg-ssr.ch/srgssr/rsj/mp3/128
RADIO_STREAM_FORMAT=mp3
RADIO_STREAM_NAME="Radio Swiss Jazz"
```

Before using it, the endpoint was verified with `GET` (the server answers `HEAD` with `405`): `200 OK`, `content-type: audio/mpeg`, first bytes `FF FB 94 04` (MPEG-1 Layer III, 128 kbps), `icy-name: Swiss Jazz`, no `icy-metaint` header — the stream carries no in-band metadata, so the player never shows a current track title.

To remove it, empty `RADIO_STREAM_URL` (or delete the three variables). The site then returns to the **«Transmisión no configurada»** state: no audio element, no compact player and no dead controls. `RADIO_STREAM_NAME` is the only visible station name; nothing is hardcoded in the views.

If the endpoint stops working, do not invent a replacement URL: report it and verify a new candidate the same way before configuring it.

## Accounts

| | Listener | Administrator |
|---|---|---|
| Guard | `web` | `admin` |
| Model / table | `User` / `users` | `AdminUser` / `admin_users` |
| Login | `/login` | `/admin/login` |
| Panel | `/profile` | `/admin` |
| Registration | Public | **None** |
| Provisioning | `/register` | `php artisan radio:create-admin` |

Both guards are independent. A listener session is not valid for the admin panel and an admin session is not valid for listener routes. Administrator password recovery is not implemented; until it is approved, recovery is a server-side operation.

### Development seeders

`php artisan db:seed` creates listener test accounts and a development administrator. Both seeders are gated by `config/seeding.php` and only run in the environments listed there (`local` by default); they never run in production, and they skip instead of inventing a password when the variables are missing.

```dotenv
ADMIN_SEED_NAME="Administrador de desarrollo"
ADMIN_SEED_EMAIL=
ADMIN_SEED_PASSWORD=

SEED_USER_PASSWORD=
```

| Variable | Meaning |
|---|---|
| `ADMIN_SEED_NAME` | Display name of the development administrator. |
| `ADMIN_SEED_EMAIL` | E-mail of the development administrator. Required. |
| `ADMIN_SEED_PASSWORD` | Password for that account. Required, at least 12 characters, hashed by Laravel before storage. |
| `SEED_USER_PASSWORD` | Password shared by the three listener test accounts. Required for listener seeding. |

- `php artisan db:seed` runs every seeder.
- `php artisan db:seed --class=AdminUserSeeder` runs one.
- Re-running is safe: accounts are matched by e-mail and existing rows are never modified.
- To stop seeding, remove the credentials; to provision a real administrator use `php artisan radio:create-admin`.


## Tests and quality

```powershell
C:\php84\php.exe artisan test
C:\php84\php.exe vendor\bin\pint --test
C:\php84\php.exe C:\composer\composer.phar audit --locked
corepack pnpm audit
corepack pnpm run build
```

The suite covers the public site and player states, listener registration/login/logout, password reset and token reuse, profile isolation, the separate admin guard, the provisioning command, rate limiting, security headers and the structural presence of CSRF protection.

Laravel skips CSRF enforcement while `APP_ENV=testing`. The 419 path is therefore also verified against a real local server:

```powershell
# POST /login without _token  -> 419
# POST /login with a valid _token -> reaches the controller
```

See `docs/architecture.md` for the layering and security decisions, and `docs/deployment-namecheap.md` before deploying.

## Project layout

```
app/Domain/          Framework-free values and rules
app/Application/     Use cases, DTOs and the contracts they need
app/Infrastructure/  Eloquent repositories, config adapter, hashing
app/Http/            Controllers, form requests, middleware
config/radio.php     Stream configuration
docs/                Architecture and deployment notes
odd/tasks/           Feature specification and work log
```

## Known limitations

- HLS plays natively (Safari/iOS). Other browsers show an explicit unsupported state; `hls.js` is a pending decision.
- Administrator password recovery is not implemented.
- Email verification is prepared in the schema but not enforced.
- The public site uses placeholder copy until the station supplies approved content.
- The streaming provider, SMTP and the Namecheap plan are not confirmed.

## License

Application code for Grupo Tecno Desarrollo. Laravel and third-party packages keep their own licenses.
