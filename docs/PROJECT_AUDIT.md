# Project Audit (v1.0, repository-based)

Date: 2026-10-23
Repository: `athar-javed72/MaskanoorGroup`, branch `main`, commit `f773766` (2026-10-22, "Merge pull request #1 from athar-javed72/fix/living-page")
Supersedes: baseline v0.1 (documents and live homepage only). That version assumed a static site. The repository shows a full platform, so its F-10 and F-11 are resolved below.

## 1. Evidence and Limits

| Tag | Meaning |
|---|---|
| CODE | Source file read in this audit. Path given in the finding. Logic read, not executed. |
| SITE | Live staging homepage `https://maskanoor.duckdns.org` fetched 2026-10-22. Homepage only. |
| DOC | Statement from repo docs (`README.md`, `PROJECT_STATUS.md`, `.planning/STATE.md`, `DEPLOYMENT.md`). Treated as a claim, not proof. |
| SPEC | `Maskanoor Hybrid Web Application Specification.md` (target design). |

Limits of this audit:

- Tests were NOT executed. The sandbox has PHP 8.3.6, the project needs PHP 8.4.1 or newer (DOC). Test files were counted and some were read, nothing was run.
- The clone is shallow (1 commit). Git history, previous decisions and secrets in old commits were not examined.
- The VPS, Nginx, systemd, cron and the staging database were not accessed.
- Only the homepage of the live site was fetched.
- No secret values are recorded here. `.env.example` has empty secret fields (checked by key name only).

Classification used: Implemented and verified (CODE) = logic path read and complete. Partially implemented. Missing. Needs improvement. Unverified. Proposed future enhancement.

## 2. Existing Project Overview

- Product: multi-country property listing and management platform by Nexora Labs (DOC). Purposes supported by the search config: sale, rent, short stay (`config/search.php`).
- Stack (CODE `composer.json`, `package.json`): Laravel 13, Blade, Tailwind CSS 4 via Vite, Alpine.js CSP build, Sanctum, Spatie Permission and Activity Log, Intervention Image 2, DomPDF, Scribe, Sentry (optional), google2fa. Database MySQL/MariaDB in production, SQLite in tests (DOC, `phpunit.xml`, CI).
- Size (CODE): 46 models, 35 migrations, 18 policies, 105 files under `app/Http`, 27 under `app/Services`, 182 test methods in 29 feature test files and 1 unit test.
- Roles (DOC, README): super_admin, admin, agent, support, landlord, hotel_manager, client, plus country_admin and organization_admin referenced in `.planning/STATE.md`.
- Deployment (CODE `.github/workflows/ci.yml`): GitHub Actions runs Pint, frontend build, PHPUnit, then deploys over SSH to an Oracle VPS on every push to `main`.
- The live site is described as a demo/portfolio deployment, not a customer production system (DOC `PROJECT_STATUS.md`).

## 3. Current Architecture Assessment

Single Laravel monolith with layers by folder: `Http` (controllers, requests, middleware), `Services` (Hotel, Payments, Rent, Messaging), `Policies`, `Support`, `Models`. No module boundaries inside the app namespace, but service classes are already grouped by business area, which makes a modular monolith a low-cost next step. No microservices, which matches the master prompt's guidance.

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| A-01 | Monolith with service layer for booking, payments, rent | Implemented and verified | CODE `app/Services/*` | Good base, no unnecessary complexity | Low | Keep. Define module boundaries in the architecture doc |
| A-02 | `composer.json` requires PHP `^8.2`, README says 8.4.1 or newer is required | Needs improvement | CODE `composer.json`, DOC README | Wrong platform constraint can break installs and CI assumptions | Medium | Align the constraint to the real minimum |
| A-03 | Stale docs: `AGENTS.md` and `PRD.md` say Laravel 10 and LuxSecure, `.planning/STATE.md` says remote is `LuxSecure.git`, `config/luxsecure.php` and `luxsecure:*` aliases remain | Needs improvement | CODE/DOC | Confuses developers and AI agents, wrong scope | Medium | Archive old PRD, refresh `AGENTS.md` and `STATE.md` |
| A-04 | VPS-only commit `38b6538` and a VPS ahead of `origin/main` are unresolved | Unverified | DOC `PROJECT_STATUS.md` | Deploy uses `git pull --ff-only`, a diverged server will fail deploys | High | Inspect the VPS before the next deploy |
| A-05 | Public API v1 (auth, listings, favorites, bookings, profile, 2FA, countries) with token abilities and Scribe docs; legacy `/api/properties` alias kept | Implemented and verified | CODE `routes/api.php` | Mobile/partner integration is possible | Low | Add versioning and deprecation policy later |

