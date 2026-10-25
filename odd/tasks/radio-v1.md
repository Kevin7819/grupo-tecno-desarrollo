# Feature: radio-v1 — emisora pública, cuentas de oyentes y administración

Objetivo: primera versión funcional de la emisora (sitio público, reproductor configurable, autenticación de oyentes, autenticación administrativa separada y área personal).
Rama: `feature/radio-v1` (creada desde `main` en `4686561`). Entrega: commits por unidad de trabajo. Runner: `C:\php84\php.exe artisan test` (PHPUnit 12, SQLite en memoria).

## Specs

- **S1 — Paleta.** Tokens exactos: fondo `#0D0E12`, tarjetas `#14171F`, bordes `#232733`, acento `#FF5528`, texto `#F3F4F6`, secundario `#9CA3AF`. Los botones naranjas usan texto oscuro para mantener contraste AA. Los bordes decorativos no son el único indicador de un campo.
- **S2 — Guards.** Oyentes: guard `web`, modelo `User`, tabla `users`. Administradores: guard `admin`, modelo `AdminUser`, tabla `admin_users`. Login administrativo en `/admin/login`, middleware propio `admin.auth`. No existe registro público de administradores.
- **S3 — Aprovisionamiento.** `php artisan radio:create-admin` solicita nombre, correo y contraseña dos veces, no imprime la contraseña, no trunca tablas y no define credenciales por defecto.
- **S4 — Reproductor.** Configuración centralizada con `RADIO_STREAM_URL`, `RADIO_STREAM_FORMAT`, `RADIO_STREAM_NAME`. URL vacía ⇒ estado «Transmisión no configurada», sin controles inútiles ni errores de consola. Laravel no sirve audio.
- **S5 — Oyentes.** Registro, inicio y cierre de sesión, recuperación de contraseña y área personal con nombre, correo y cambio de contraseña.
- **S6 — Seguridad de sesión.** CSRF activo, hash de contraseñas con Laravel, regeneración de sesión al iniciar sesión, validación en servidor, autorización en servidor, sin asignación masiva insegura, sin redirecciones abiertas.
- **S7 — Límites.** Rate limiting en login, login administrativo, registro, solicitud de recuperación y envío de nueva contraseña.
- **S8 — Pruebas obligatorias.** Visitante rechazado en rutas protegidas; oyente rechazado en rutas administrativas; administrador autenticado por `admin` y no por `web`; CSRF verificado; aislamiento entre cuentas; validación de datos; contraseñas nunca en texto plano; token de recuperación no reutilizable.
- **S9 — Base de datos.** Solo tablas con uso definido. Se conservan las migraciones del esqueleto (`users`, `password_reset_tokens`, `sessions`, `cache`, `jobs`) porque la configuración actual las usa. Se agrega `admin_users`; no se agrega tabla de reset administrativo porque ese flujo no está aprobado.
- **S10 — Contenido.** No inventar eslogan, biografía, programación, locutores ni contacto. Textos provisionales neutros en español. El nombre «Grupo Tecno Desarrollo» se usa como nombre provisional.
- **S11 — Accesibilidad.** Etiquetas visibles, foco de teclado visible, estados que no dependen solo del color, `aria-live` para el estado del reproductor, `prefers-reduced-motion`.
- **S12 — Capas.** `app/Domain` sin dependencias del framework; `app/Application` con casos de uso y contratos; `app/Infrastructure` con adaptadores Eloquent/config; `app/Http` delgado. Sin repositorios genéricos ni interfaces decorativas.
- **S13 — Calidad.** Suite existente conservada, pruebas nuevas, build de producción de Vite, `composer audit` y auditoría de dependencias frontend.
- **S14 — Documentación.** README con ejecución local y `docs/deployment-namecheap.md` con requisitos a confirmar antes de desplegar.

## Tasks

| ID | Specs | Route | Estado | Evidencia |
|---|---|---|---|---|
| T1 | S12, S14 | inline | Completado | `docs/architecture.md`; capas `Domain`/`Application`/`Infrastructure`/`Http`; `pint --test` limpio |
| T2 | S2, S5, S6, S7, S8, S9 | inline | Completado | `routes/web.php`, controladores `Auth`/`Profile`, `tests/Feature/Auth`, `tests/Feature/ProfileTest.php` |
| T3 | S2, S3, S8, S9 | inline | Completado | `config/auth.php` (guard `admin`), `admin.auth`, `radio:create-admin`, `tests/Feature/Admin` |
| T4 | S1, S4, S10, S11 | inline | Completado | `resources/css/app.css`, `resources/js/player.js`, `partials/player.blade.php`, `tests/Feature/PublicSiteTest.php`, `tests/Feature/DesignTokensTest.php` |
| T5 | S13 | inline | Completado | 82 tests / 307 assertions; `composer audit --locked` y `pnpm audit` sin avisos; build de Vite exitoso; CSRF real 419 |
| T6 | S14 | inline | Completado | `README.md`, `docs/deployment-namecheap.md`, `AGENTS.md`, `CLAUDE.md` |

