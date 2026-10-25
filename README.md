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

The stream is configuration, not code. Add this block to `.env` (and keep it in sync with `.env.example` in your own checkout):

```dotenv
APP_NAME="Grupo Tecno Desarrollo"

RADIO_STREAM_URL=
RADIO_STREAM_FORMAT=mp3
RADIO_STREAM_NAME=
```

| Variable | Meaning |
|---|---|
| `RADIO_STREAM_URL` | Public HTTP(S) URL of the audio stream. **Leave empty** until a provider is confirmed. |
| `RADIO_STREAM_FORMAT` | `mp3`, `aac` or `hls`. Unknown values fall back to `mp3`. |
| `RADIO_STREAM_NAME` | Optional display name for the current stream. |

An empty or invalid URL renders the explicit **«Transmisión no configurada»** state: no audio element, no dead button and no console error. Laravel never serves audio.

> The local agent policy in this environment denies edits to `.env` and `.env.example`. The variables above are therefore documented here instead of being pre-filled in the example file. Add them manually before configuring a stream.

Production values must be set in the hosting panel or the server `.env`. Never commit `.env` or real credentials.

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
