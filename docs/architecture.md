# Architecture decisions — Grupo Tecno Desarrollo (radio v1)

This document records the decisions that shape the codebase. It is intentionally short: one entry per decision, with the reason and the consequence.

## 1. Pragmatic Clean Architecture

```
app/Domain/          Framework-free values and rules
app/Application/     Use cases, DTOs and the contracts they need
app/Infrastructure/  Adapters: Eloquent repositories, config, hashing
app/Http/            Controllers, form requests, middleware
```

Dependency rule: `Domain` depends on nothing; `Application` depends on `Domain` and on its own contracts; `Infrastructure` and `Http` depend inward. No domain class imports `Illuminate`.

What we deliberately did **not** build:

- A repository per model. Only two repositories exist, both because a use case must persist without knowing Eloquent: `ListenerRepository` and `AdminAccountRepository`.
- An interface per class. `StreamFormat` and `StreamConfig` are plain value objects with no contract.
- A generic `BaseRepository`. It would wrap Eloquent without isolating anything.
- A service container "manager" layer. Bindings live in `AppServiceProvider`.

The use cases are testable with in-memory fakes; `tests/Unit/Application` proves it without booting the framework.

## 2. Two guards, two tables

| | Listener | Administrator |
|---|---|---|
| Guard | `web` | `admin` |
| Provider | `users` | `admin_users` |
| Model | `App\Models\User` | `App\Models\AdminUser` |
| Login | `/login` | `/admin/login` |
| Middleware | `auth` | `admin.auth` |

Why two tables instead of a `role` column: privilege is structural. A listener row cannot become an administrator row by mass assignment, a broken policy or a missing check, because the session guard reads a different table. There is no public administrative registration route.

No permission package in v1. The guard *is* the role. A finer role model can be added later with a migration and a `Gate`; it is not needed to protect a single dashboard.

## 3. Admin provisioning

`php artisan radio:create-admin` asks for name, email, password and confirmation. It never prints the password, never truncates the table, and refuses duplicates. `DatabaseSeeder` creates no users or administrators. The Eloquent factories keep a fixed password (`password`) for automated tests only; it is public and must never be used in production.

Administrator password recovery is **not** implemented: it was not part of the approved scope, and it would require a separate reset table and broker. Until then, recovery is a server-side operation (create a new administrator or update the hash with `artisan tinker` under server access).

## 4. Radio stream is configuration, not code

```
config/radio.php  ← RADIO_STREAM_URL, RADIO_STREAM_FORMAT, RADIO_STREAM_NAME
```

`StreamConfig` is a domain value object. `ConfigStreamConfigProvider` is the only adapter that reads `config()`. Changing provider means changing environment variables; no view or controller changes.

- Empty URL ⇒ `Transmisión no configurada`. No `<audio>` source, no dead button, no console error.
- Supported formats: `mp3`, `aac`, `hls`. Unknown values fall back to `mp3` and are reported as configured only when the URL is a valid HTTP(S) URL.
- HLS is played natively (Safari/iOS). Other browsers get an explicit error state. Adding `hls.js` is a pending decision because it is a new runtime dependency and needs a CSP review.
- Laravel never streams audio. No streaming server, no proxy, no fake URL.

## 5. Security baseline

| Control | Where |
|---|---|
| CSRF | Laravel `web` group (`PreventRequestForgery`, formerly `ValidateCsrfToken`), never disabled |
| Password hashing | `LaravelPasswordHasher` → `Hash::make` / `Hash::check` |
| Session fixation | `session()->regenerate()` after listener and admin login and after password change |
| Login throttling | `throttle:login` (5/min per email+IP), `throttle:admin-login` |
| Registration / reset throttling | `throttle:register`, `throttle:password-email`, `throttle:password-reset` |
| Reset link origin | `ForceAppUrl` anchors generated URLs to `APP_URL`; the request `Host` is ignored and a non-HTTPS `APP_URL` fails outside `local`/`testing` |
| Account enumeration | Reset request always answers with the same message; login error is generic |
| Mass assignment | Explicit `#[Fillable]` attributes on both models |
| Security headers | `SecurityHeaders` middleware (`nosniff`, `DENY`, referrer policy, permissions policy) |
| CSP | Sent only outside `local`/`testing`, where Vite's dev client would be blocked |
| Open redirects | Only `redirect()->intended()` with framework-internal URLs |

URL generation is anchored to `APP_URL` by `ForceAppUrl`, which runs before the rest of the `web` group. The request `Host` and `X-Forwarded-*` headers are not used to build absolute URLs, so a manipulated `Host` cannot change the password reset link. Outside `local` and `testing`, `APP_URL` must use `https://`; an invalid value or an HTTP value stops the request with a `RuntimeException` instead of silently producing an insecure link. The reverse proxy only needs to forward the request; the public origin belongs in `APP_URL`.

Email verification stays prepared (`email_verified_at` column) but is not enforced: SMTP is not confirmed and blocking a listener who only wants to hear the radio adds no value.

## 6. Database

`users`, `password_reset_tokens` and `sessions` come from the skeleton and are used by the current configuration. `cache`, `cache_locks`, `jobs`, `job_batches` and `failed_jobs` are kept because `.env` selects the database cache and queue drivers.

Added: `admin_users`. Not added: `admin_password_reset_tokens` (no approved flow), schedules, programmes, hosts, news, podcasts (no approved content or requirements).

## 7. Testing strategy

- **Unit** (`tests/Unit/Domain`, `tests/Unit/Application`): value objects and use cases with in-memory repositories. No framework boot.
- **Feature** (`tests/Feature`): HTTP boundaries, guards, validation, throttling, password reset, player states.
- **Structural security test**: asserts the `web` group still contains a `PreventRequestForgery` instance (the Laravel 13 name; `ValidateCsrfToken` remains a deprecated alias). Laravel skips CSRF enforcement while `APP_ENV=testing`, so the 419 path is additionally verified against a real server during the stage report instead of pretending the suite covers it.

## 8. Pending decisions

1. `hls.js` for HLS in non-Safari browsers.
2. Administrator password recovery flow.
3. Email verification enforcement once SMTP is verified.
4. Spanish validation messages for every rule (v1 ships targeted messages on the used forms).
5. Frontend lockfile: the repository ships `pnpm-lock.yaml`; pnpm is used through `corepack`. `pnpm audit` and `pnpm run build` are the supported commands. Do not generate a second lockfile.
6. `laravel/boost` installation: the original stub asked for it, but it was not authorized in this stage. `AGENTS.md` now documents the project instead of the stub.
