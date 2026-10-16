# LuxSecure — Laravel 10 + Blade + Tailwind 4 Rules

1. **Stack (fixed):** Laravel 10, PHP 8.1+, MySQL (or SQLite for tests), Blade, Tailwind CSS 4 via Vite (`@tailwindcss/vite`), Alpine.js. Do not reintroduce Bootstrap or Tailwind CDN.
2. **Preserve business logic:** Prefer keeping existing routes, controllers, models, middleware, and API contracts. Redesign is UI/UX-first unless a task explicitly changes backend behaviour.
3. **Design system:** Estate Noir theme — ink navy (`fs-primary`), brass (`fs-brand`), champagne (`fs-brand-light`), stone (`fs-surface`). Use shared Blade components under `resources/views/components/`. Do not reintroduce green brand washes or invent one-off patterns that fight the system.
4. **Layout:** Public pages use `x-container` (max-w-[1440px]). Target desktop ~1920×950; stay fully responsive down to 320px.
5. **Verification gate:** Before marking a task complete: `php -l` on changed PHP files, `php artisan test` (PHPUnit), and `npm run build` when frontend assets changed. Use Pint when formatting PHP (`./vendor/bin/pint --test` if available).
6. **Docs:** Prefer Context7 (or official Laravel 10 docs) before guessing framework APIs.
7. **Secrets:** `.env` is never committed; never print credentials in logs or `progress.txt`.
8. **Git:** Do not `git add` / `commit` / `push` unless the user explicitly asks.