## Log

- **L1 (2026-10-24).** Pedido del usuario: iniciar la implementación real de la emisora sobre el esqueleto Laravel 13 existente, con Clean Architecture pragmática, seguridad desde el inicio, paleta aprobada, reproductor configurable, autenticación de oyentes y administradores separada, pruebas y auditorías, y documentación de despliegue para Namecheap. Texto completo archivado en Engram (`odd/radio-v1/tasks`).
- **L2.** `AGENTS.md` y `CLAUDE.md` son idénticos y contienen únicamente el stub de Laravel Boost, que ordena `composer require laravel/boost --dev` y `php artisan boost:install`. Conflicto resuelto: no se instala Boost en esta etapa porque agrega una dependencia versionada no solicitada y reescribe las instrucciones; se registra como decisión pendiente y los stubs se reemplazan por guías reales del proyecto al cierre.
- **L3.** Remoto `origin` agregado (`https://github.com/Kevin7819/grupo-tecno-desarrollo.git`) y `main` publicado en `46865619ec40b7f169a465f65fa09051511f08dd`. El primer intento quedó esperando autenticación interactiva; el segundo usó la credencial ya registrada y terminó con `* [new branch] main -> main`.
- **L4.** Decisión: no se crea `admin_password_reset_tokens` ni broker administrativo porque la recuperación de administradores no está aprobada; el restablecimiento administrativo queda como operación de servidor documentada.
- **L5.** Decisión: HLS no incorpora `hls.js` en v1. Se usa reproducción nativa y, si el navegador no soporta HLS, el reproductor muestra un estado de error explícito. Agregar la dependencia queda como decisión pendiente.
- **L6 (2026-10-24).** Incidente: un reemplazo masivo con PowerShell iteró los caracteres de cada cadena en lugar de pares y corrompió `routes/web.php` (`R`→`o`), `RegisteredUserController.php` (`A`→`u`) y `AuthenticatedSessionController.php` (`i`→`f`). Se reescribieron los tres archivos completos desde su contenido correcto y se verificó con `php -l`; un escaneo de patrones residuales (`ooute`, `upp\`, `Vfew`, `fnnal`, `sessfon`) no encontró otros daños. Lección: no usar `String.Replace` masivo sobre código; editar con herramientas de edición estructuradas.
- **L7 (2026-10-24).** Endurecimiento de guard explícito: `Auth::guard('web')->attempt(...)`, `Auth::guard('web')->loginUsingId(...)`, `Route::middleware('auth:web')`, `Route::middleware('guest:web')`, `$request->user('web')`, `$this->user('web')` en los FormRequest de perfil, y `@auth('web')`/`@guest('web')` en las vistas. El guard administrativo ya usaba `Auth::guard('admin')` y `admin.auth`.
- **L8 (2026-10-24).** Evidencia de verificación final: `artisan test` → 82 tests, 307 assertions, 0 fallos; `vendor/bin/pint --test` → `passed`; `composer audit --locked` → sin avisos; `corepack pnpm audit` → sin vulnerabilidades conocidas; `corepack pnpm run build` → manifest y assets generados en `public/build`; `artisan view:cache` → plantillas compiladas; `route:list` → 17 rutas. CSRF contra servidor real en `127.0.0.1:8123`: `GET /login` 200, `POST /login` sin `_token` → 419, con `_token` → 200 con mensaje genérico, `POST /logout` sin `_token` → 419. Servidor detenido y puerto liberado.
- **L9 (2026-10-24).** Bloqueo de entorno: la política local de permisos deniega editar `.env` y `.env.example`. Las variables `APP_NAME`, `RADIO_STREAM_URL`, `RADIO_STREAM_FORMAT` y `RADIO_STREAM_NAME` quedaron documentadas en `README.md` y `docs/deployment-namecheap.md`; el usuario debe agregarlas manualmente. `APP_NAME` sigue siendo «Laravel» en el `.env` local, pero las vistas usan el nombre de la emisora.
- **L10 (2026-10-24).** Decisión de lockfile frontend: el repositorio trae `pnpm-lock.yaml`, así que se usa pnpm vía `corepack` (`pnpm install --frozen-lockfile`, `pnpm audit`, `pnpm run build`). No se genera un segundo lockfile. `node_modules` contenía binarios nativos de Linux; se resolvió reinstalando con pnpm, que descargó los binarios de Windows.
- **L11 (2026-10-24).** Commits de la feature en `feature/radio-v1`: `5b2a4d9` núcleo (dominio, aplicación, infraestructura, guard admin y comando), `e2b614e` cuentas de oyentes y perfil, `c3ec9db` panel administrativo aislado y headers de seguridad, `b777c5f` sitio público, reproductor y tokens. El commit de documentación cierra la unidad. Sin push: queda a decisión del usuario.
