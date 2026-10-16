# LuxSecure

**Developed by Nexora Labs**

Luxury property listing and management platform for Pakistan — verified homes, secure auth, favorites, contact inquiries, admin CRUD, and a Sanctum REST API.

## Product ownership

LuxSecure is an **in-house product** of **Nexora Labs**. Features and architecture are maintained for internal use, portfolio demos, and future client deployments under Nexora Labs.

## Current status (Oct 2026)

| Area | Status |
|------|--------|
| Public UI redesign (Estate Noir) | Done |
| Auth (split panel + Lottie + mode switch) | Done |
| Admin panel UX + filters + confirm modal | Done |
| Design system (`fs-*` tokens, `x-*` components) | Done |
| Bootstrap removed | Done |
| PHPUnit suite (incl. admin) | Passing (18 tests) |
| Production Vite build | Passing |
| Git remote | `origin` → [athar-javed72/LuxSecure](https://github.com/athar-javed72/LuxSecure) |
| Push to GitHub | Local `main` may be **ahead** of `origin/main` — push when ready |

Planning / agent docs (optional): `PRD.md`, `progress.txt`, `.planning/STATE.md`, `AGENTS.md`.

## Features

### Public site
- Home: hero search, property types, featured listings, about, agents, testimonials, blog teaser, FAQ, CTA
- Properties: search (title/location), type & price filters, pagination, detail page + inquiry
- Contact form (stored in DB)
- Auth: register, login, logout, password reset, email verification
- Smooth login ↔ register ↔ forgot switch (`x-auth-card`) without full reload; Lottie panels on desktop
- Profile: edit details, saved favorites
- Responsive layout (`x-container`, target ~1920 desktop down to 320px)
- Custom error pages (404 / 500 / 503)

### Admin (`/admin`)
- Role-gated (`auth` + `admin` middleware)
- Dashboard with live DB stats + recent properties/inquiries
- Properties CRUD, image uploads (jpg/jpeg/png/webp), search + type/status filters
- Users: search, edit role, delete (cannot delete own account)
- Inquiries: search, read/unread filter, mark as read
- Styled delete confirmation (`x-confirm-delete`), breadcrumbs, mobile sidebar

### API
- `GET /api/properties` — filters + pagination
- Sanctum: `GET /api/user`, `GET /api/properties/favorites`

## Design system — Estate Noir

Luxury property look (not a green brand wash):

| Token | Role | Example |
|-------|------|---------|
| `fs-primary` | Ink navy (nav, footer, structure) | `#0b1426` |
| `fs-brand` | Brass CTAs / accents | `#9a7b4f` |
| `fs-brand-light` | Champagne washes | `#f0e6d4` |
| `fs-surface` | Stone page background | `#f5f4f1` |

- **Fonts:** Cormorant Garamond (display) + DM Sans (UI)
- **Components:** `x-container`, `x-button`, `x-property-card`, `x-auth-card`, `x-badge`, `x-input`, `x-select`, `x-breadcrumbs`, `x-confirm-delete`, and more under `resources/views/components/`

## Tech stack

- **Backend:** Laravel 10.x, PHP 8.1+, Sanctum 3.x
- **Frontend:** Blade, Tailwind CSS 4 (`@tailwindcss/vite`), Alpine.js, Vite 7, Lottie (auth)
- **Database:** MySQL (primary); PostgreSQL/SQLite supported by Laravel
- **Quality:** PHPUnit 10, Laravel Pint, Tailwind pagination
- **Media helper:** `App\Support\Media` resolves storage paths and absolute http(s) image URLs

## Requirements

- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL (recommended) or another Laravel-supported DB

## Installation

1. **Clone and install**
   ```bash
   git clone https://github.com/athar-javed72/LuxSecure.git
   cd LuxSecure
   composer install
   npm install
   ```

2. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Set `APP_URL`, `DB_*`, and optionally `MAIL_*` in `.env`.

3. **Database and storage**
   ```bash
   php artisan migrate
   php artisan storage:link
   php artisan db:seed
   ```
   Seeder creates admin `admin@luxsecure.com` / `password` and sample properties.  
   Optional: import `luxsecure_db.sql` if you use the bundled dump.

4. **Assets**
   ```bash
   npm run dev      # development (Vite HMR)
   # or
   npm run build    # production assets into public/build
   ```

5. **Run**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000`. Admin: `/admin`.

## Usage

| Path | Purpose |
|------|---------|
| `/` | Home + search |
| `/properties` | Browse / filter |
| `/properties/{id}` | Detail + inquiry |
| `/contact` | Contact form |
| `/login`, `/register` | Auth (shared card UX) |
| `/profile` | Account + favorites |
| `/admin` | Admin dashboard (admin role only) |

## API

- `GET /api/properties` — Query: `search`, `type`, `price_min`, `price_max`, `per_page` (max 50)
- `GET /api/user` — Bearer token (Sanctum)
- `GET /api/properties/favorites` — Authenticated favorites (Sanctum)

## Testing

```bash
php artisan test
npm run build
```

Feature coverage includes auth, contact, property listing, property API, and admin panel (guest redirect, 403, dashboard, property CRUD, mark inquiry read, cannot delete self).

## Deployment

- Document root → `public/`
- `php artisan migrate --force`
- `php artisan storage:link`
- `npm run build`
- `php artisan config:cache`
- `APP_ENV=production`, `APP_DEBUG=false`, strong `APP_KEY`
- Queue worker if using queued mail

## License

MIT.

---

*LuxSecure — Developed by Nexora Labs*
