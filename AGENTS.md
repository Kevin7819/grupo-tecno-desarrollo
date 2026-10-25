# Project instructions — Grupo Tecno Desarrollo

Internet radio site built on Laravel 13. Public site + configurable player + listener accounts + a separate administrator panel.

## Commands

This machine has PHP 8.4 outside `PATH`. Always use it explicitly:

```powershell
C:\php84\php.exe artisan test
C:\php84\php.exe artisan serve
C:\php84\php.exe artisan migrate
C:\php84\php.exe artisan radio:create-admin
C:\php84\php.exe vendor\bin\pint --test
C:\php84\php.exe C:\composer\composer.phar install
C:\php84\php.exe C:\composer\composer.phar audit --locked
corepack pnpm install --frozen-lockfile
corepack pnpm run build
corepack pnpm audit
```

Do not use the XAMPP PHP 8.2 binary for this project.

## Architecture

```
app/Domain/          Framework-free values and rules (no Illuminate imports)
app/Application/     Use cases, DTOs, repository/hasher contracts
app/Infrastructure/  Eloquent repositories, config adapter, Laravel hasher
app/Http/            Thin controllers, form requests, middleware
```

- Dependencies point inward. Domain and Application must not import HTTP, Eloquent, session or database classes.
- Do not add a repository per model, generic base repositories or decorative interfaces. Add a contract only when a use case needs to cross a boundary.
- Business rules live in use cases; controllers validate, delegate and build the response.
- Hashing is an application-layer decision (`PasswordHasher`). Do not add a `hashed` cast that re-hashes an already hashed value.

## Authentication and security invariants

- Listeners: guard `web`, `User`, `users`, login `/login`.
- Administrators: guard `admin`, `AdminUser`, `admin_users`, login `/admin/login`, middleware `admin.auth`.
- No public administrator registration. Provision with `radio:create-admin`.
- Never disable CSRF. Laravel skips it in `APP_ENV=testing`; do not "fix" tests by removing middleware.
- Keep rate limiting on login, admin login, registration and password reset.
- Regenerate the session after login and after a password change.
- Validate on the server; authorize on the server; never trust hidden fields.
- Do not log passwords, tokens or secrets. Do not commit `.env`.
- Password reset responses must not reveal whether an email exists.
- `.env` and `.env.example` are protected by the local agent policy; document new variables in `README.md` instead of editing them blindly.

## Testing

- Run `C:\php84\php.exe artisan test` before declaring work done.
- Feature tests use SQLite in memory and `withoutVite()`.
- Unit tests for domain/use cases use in-memory fakes in `tests/Support`.
- Add a test for every security boundary you touch: guest rejection, listener-vs-admin separation, validation, isolation between accounts, token reuse, rate limiting.
- CSRF is verified structurally in the suite and against a real local server (419 without token).

## Frontend

- Palette tokens live in `resources/css/app.css` under `@theme`. Do not replace them with Tailwind approximations.
- Dark UI. Orange `#FF5528` is for primary actions, active controls and the live indicator; use dark text on orange.
- Keep visible labels, keyboard focus, non-color state cues, `aria-live` for player status and `prefers-reduced-motion`.
- `resources/js/app.js` imports `player.js`; both are bundled by Vite.
- The stream URL is configuration (`config/radio.php`). Never invent a URL or serve audio from Laravel.

## Content policy

Do not invent the station slogan, biography, schedule, hosts, programmes or contact details. Use neutral Spanish placeholders until the station approves real content. The name «Grupo Tecno Desarrollo» is provisional.

## Documentation

- `docs/architecture.md` — layering, guards, stream, security decisions, pending items.
- `docs/deployment-namecheap.md` — production checklist; not deployed.
- `odd/tasks/radio-v1.md` — feature specs, tasks and evidence log.
