# STATE — LuxSecure

Production hardening (ledger / holds / CSP / Sanctum / SMS) — **COMPLETE locally**

---

## 1. What Exists

### Product
- **LuxSecure** — property listing & management (Nexora Labs).
- Public: home, properties, contact, auth, profile, bookings/tenancies, reviews, KYC, phone OTP, notifications bell.
- Admin (`/admin`): listings, payments (filters/CSV/approve/refund), reviews moderation, KYC review, tenancies, bookings, CMS, settings, activity, RBAC users/roles.
- Payments: **canonical polymorphic `payments` ledger** via `PaymentLedgerService` + `RefundCalculator`; `booking_payments` deprecated/read-only (backfilled).
- Bookings: `hold_expires_at` (default 30m, `BOOKING_HOLD_MINUTES`); `luxsecure:expire-booking-holds` every 5 minutes; availability ignores expired holds.
- API: Sanctum with **role ability sets**; staff needing 2FA get short-lived `2fa:setup` only.
- Ops: listing cache UUID versioning; TrustProxies from `TRUSTED_PROXIES`; prod CSP nonce + Alpine CSP build; SMS `log`/`http` drivers.

### Stack
- Laravel 10, PHP 8.1+, Sanctum, Spatie Permission + Activity Log, PHPUnit, Pint
- Blade + Tailwind CSS 4 (Vite) + **@alpinejs/csp** — Estate Noir theme

### Git
- `origin` → `https://github.com/athar-javed72/LuxSecure.git`
- Hardening changes uncommitted unless user asks to commit

### Env samples
- `.env.example` — production-oriented (HTTPS, `APP_DEBUG=false`, `LOG_LEVEL=error`)
- `.env.local.example` — local development

---

## 2. Metrics

| Area | Status |
|------|--------|
| PHPUnit | **105 passed** |
| `npm run build` | Passing |
| `migrate:fresh --seed` | Passing |
| Ledger / holds / CSP / Sanctum / SMS | Done (local) |
| Pushed to GitHub | No (awaiting user) |

---

## 3. Known gaps

1. Push / commit when user requests.
2. Real JazzCash/Easypaisa + SMS HTTP gateway credentials for live calls.
3. Drop `booking_payments` table only after a later deprecation window.

---

## 4. Next (optional)

- `git commit` / `git push` when ready
- Wire production SMS HTTP gateway and payment secrets
