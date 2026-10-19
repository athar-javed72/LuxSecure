# Maskanoor Group

**Developed by Nexora Labs**

Multi-country property listing and management platform — verified homes, RBAC admin, bookings/tenancies, payments, KYC, reviews, Sanctum API, and Estate Noir UI. Brand name, tagline, logo, and contact details are Admin-editable Settings (`site.name`, `site.tagline.{locale}`, `site.logo`).

## Product ownership

Maskanoor is an **in-house product** of **Nexora Labs**. Features and architecture are maintained for internal use, portfolio demos, and future client deployments under Nexora Labs.

## Tech stack

- **Backend:** Laravel 13.x, PHP 8.4.1–8.5, Sanctum 4.x, Spatie Permission + Activity Log
- **Frontend:** Blade, Tailwind CSS 4 via Vite (`@tailwindcss/vite`), Alpine.js — **no Bootstrap, no Tailwind CDN**
- **Database:** MySQL (primary); SQLite for tests
- **Media:** Intervention Image; uploads via configurable `UPLOADS_DISK` / `PRIVATE_DISK`
- **Payments PDF:** barryvdh/laravel-dompdf
- **API docs:** Scribe (`/docs`)
- **Errors (optional):** Sentry (`SENTRY_LARAVEL_DSN` empty = off)
- **Quality:** PHPUnit 12, Laravel Pint

## Roles (Spatie RBAC)

| Role | Access |
|------|--------|
| `super_admin` | Full (`*`) |
| `admin` | Panel + listings, users, payments, reviews, KYC, CMS, settings, reports |
| `agent` | Own listings/leads/tickets, KYC submit |
| `support` | Inquiries, leads, tickets |
| `landlord` | Own listings/tenancies/payments, KYC |
| `hotel_manager` | Own short-stay listings/bookings/payments, KYC |
| `client` | Own bookings/tenancies/payments/reviews, KYC submit |

Permission catalog lives in `config/rbac.php` and is seeded by `RolesAndPermissionsSeeder`.

## Modules

| Module | Highlights |
|--------|------------|
| Listings | Types, attributes, gallery, floor plan, video URL, private docs, cache |
| Auth | Register/login, email verify, password reset, optional 2FA (admin) |
| Bookings | Short-stay rooms, seasonal pricing, availability, confirmation mail |
| Tenancies | Rent schedule, overdue list, mark paid |
| Payments | Polymorphic payments, bank transfer proof + approval, JazzCash/Easypaisa sandbox webhooks (signed, idempotent), invoice PDF, admin filters + CSV |
| Reviews | Completed booking/tenancy only; admin moderation; averages on listing/agent |
| KYC | Country-specific document types; admin review; verified badges |
| Notifications | Database + mail; in-app bell; SMS/WhatsApp `MessageChannel` with log driver |
| Phone OTP | Throttled send/verify; hashed codes; expiry |
| CMS | Blog, FAQ, testimonials, pages |
| Agents / leads / tickets | Profiles, lead conversion, support tickets |
| Settings | Brand, contact, commerce commission/tax (global + per country) |
| Multi-country | PK, SA, AE, QA, MY, GB reference data; currencies, locales, cities, KYC, payments |
| API v1 | Sanctum token auth, listings/favorites, Scribe OpenAPI |
| Ops | Listing cache, `luxsecure:backup`, optional Sentry |

## Design system — Estate Noir

| Token | Role |
|-------|------|
| `fs-primary` | Ink navy (`#0b1426`) |
| `fs-brand` | Brass accents (`#9a7b4f`) |
| `fs-surface` | Stone background |

Fonts: Cormorant Garamond (display) + DM Sans (UI). Shared `x-*` Blade components. Approved brand SVGs live under `public/brand/` (copied from `brand-source/maskanoor-brand/`; do not regenerate or edit path data). Use `<x-brand-logo>` / `App\Support\BrandAssets`.

## Requirements

- **PHP 8.4.1–8.5** (Composer lock / Symfony 8 require `>= 8.4.1`; PHP 8.2 will fatal on `artisan`)
- Composer, Node.js & NPM, MySQL (or SQLite for local/tests)

### Windows: wrong `php` on PATH

If `php -v` shows **8.2.x**, artisan fails with `Composer dependencies require a PHP version ">= 8.4.1"`. This repo expects PHP at:

`C:\php\php-8.5.11-nts-Win32-vs17-x64`

In the **current** PowerShell session:

```powershell
. .\scripts\use-php85.ps1
php -v   # must be 8.5.x
```

Cursor/VS Code terminals pick this up via `.vscode/settings.json` after you **open a new terminal**. Put that folder **first** on your system PATH (before Laragon/XAMPP PHP 8.2) so external terminals work too.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Set DB_*, APP_URL, QUEUE_CONNECTION=database (or sync for local)
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

**Seeding safety:** `migrate:fresh --seed` always installs roles/permissions and `ReferenceDataSeeder` (countries, languages, currencies, KYC types, payment methods, cancellation policies, brand settings). Demo accounts, demo listings, hotel inventory, CMS drafts, and the **Maskanoor Stays** demo organization seed **only when `APP_ENV` is `local` or `testing`**. In production, create the first super admin with:

```bash
php artisan luxsecure:create-super-admin
```

Local demo admin after seed: `admin@luxsecure.com` / `password` (change immediately).

### Important env keys

| Key | Purpose |
|-----|---------|
| `UPLOADS_DISK` | Public media disk (`public` or `s3`) |
| `PRIVATE_DISK` | KYC/proofs/invoices (`local` or private S3) |
| `JAZZCASH_*` / `EASYPAISA_*` | Sandbox payment gateways |
| `SENTRY_LARAVEL_DSN` | Leave empty to disable Sentry |
| `LISTING_CACHE_*` | Listing query cache |
| `BACKUP_*` | Scheduled DB backup |
| `CACHE_PREFIX` / `SESSION_COOKIE` / `REDIS_PREFIX` | Pin cache/session key names across Laravel upgrades |
| `TRUSTED_PROXIES` | Comma-separated proxy IPs/CIDRs; empty = trust none |

### Scheduled commands

```bash
php artisan schedule:work
# luxsecure:backup @ 02:00
# luxsecure:notify-rent-due @ 08:00
# luxsecure:expire-booking-holds every 5 minutes
# luxsecure:create-super-admin — first production super_admin (interactive)
```

## Usage

| Path | Purpose |
|------|---------|
| `/` | Home |
| `/properties` | Browse / filter |
| `/properties/{listing}` | Detail, reviews, inquiry |
| `/bookings/*`, `/tenancies/*` | Client bookings & rent |
| `/payments/initiate` | Start gateway payment |
| `/kyc`, `/phone/verify`, `/notifications` | KYC, OTP, bell |
| `/admin` | RBAC panel (payments, reviews, KYC, …) |
| `/docs` | Scribe API docs |
| `/webhooks/payments/{jazzcash\|easypaisa}` | Signed payment webhooks |

## API

- Legacy: `GET /api/properties`, Sanctum favorites
- v1: `/api/v1/*` (see Scribe at `/docs`)

## Testing & quality

```bash
vendor/bin/pint
php artisan test
npm run build
php artisan migrate:fresh --seed
```

## License

MIT.

---

*Developed by Nexora Labs*
