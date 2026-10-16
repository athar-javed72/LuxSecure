# Project Requirements Document (PRD): LuxSecure UI Redesign & Design System

## Overview

LuxSecure is a Nexora Labs in-house Laravel property listing platform (auth, favorites, contact inquiries, admin CRUD, Sanctum API). GitHub baseline: [athar-javed72/LuxSecure](https://github.com/athar-javed72/LuxSecure) (`origin/main` @ `143159f`, Feb 2026).

This PRD covers the **UI redesign phase** already largely executed in the local workspace: replace CDN/Bootstrap-era UI with a Tailwind 4 + Alpine design system, restore LuxSecure branding, improve auth UX, and harden layout consistency — **without** rewriting core business logic or routes.

## Stack and environment (fixed)

- Laravel 10.x, PHP 8.1+, Sanctum, PHPUnit, Pint
- Frontend: Blade, Tailwind CSS 4 (`@tailwindcss/vite`), Alpine.js, Vite 7, Lottie (auth panels)
- Database: MySQL (local); `luxsecure_db.sql` / seeders for demo data
- Layout: `x-container` max-w-[1440px]; desktop target ~1920×950; responsive to 320px
- Remote: `origin` → `https://github.com/athar-javed72/LuxSecure.git`
- Credentials from `.env` only; never commit secrets

## Hard rules (apply to every task)

1. Prefer preserving routes, controllers, models, middleware, and API contracts. UI-first changes unless a task says otherwise.
2. No Bootstrap. No Tailwind CDN. Use Vite-built CSS and shared `x-*` components.
3. Use design tokens / brand greens already in `resources/css/app.css` (`fs-*`). Avoid generic purple/cream AI aesthetic biases.
4. One task per turn when executing from this PRD. Verify, append `progress.txt` (append only), then STOP.
5. Verification gate: `php -l` on changed PHP · `php artisan test` · `npm run build` when assets/views/CSS/JS changed.
6. Never `git add` / `commit` / `push` unless the user explicitly asks.
7. Update `.planning/STATE.md` at phase handoff; keep `AGENTS.md` accurate if workflow changes.

## Tasks

### A — Design system & public shell (DONE)

- [x] Task A1: Introduce design tokens and shared layout (`x-container`, CSS theme).
- [x] Task A2: Redesign public pages (home, properties, property detail, contact, profile, errors) with LuxSecure branding.
- [x] Task A3: Build reusable components (`x-button`, `x-property-card`, `x-section-heading`, `x-input`, `x-select`, `x-badge`, cards for agents/blog/testimonials/FAQ).
- [x] Task A4: Dark LuxSecure navbar (Home / Properties / Why LuxSecure / Contact) + responsive mobile nav.
- [x] Task A5: Remove Bootstrap usage from views; wire Tailwind 4 + Alpine via Vite.

### B — Auth experience (DONE)

- [x] Task B1: Split auth layout with Lottie panels (login / register / forgot).
- [x] Task B2: `x-auth-card` mode switch (login ↔ register ↔ forgot) with `history.replaceState` — no full reload between modes.
- [x] Task B3: Keep password reset / verify-email flows working under the new shell.

### C — Admin panel (DONE)

- [x] Task C1: Redesign admin layout, dashboard, property CRUD views, users, inquiries tables/forms to match the design system.

### D — Content & data (DONE)

- [x] Task D1: Update seeders for distinct property images (e.g. Unsplash HTTP URLs where used).
- [x] Task D2: Keep demo SQL / seed path working for local setup.

### E — Footer & polish (DONE / in progress)

- [x] Task E1: Rewrite footer — even columns; Explore (no redundant Home); auth-aware Account; real Contact block (no fake newsletter); copyright + Nexora Labs.
- [ ] Task E2: Visual pass on 1920 desktop + 320 mobile for footer and auth pages after hard refresh; fix any residual spacing/contrast issues found.

### F — Repo hygiene & handoff (OPEN)

- [ ] Task F1: Align `README.md` tech stack section with Tailwind 4 + Alpine + Vite (remove “Tailwind CDN” wording).
- [ ] Task F2: Run full gate (`php artisan test`, `npm run build`) and record results in `progress.txt`.
- [ ] Task F3: First git commit of the redesign (only when user asks) and optional push to `origin/main` (only when user asks).
- [ ] Task F4: Archive this PRD copy under `docs/archive/PRD_ui_redesign.md` after F2–F3; refresh `.planning/STATE.md` known gaps.

## Out of scope for this PRD

- New domains (agents CMS, blog CMS, payments, multi-tenant orgs)
- Laravel version upgrade (10 → 11/12)
- Replacing PHPUnit with Pest
- Mobile native apps
- Changing public API response shapes unless required by a bug fix

## Success criteria

- Public + admin UI consistent with LuxSecure brand and design tokens
- Auth mode switching feels seamless; Lottie panels present
- Footer content appropriate (no fake newsletter); responsive
- Tests and production build green
- GitHub remote attached; commit/push only on explicit user request
