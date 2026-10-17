# LuxSecure

**Developed by Nexora Labs**

Luxury property listing and management platform for Pakistan — verified homes, RBAC admin, bookings/tenancies, payments, KYC, reviews, Sanctum API, and Estate Noir UI.

## Product ownership

LuxSecure is an **in-house product** of **Nexora Labs**. Features and architecture are maintained for internal use, portfolio demos, and future client deployments under Nexora Labs.

## Tech stack

- **Backend:** Laravel 10.x, PHP 8.1+, Sanctum 3.x, Spatie Permission + Activity Log
- **Frontend:** Blade, Tailwind CSS 4 via Vite (`@tailwindcss/vite`), Alpine.js — **no Bootstrap, no Tailwind CDN**
- **Database:** MySQL (primary); SQLite for tests
- **Media:** Intervention Image; uploads via configurable `UPLOADS_DISK` / `PRIVATE_DISK`
- **Payments PDF:** barryvdh/laravel-dompdf
- **API docs:** Scribe (`/docs`)
- **Errors (optional):** Sentry (`SENTRY_LARAVEL_DSN` empty = off)
- **Quality:** PHPUnit 10, Laravel Pint

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
| KYC | Private-disk docs (CNIC/license/registry); admin review; verified badges; unverified agents cannot publish |
| Notifications | Database + mail (booking, rent due daily, lead assigned, ticket reply, KYC); in-app bell; SMS/WhatsApp `MessageChannel` with log driver |
| Phone OTP | Throttled send/verify; hashed codes; expiry |
| CMS | Blog, FAQ, testimonials, pages |
| Agents / leads / tickets | Profiles, lead conversion, support tickets |
| Settings | Site name/email/phone (footer + branding), commerce/policies |
| API v1 | Sanctum token auth, listings/favorites, Scribe OpenAPI |
| Ops | Listing cache, `luxsecure:backup`, optional Sentry |

## Design system — Estate Noir

| Token | Role |
|-------|------|
| `fs-primary` | Ink navy |
| `fs-brand` | Brass accents |
| `fs-surface` | Stone background |

Fonts: Cormorant Garamond (display) + DM Sans (UI). Shared `x-*` Blade components.

## Requirements

- PHP 8.1+, Composer, Node.js & NPM, MySQL (or SQLite for local/tests)

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

Default seeded admin (see seeder): check `DatabaseSeeder` / README seed output — typically `admin@luxsecure.com` / `password`.

### Important env keys

| Key | Purpose |
|-----|---------|
| `UPLOADS_DISK` | Public media disk (`public` or `s3`) |
| `PRIVATE_DISK` | KYC/proofs/invoices (`local` or private S3) |
| `JAZZCASH_*` / `EASYPAISA_*` | Sandbox payment gateways |
| `SENTRY_LARAVEL_DSN` | Leave empty to disable Sentry |
| `LISTING_CACHE_*` | Listing query cache |
| `BACKUP_*` | Scheduled DB backup |

### Scheduled commands

```bash
php artisan schedule:work
# luxsecure:backup @ 02:00
# luxsecure:notify-rent-due @ 08:00
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

*LuxSecure — Developed by Nexora Labs*