## 4. Features and Functionality Inventory

| ID | Feature | Class | Evidence | Notes |
|---|---|---|---|---|
| FI-01 | Listings with types, dynamic attributes (JSON), gallery, floor plan, video URL, private document, caching | Implemented and verified | CODE `Listing`, `ListingController`, `ListingCacheService` | Purpose: sale, rent, short_stay |
| FI-02 | Hotel inventory: room types, rooms, seasonal prices | Implemented and verified (inventory), seasonal pricing logic not read | CODE migrations `room_types`, `rooms`, `seasonal_prices`, `BookingAvailabilityService` | Resolves SPEC risk S-03 |
| FI-03 | Short-stay booking with holds, cancellation policies, refund calculator | Implemented and verified | CODE `BookingAvailabilityService`, `PaymentLedgerService`, `RefundCalculator` | Correctness of refund rules not verified |
| FI-04 | Long-term rent: tenancies, rent schedule, overdue list, due notifications | Partially implemented | CODE `Tenancy`, `RentScheduleGenerator`, `NotifyRentDueCommand` | No lease document, renewal or deposit lifecycle found |
| FI-05 | Payments ledger, bank transfer proof with admin approval, JazzCash/Easypaisa sandbox, invoice PDF, CSV export | Implemented and verified | CODE `app/Services/Payments/*`, routes | Gateways disabled by default, sandbox oriented. Bank transfer approval and invoice code not read |
| FI-06 | Reviews limited to completed booking or tenancy, moderation | Implemented and verified | CODE `ReviewController` lines 37-51 | Gives verified reviews |
| FI-07 | KYC with country-specific document types and admin review | Unverified (routes, controller and table exist, logic not read) | CODE `KycDocumentController`, `kyc_document_types` | |
| FI-08 | Auth: register, login, email verify, reset, optional 2FA (required for panel when configured), phone OTP | Unverified (routes, throttles and middleware read, service logic not read) | CODE `routes/web.php`, `AppServiceProvider` | |
| FI-09 | Multi-country: countries, currencies, languages, cities/areas, country settings, wind-down, visibility | Implemented and verified | CODE migrations, `CountryContext`, `CountryVisibility` | PK, SA, AE, QA, MY, GB per DOC |
| FI-10 | Organizations (multi-branch operators) and org admin role | Partially implemented | CODE `Organization`, `listings.organization_id` | Not tenant-isolated by domain or schema |
| FI-11 | Admin panel: listings, users, roles matrix, CMS (blog, FAQ, pages, testimonials), media, settings, leads, tickets, reports, activity log | Unverified (routes exist, controllers not read) | CODE `routes/web.php` lines 140-300 | |
| FI-12 | Notifications: database, mail, SMS/WhatsApp channel interface with log and HTTP drivers | Partially implemented | CODE `app/Services/Messaging` | No real provider confirmed |
| FI-13 | Stay search by destination, dates and guests with availability | Partially implemented | CODE `HotelsStaySearchService` | SQL LIKE based |
| FI-14 | Favorites (server-side, login required) | Implemented and verified | CODE `FavoriteController` | SPEC wanted guest LocalStorage wishlist, not built |
| FI-15 | Map search, mortgage calculator, virtual tours, host calendar sync | Missing | CODE (no map library in `package.json` or `resources`) | SPEC items |

