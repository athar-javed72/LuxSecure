# STATE — Maskanoor

Brand SVG kit + Critical multi-country remediations — **COMPLETE locally**

---

## 1. What Exists

### Product
- **Maskanoor** — multi-country property listing & management (Nexora Labs). Brand from Settings (`site.name`, `site.tagline.{locale}`, `site.logo`).
- Public: home, properties, contact, auth, profile, bookings/tenancies, reviews, KYC, phone OTP, notifications bell; RTL for ur/ar.
- Admin (`/admin`): listings, payments, reviews, KYC, CMS, settings, countries (+ tax needs-review badge), cancellation policies (`applies_to` / `reference_date`), orgs, activity, RBAC — **country-scoped for country_admin**.
- Reference data (all envs): PK/SA/AE/QA/MY/GB, currencies, languages, cities/areas, KYC types, payment methods, cancellation policies, commission defaults.
- Demo (local/testing only): admin user, listings, CMS drafts, **Maskanoor Stays** org + 3 `[DEMO]` branches.
- Payments: polymorphic ledger + RefundCalculator (purpose-aware, currency decimals).
- API: Sanctum + Scribe + legal acceptance middleware; ops: `luxsecure:*` artisan commands.

### Security / RBAC (audit Critical)
- `CountryAccess` + `CountryScoped` + policy `ChecksCountryScope` (deny-by-default for country-scoped roles).
- Admin list/export/dashboard queries constrained by user country.
- `hotel_manager` owns `manager_id` listings only; `organization_admin` owns org listings + `rooms.*.own`.
- Locale switcher cannot rewrite `country_admin.country_id`; panel context locked.
- Legal acceptance enforced on web + API JSON.
- KYC document types validated against country DB types.
- Invoice tax/commission from payment country settings; backups include private disks; `luxsecure_db.zip` untracked.

### Stack
- Laravel 13, PHP 8.4.1–8.5, Sanctum, Spatie Permission + Activity Log, PHPUnit (sqlite `:memory:`), Pint
- Blade + Tailwind CSS 4 (Vite) + **@alpinejs/csp** — Estate Noir theme

### Git
- `origin` → `https://github.com/athar-javed72/LuxSecure.git`
- Remediations uncommitted unless user asks to commit

---

## 2. Metrics

| Area | Status |
|------|--------|
| PHPUnit | **150 passed** |
| `npm run build` | Passing |
| Country/City geo (import, visibility, location-picker) | Done |
| Country scoping remediation | Done |
| Brand SVG kit (`public/brand/` = byte-copy of `brand-source/`) | Done |
| Logo regenerator removed | Done |
| Pushed to GitHub | No (awaiting user) |

---

## 3. Known gaps

1. Commit / push when user requests.
2. Repo remote / folder still named LuxSecure; artisan prefix still `luxsecure:*`; demo login `admin@luxsecure.com`.
3. Regenerate Scribe HTML if `/docs` should show Maskanoor title from live config.
4. CMS legal drafts still `[PLACEHOLDER:*]` until legal review; ar/ms lang files are machine drafts (`needs_native_review`).
5. After deploy: re-seed RBAC (`RolesAndPermissionsSeeder`) for `countries.manage_visibility` + prior room grants.
6. Download dr5hn geo JSON into `storage/app/geo/` (not in git) before `php artisan maskanoor:geo:import`.
7. Publish privacy/terms CMS pages (or global fallbacks) before enabling additional countries via Admin → Countries → Visibility.

---

## 4. Next (optional)

- `git commit` / `git push` when ready
- Run geo import for PK/AE (or full set) after placing dataset files
- Native review of ar/ms translations; clear Pakistan `tax_needs_review` in Admin after confirmation
