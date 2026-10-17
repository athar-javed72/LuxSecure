# STATE — Maskanoor

Maskanoor rebrand + multi-country reference data — **COMPLETE locally**

---

## 1. What Exists

### Product
- **Maskanoor** — multi-country property listing & management (Nexora Labs). Brand from Settings (`site.name`, `site.tagline.{locale}`, `site.logo`).
- Public: home, properties, contact, auth, profile, bookings/tenancies, reviews, KYC, phone OTP, notifications bell; RTL for ur/ar.
- Admin (`/admin`): listings, payments, reviews, KYC, CMS, settings, countries (+ tax needs-review badge), cancellation policies (`applies_to` / `reference_date`), orgs, activity, RBAC.
- Reference data (all envs): PK/SA/AE/QA/MY/GB, currencies, languages, cities/areas, KYC types, payment methods, cancellation policies, commission defaults.
- Demo (local/testing only): admin user, listings, CMS drafts, **Maskanoor Stays** org + 3 `[DEMO]` branches.
- Payments: polymorphic ledger + RefundCalculator (purpose-aware policy resolution).
- API: Sanctum + Scribe; ops: `luxsecure:*` artisan commands (namespace unchanged).

### Stack
- Laravel 13, PHP 8.4.1–8.5, Sanctum, Spatie Permission + Activity Log, PHPUnit, Pint
- Blade + Tailwind CSS 4 (Vite) + **@alpinejs/csp** — Estate Noir theme

### Git
- `origin` → `https://github.com/athar-javed72/LuxSecure.git`
- Maskanoor changes uncommitted unless user asks to commit

---

## 2. Metrics

| Area | Status |
|------|--------|
| PHPUnit | **132 passed** |
| `npm run build` | Passing |
| `migrate:fresh --seed` | Passing (reference all envs; demo local/testing) |
| Maskanoor brand from Settings | Done |
| Multi-country reference + purpose policies | Done |
| Pushed to GitHub | No (awaiting user) |

---

## 3. Known gaps

1. Commit / push when user requests.
2. Repo remote / folder still named LuxSecure; artisan prefix still `luxsecure:*`; demo login `admin@luxsecure.com`.
3. Regenerate Scribe HTML if `/docs` should show Maskanoor title from live config.
4. Repo hygiene: tracked secrets history / `luxsecure_db.sql` if still public.
5. CMS legal drafts still `[PLACEHOLDER:*]` until legal review; ar/ms lang files are machine drafts (`needs_native_review`).

---

## 4. Next (optional)

- `git commit` / `git push` when ready
- Native review of ar/ms translations; clear Pakistan `tax_needs_review` in Admin after confirmation