## 5. Functional Requirements Inventory (confirmed vs proposed)

Confirmed sources: `README.md` module table and `.planning/STATE.md` (DOC), cross-checked in code above. Proposed only: everything in SPEC that has no code (map search, mortgage calculator, PKR/USD toggle, WhatsApp notifications, guest wishlist, virtual tours, JSON-LD for listings). These move to `docs/REQUIREMENTS.md` as "Proposed" with IDs in a later step. No separate requirements document exists in the repo; `PRD.md` covers only the old UI redesign phase.

## 6. Non-Functional Requirements Inventory

No measurable NFRs exist in the repo (no response-time, availability, RPO/RTO or capacity targets). Present controls: rate limiters (login 5/min per email and IP, two-factor 6/min, contact 6/min, API 60/min, OTP send 3 per 10 min), listing query cache, scheduled DB backup, optional Sentry. Targets must be proposed in `docs/NON_FUNCTIONAL_REQUIREMENTS.md`.

## 7. Database and Domain Model Assessment

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| D-01 | One `listings` table holds the asset, the offer (`purpose`, `price_unit`) and, for hotels, the branch (`branch_name`, check-in times, `manager_id`). Bookable units are separate (`room_types`, `rooms`) | Partially implemented | CODE migrations, `Listing` fillable | Works now. Conflicts with the master prompt's goal of separating asset, offer and bookable unit as the product grows | High | Decide the split in `DOMAIN_MODEL.md` before adding hotel operations or leases |
| D-02 | `listings.price` is `decimal(15,0)`, while `bookings.total` and `payments.amount` are `decimal(12,2)`. `currencies.decimals` exists | Needs improvement | CODE migrations | A nightly price with fractions cannot be stored on the listing, mixed precision invites rounding bugs | Medium | Store prices in minor units or use one precision rule |
| D-03 | `payments.currency` is `string(10)`, `bookings.currency` is `string(3)` | Needs improvement | CODE migrations | Inconsistent, minor | Low | Normalize to ISO 4217 length |
| D-04 | No cleaning fee, service fee or per-night rate snapshot columns on bookings | Missing | CODE (grep found no `cleaning` or `service_fee` anywhere) | Price breakdown shown at checkout cannot be reproduced later | Medium | Add a booking price breakdown (line items) |
| D-05 | No payout, commission-ledger or host settlement tables. Commission/tax is applied in `InvoiceService` from settings | Missing | CODE migration list, grep `commission` | The marketplace model (host payouts, escrow) has no money-out records | High | Design ledger and payouts in `DATABASE_DESIGN.md` |
| D-06 | Availability is derived from overlapping bookings versus room count. No per-date calendar, host blocks, or per-day price overrides beyond `seasonal_prices` | Partially implemented | CODE `BookingAvailabilityService` | No maintenance blocks, no iCal or channel sync path | Medium | Add blocks and calendar view when hotel operations phase starts |
| D-07 | Unique constraints exist on `listings.slug`, `payments.uuid`, `payments.idempotency_key`, `payment_webhook_events(gateway,event_id)`, favorites per user and listing | Implemented and verified | CODE migrations | Good integrity basics | Low | Keep |
| D-08 | Legacy `properties` table and `property_images` still exist next to `listings` | Needs improvement | CODE migration `2025_02_19_000003`, `2026_03_22_100000` | Dead schema, confusion | Low | Confirm unused, then plan removal |

