@AGENTS.md

# Claude Code

The imported file above is the single source of truth for this repository. Read it before changing code.

Key reminders:

- Use `C:\php84\php.exe` explicitly on this machine.
- Run `C:\php84\php.exe artisan test` before reporting work as done.
- Listeners use the `web` guard; administrators use the separate `admin` guard.
- Never add a public administrator registration route or default credentials.
- Do not disable CSRF, and do not commit `.env`.