## 8. Availability, Booking and Payment Integrity

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| B-01 | Booking creation runs in a DB transaction, locks the room rows with `lockForUpdate()`, then locks overlapping bookings and assigns a free room | Implemented and verified | CODE `BookingAvailabilityService::createBooking` | Prevents double booking on MySQL/InnoDB if the logic is correct | Critical (core) | Keep. Add the concurrency test below |
| B-02 | Tests run on SQLite `:memory:` where `lockForUpdate()` has no effect. No concurrent booking test was found | Unverified | CODE `phpunit.xml`, CI env | The main data-integrity promise is untested on the real database | High | Add a MySQL service job and a two-connection race test to CI |
| B-03 | Overlap query uses `whereDate()` on `check_in` and `check_out` | Needs improvement | CODE `overlappingBookingsQuery` | Functions on columns can stop MySQL using the `(room_type_id,status,check_in,check_out)` index, slower under load | Medium | Compare using plain date columns after confirming column type |
| B-04 | Pending bookings carry `hold_expires_at` and an expiry command is scheduled every 5 minutes. The command body was not read | Partially verified | CODE `bootstrap/app.php` lines 126-132, migration `2026_10_17_140446` | Holds only expire if the scheduler runs on the server. See O-02 | High | Verify the scheduler on the VPS |
| B-05 | Bookings have no idempotency key. Payments do | Missing | CODE migrations, `PaymentController` | A double submit can create two pending bookings for the same dates and user | Medium | Add a client token with a unique index |
| P-01 | Payment status machine with allowed transitions, row locks on state changes, refund flow, invoices | Implemented and verified | CODE `PaymentStatusMachine`, `PaymentLedgerService` | Consistent state changes | Low | Keep |
| P-02 | Webhook handling: signature check with `hash_equals`, unique `(gateway,event_id)`, lock, replay-safe | Implemented and verified (JazzCash read) | CODE `JazzCashGateway`, `RecordsWebhookEvents` | Easypaisa has the same structure but was not read line by line | Medium | Read Easypaisa and compare both to provider specs |
| P-03 | Whether the JazzCash hash input format matches the provider specification was not checked | Unverified | CODE | A wrong format would reject real webhooks | Medium | Verify against provider docs before enabling live mode |
| P-04 | Webhook routes have no throttle middleware | Needs improvement | CODE `routes/web.php` lines 97-100 | Cheap abuse of an unauthenticated endpoint | Low | Add a dedicated limiter |
| P-05 | Only Pakistani gateways (JazzCash, Easypaisa, bank transfer). No card or international provider | Missing | CODE `app/Services/Payments/Drivers` | Blocks international expansion | High | Select and document a gateway per region as "Proposed" |

## 9. Security and Performance Assessment

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| S-01 | Login throttling, 2FA middleware for panel users, signed email verification, active-user check, Sanctum token abilities. Routes and limiters read, 2FA and verification logic not read | Partially verified | CODE routes, middleware names, `AppServiceProvider` limiters | Solid baseline on paper | Medium | Read the 2FA and verification code in the security doc |
| S-02 | Spatie RBAC plus `CountryScoped` global scope and 18 policies, deny by default for country-scoped roles (DOC). Scope and policy code not read | Unverified (files exist) | CODE `app/Support/Scopes`, `app/Policies`, DOC `.planning/STATE.md` | Isolation by country is claimed, not confirmed | High | Read `CountryScoped` and policies, add negative tests per role |
| S-03 | Security headers: CSP with per-request nonce, HSTS in production, X-Frame-Options, nosniff, referrer and permissions policy | Implemented and verified | CODE `SecurityHeaders` | Production CSP still allows `unpkg.com` scripts, `style-src 'unsafe-inline'` and `ws:`/`wss:` in `connect-src` | Medium | Self-host scripts, tighten `connect-src` |
| S-04 | `.env`, SQL dumps, zips and key files are not tracked (checked with `git ls-files`), `.env.example` has empty secret fields. Repo is public on GitHub. README lists a demo admin login that exists only when `APP_ENV` is local or testing | Implemented and verified | CODE `git ls-files`, `.env.example` | Old commits were not scanned (shallow clone). `PROJECT_STATUS.md` says to review secrets "before making the repository public", but it is already public | High | Run secret scanning over full history |
| S-05 | No data export, deletion or retention features. Legal pages are placeholders (`[PLACEHOLDER:*]`) per STATE | Missing | CODE grep, DOC | Privacy risk before real customer data | High | Define privacy requirements and policy pages |
| S-06 | Uploads: image optimizer, private disk for KYC proofs, invoices and private documents | Unverified | CODE structure only | Validation rules and file type checks not read | Medium | Review `MediaUploadService` and request rules |
| S-07 | Rate limits absent on search and booking quote endpoints | Needs improvement | CODE routes | Scraping and load risk | Low | Add limiters |
| PF-01 | Listing search uses `LIKE '%term%'` and JSON attribute filters with 12 per page. No full-text or geo search | Needs improvement | CODE `ListingController`, `HotelsStaySearchService` | Fine for demo data, slow at scale | Medium | Plan indexed or external search only when measured |

## 10. UX, Accessibility, SEO and Internationalization

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| U-01 | Base layout sets title, description, Open Graph image and Twitter card. Canonical and JSON-LD appear only on division pages. Listing detail has title and description only | Partially implemented | CODE `include/master.blade.php`, `divisions/show.blade.php`, `properties/show.blade.php` | Weak rich results for listings | High | Add listing structured data and canonical |
| U-02 | No sitemap and no hreflang. `public/robots.txt` allows everything and has no Disallow for `/admin`, `/api`, `/webhooks` or sitemap link | Missing | CODE | Poor international indexing, avoidable exposure of non-public paths to crawlers | High | Add sitemap, hreflang, robots rules |
| U-03 | Languages table with RTL flag, locale switcher, per-country default locale. `lang/` has only 63 lines in total, some UI strings and validation messages are hardcoded English. ar and ms are machine drafts (DOC) | Partially implemented | CODE `lang/`, `SetCountryContext`, `BookingAvailabilityService` | Urdu/Arabic/Malay pages will be mixed language | Medium | Move strings to lang files, get native review |
| U-04 | `currencies.rate_to_base` exists, but no conversion logic was found in app code besides model and admin CRUD | Partially implemented | CODE grep | No cross-currency display or pricing rules | Medium | Define conversion rules in the i18n doc |
| U-05 | Budget presets are hardcoded PKR "Cr" bands, used regardless of country | Needs improvement | CODE `config/search.php` | Wrong filters for AED, GBP and other countries | Medium | Make presets per country and currency |
| U-06 | Skip link, `aria-current`, image alt text and RTL `dir` are present. No automated accessibility tests | Unverified | CODE views | Level of accessibility unknown | Medium | Add axe or Playwright checks |
| U-07 | No analytics or cookie consent code found | Missing | CODE grep | No conversion measurement, consent needed for some regions | Medium | Choose tooling in the marketing doc |
| U-08 | Staging homepage shows monogram placeholders and only a "House" type card. Demo listings seed only in local and testing | Unverified | SITE, CODE `README` seeding note | Likely the staging database has no listings, which explains the static feel. Not confirmed | Medium | Check the staging DB for listings and listing types |
| U-09 | `/properties?type=house` is handled by `ListingController` (type slug filter). SPEC and earlier audit called it a dead link | Implemented and verified | CODE `ListingController` lines 35-40 | Not dead in code. Result depends on data | Low | Close earlier finding F-04 |

## 11. Testing, CI/CD and Operations

| ID | Finding | Class | Evidence | Impact | Priority | Action |
|---|---|---|---|---|---|---|
| O-01 | CI runs Pint, `npm run build`, PHPUnit (SQLite). No static analysis, coverage, dependency audit, MySQL job or browser tests. Playwright is a dev dependency and `scripts/qa-smoke.mjs` exists but is not in CI | Partially implemented | CODE `ci.yml`, `package.json` | Regressions in MySQL-specific and UI behavior go unnoticed | Medium | Extend CI in the testing strategy phase |
| O-02 | Deploy script pulls, installs, builds, caches and restarts the service. It does not run `migrate`, restart queue workers or manage a scheduler. No rollback or maintenance mode. Smoke check is homepage status only | Needs improvement | CODE `ci.yml` | Schema changes need manual steps. If no scheduler runs, booking holds, backups and rent notices never run | High | Verify scheduler and queue worker on the VPS, add them to deployment docs |
| O-03 | Backup command and host script exist. A restore drill was never done | Unverified | DOC `PROJECT_STATUS.md` | Backups are unproven | High | Run and document a restore test |
| O-04 | Monitoring limited to optional Sentry, `/up` health route and activity log. No uptime or metrics | Partially implemented | CODE, DOC | Slow detection of outages | Medium | Define alerting in operations doc |
| O-05 | 182 test methods found in 29 feature files. DOC states 152 passing at an earlier point. Nothing was run here | Unverified | CODE, DOC | Current pass rate unknown | Medium | Run tests on PHP 8.4 and record the result |

## 12. Technical Debt and Risks (ranked)

1. Double-booking guard untested on MySQL (B-02) and idempotency missing for bookings (B-05).
2. Scheduler and queue worker not confirmed on the server (O-02, B-04).
3. No payouts or commission ledger for a marketplace model (D-05) and no international payment option (P-05).
4. Privacy and legal gaps before real data (S-05) and secrets history unchecked on a public repo (S-04).
5. SEO and i18n are not ready for international marketing (U-01 to U-05).
6. Domain model conflates asset, offer and hotel branch (D-01).
7. Documentation and branding drift (A-03, A-04, D-08).

## 13. Existing Versus SPEC and Master Prompt

| Topic | SPEC says | Repo has | Gap |
|---|---|---|---|
| Stack | Laravel 12 | Laravel 13 | Update spec |
| Listings and roles | One `properties` table, one `users.role` | `listings` with purpose and price unit, Spatie roles and permissions | Repo is ahead of the spec |
| Hotel inventory | `stay_calendars` per property per date | `room_types`, `rooms`, derived availability | Different design, no per-date calendar |
| Booking lock | `SELECT ... FOR UPDATE` on calendar rows | Lock on room rows and overlapping bookings | Equivalent intent, not tested on MySQL |
| Fees | cleaning fee, service fee on bookings | Not present | Add |
| Wishlist | Guest LocalStorage | Server-side, login only | Decide |
| Maps, calculator, PKR/USD toggle, WhatsApp, JSON-LD for listings | Required | Not built | Proposed |

## 14. Prioritized Gap Analysis

| Priority | Gap | IDs |
|---|---|---|
| Critical | Prove booking integrity on MySQL, add booking idempotency | B-02, B-05 |
| High | Server scheduler, queue, deploy migrations, restore drill | O-02, O-03, B-04 |
| High | Secret scan of full history, privacy and legal pages | S-04, S-05 |
| High | Payouts and commission ledger, international payments | D-05, P-05 |
| High | Listing SEO, sitemap, hreflang, robots | U-01, U-02 |
| High | Decide asset, offer and unit split | D-01 |
| Medium | Price precision, fees snapshot, i18n completeness, currency rules, CSP tightening, CI upgrades, test run | D-02, D-04, U-03 to U-05, S-03, O-01, O-05 |
| Low | Legacy tables, docs drift, webhook throttle, rate limits | D-08, A-03, P-04, S-07 |

## 15. Open Questions for the Owner

1. Is the VPS running a scheduler (`schedule:work` or cron) and a queue worker today?
2. Does the staging database contain listings and listing types?
3. Which payment gateway should serve non-Pakistani countries first?
4. Is the platform a marketplace paying out hosts, or does Maskanoor operate the properties itself? This decides D-05.
5. Is the repository meant to stay public?
