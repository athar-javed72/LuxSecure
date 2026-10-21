# Deployment guide — Maskanoor Group

Operational guide for the **demo / portfolio** deployment and for repeating a similar production-style setup.

| Related docs | |
|--------------|--|
| Developer entry point | [`README.md`](README.md) |
| Status & pending work | [`PROJECT_STATUS.md`](PROJECT_STATUS.md) |
| App DB/storage backup artisan | [`docs/BACKUP.md`](docs/BACKUP.md) |
| Generic Ubuntu + PHP-FPM runbook | [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) |

**Evidence legend used in this file**

| Label | Meaning |
|-------|---------|
| **Verified** | Confirmed from files in this repository (or from a dated check recorded in [`PROJECT_STATUS.md`](PROJECT_STATUS.md)). |
| **Historical** | Reported from a past server check; re-verify before relying on it. |
| **Reconstructed** | Reasonable procedure inferred from the *current* architecture; not a transcript of commands that were definitely run. |
| **Pending verification** | Expected or planned; not confirmed in-repo or at last check. |

---

## 1. Deployment architecture and request flow

**Verified (from `.github/workflows/ci.yml` deploy job and project layout):**

```text
Internet (HTTPS)
    → Nginx (public reverse proxy on the VPS)
        → Laravel app listening on 127.0.0.1:8000  (systemd unit: maskanoor.service)
            → MariaDB/MySQL (DB name from env; .env.example uses Maskanoor_db)
```

| Item | Value | Evidence |
|------|--------|----------|
| GitHub repository | `https://github.com/athar-javed72/MaskanoorGroup` | `git remote` |
| Deploy branch | `main` | CI `if: … refs/heads/main` |
| VPS project directory | `/home/opc/MaskanoorGroup` | CI remote script |
| Public demo URL | `https://maskanoor.duckdns.org` | CI “Verify website” step |
| App bind address | `127.0.0.1:8000` | **Historical / operational note** (unit file not in repo) |
| Systemd unit | `maskanoor.service` | CI: `sudo -n systemctl restart maskanoor` |
| Web server | Nginx | **Historical / reconstructed** (no Nginx config committed) |
| Database | MariaDB / MySQL-compatible | `.env.example` `DB_CONNECTION=mysql`, DB name `Maskanoor_db` |

**Request flow (intended):** browsers hit HTTPS on the DuckDNS hostname; Nginx terminates TLS and proxies to the local Laravel process; Laravel talks to the database using credentials from the server `.env` (never committed).

---

## 2. Server and runtime requirements

### Application (verified from repo)

| Requirement | Source |
|-------------|--------|
| PHP **8.4** in CI (`setup-php`); local README targets **8.4.1–8.5** | `.github/workflows/ci.yml`, `README.md` |
| Composer dependencies | `composer.json` / `composer.lock` (Laravel **v13.38.0** in lock) |
| Node.js **22** + `npm ci` / `npm run build` | CI + `package.json` |
| Vite 7 + `@tailwindcss/vite` + Laravel Vite plugin | `package.json`, `vite.config.js` |
| Alpine CSP build | `resources/js/app.js` imports `@alpinejs/csp` |
| MySQL/MariaDB for real deploys; SQLite `:memory:` for PHPUnit | `.env.example`, `phpunit.xml`, CI env |

### Demo VPS (mixed evidence)

| Item | Status |
|------|--------|
| Oracle Linux 9, ARM64 | **Historical** (not asserted by files in this repo) |
| User / path `opc` @ `/home/opc/MaskanoorGroup` | **Verified** in CI deploy script |
| `maskanoor.service` | **Verified** that CI restarts it; unit file contents **not in repo** (**Pending verification** of exact `ExecStart`) |
| Nginx + HTTPS | HTTPS URL checked by CI; Nginx/Certbot config **not in repo** |

---

## 3. Tools used

| Tool / service | Purpose | Where used | Verification source | Confidence |
|----------------|---------|------------|---------------------|------------|
| Git | Source control | Local + VPS | `.git`, remotes | Confirmed |
| GitHub | Hosted repository | `origin` | `git remote` | Confirmed |
| GitHub Actions | CI + deploy on push to `main` | `.github/workflows/ci.yml` | Workflow file | Confirmed |
| PHP | Runtime | Local, CI, VPS | CI PHP 8.4; `composer.json` | Confirmed |
| Laravel 13 | Application framework | App | `composer.lock` v13.38.0 | Confirmed |
| Composer | PHP dependencies | Local, CI, deploy | `composer.json`, CI, deploy remote | Confirmed |
| Node.js / npm | Frontend toolchain | Local, CI, deploy | CI Node 22, `package.json` | Confirmed |
| Vite | Asset bundler | `npm run build` / `npm run dev` | `vite.config.js`, `package.json` | Confirmed |
| Tailwind CSS 4 | Styling | `resources/css/app.css` | `@tailwindcss/vite` | Confirmed |
| Blade | Server-rendered UI | `resources/views` | Repo layout | Confirmed |
| Alpine.js (CSP) | Client interactivity | `resources/js` | `@alpinejs/csp` import | Confirmed |
| PHPUnit | Automated tests | `php artisan test` | `phpunit.xml`, CI | Confirmed |
| Laravel Pint | PHP style | `vendor/bin/pint --test` | CI, `composer.json` | Confirmed |
| Playwright | Optional QA smoke scripts | `scripts/qa-smoke.mjs` | `package.json` + scripts | Confirmed as dependency/script; not part of CI |
| MariaDB / MySQL | Primary datastore | Server `.env` | `.env.example` | Confirmed as intended engine |
| Nginx | Reverse proxy / TLS | VPS | **Inferred** from architecture + HTTPS demo | Inferred |
| systemd | Process supervisor | `maskanoor.service` | CI restart | Confirmed unit name; config inferred |
| DuckDNS | Demo hostname | `maskanoor.duckdns.org` | CI verify URL | Confirmed hostname |
| Certbot / Let’s Encrypt | HTTPS certificates | VPS | **Inferred** (HTTPS works; installer not in repo) | Inferred |
| SSH | Admin + Actions deploy | CI `ssh` step | Workflow secrets usage | Confirmed mechanism |
| cron | Host DB backup schedule | VPS root crontab | **Historical** (see §8); not in repo | Historical |
| Cursor | Assisted documentation / coding prompts | Local IDE | Not part of app runtime | Confirmed for docs assistance only — **do not treat as the application build system** |
| Oracle Linux 9 ARM64 | Demo OS | VPS | **Historical** | Historical |

**Development tools** (local + CI): PHP, Composer, Node/npm, Vite, Tailwind, Pint, PHPUnit, Git.  
**Deployment / infrastructure tools**: SSH, systemd, Nginx, DuckDNS, HTTPS, GitHub Actions deploy job, host cron backups.

---

## 4. GitHub Actions CI/CD

**Verified workflow:** [`.github/workflows/ci.yml`](.github/workflows/ci.yml)

### `build-and-test` (every push/PR)

1. Checkout  
2. Setup PHP 8.4 (+ extensions)  
3. Setup Node 22  
4. `composer install`  
5. `npm ci`  
6. `vendor/bin/pint --test`  
7. `npm run build` (produces `public/build/manifest.json` required by Blade `@vite`)  
8. `php artisan test` (SQLite in-memory env)

### `deploy` (only on push to `main`, after tests pass)

1. Configure SSH using GitHub secrets `DEPLOY_HOST`, `DEPLOY_SSH_KEY`, `DEPLOY_USER`  
2. Remote: `cd /home/opc/MaskanoorGroup` → `git pull --ff-only origin main` → Composer (no-dev) → `npm ci` → `npm run build` → `optimize:clear` / `config:cache` / `view:cache` → `systemctl restart maskanoor`  
3. Verify: `curl` HTTPS homepage on `https://maskanoor.duckdns.org/`

### How to check results

1. Open the repository on GitHub → **Actions**.  
2. Select the latest workflow run for the commit.  
3. Confirm `build-and-test` green, then `Deploy to Oracle VPS` (on `main` pushes).  
4. Confirm the verify step succeeded (HTTP success against the demo URL).

**Secrets (names only — never commit values):** `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`.

---

## 5. Safe developer workflow (local → GitHub → VPS)

**Do routine source changes on the local machine, not by editing live code on the VPS.**

1. Change code locally (Windows or other).  
2. Run checks: `vendor/bin/pint --test`, `npm run build`, `php artisan test`.  
3. Review `git status` / `git diff`; commit with a clear message.  
4. Push to `origin` (`main` or a PR branch).  
5. Watch GitHub Actions.  
6. After merge/push to `main`, confirm deploy job + live site.  
7. If needed, inspect VPS logs (commands in §7).

**Do not** force-push or hard-reset without reviewing remote/VPS Git state.  
**Do not** commit `.env`, keys, dumps, or backup archives (see `.gitignore`).

Alternative helper on some hosts: [`scripts/deploy.sh`](scripts/deploy.sh) (pull, migrate, build, Supervisor restart). That script targets a **Supervisor**-style setup and is **not** what the current GitHub Actions remote script runs. Prefer the CI path for the Oracle demo unless you have verified Supervisor on that host.

---

## 6. Environment configuration and secrets

Copy from [`.env.example`](.env.example) on the server; never commit the real file.

| Area | Notes |
|------|--------|
| `APP_ENV=production`, `APP_DEBUG=false` | Required for public demos |
| `APP_URL` | Must match the public HTTPS URL |
| `DB_*` | Example DB name `Maskanoor_db`; credentials live only in server `.env` |
| `CACHE_*` / `QUEUE_*` / `SESSION_*` | Example prefers Redis |
| `TRUSTED_PROXIES` | Set to the reverse-proxy addresses if Nginx sits in front |
| `SESSION_SECURE_COOKIE` | `true` behind HTTPS |
| Uploads | `UPLOADS_DISK`, `PRIVATE_DISK` |
| Payments / messaging / Sentry | Leave disabled or empty until intentionally configured |

GitHub Actions secrets are configured in the repository settings; they are not stored in this documentation.

---

## 7. Nginx, systemd, HTTPS, and Laravel service verification

Exact Nginx site files and the `maskanoor.service` unit are **not stored in this repository**. Use these **diagnostic** commands on the VPS (require appropriate privileges):

```bash
sudo systemctl status maskanoor --no-pager
sudo systemctl restart maskanoor
sudo journalctl -u maskanoor -n 100 --no-pager
sudo nginx -t
curl -I https://maskanoor.duckdns.org
```

**Pending verification on any new host:** contents of the systemd unit (`ExecStart`, user, working directory), Nginx `proxy_pass` target, certificate renewal timer, and firewall rules.

---

## 8. Database backup configuration (host-level)

> Distinct from Laravel’s `php artisan maskanoor:backup` (see [`docs/BACKUP.md`](docs/BACKUP.md)).

### Known host backup layout (**Historical** — not defined in this repo)

| Item | Value |
|------|--------|
| Script | `/usr/local/bin/maskanoor-db-backup.sh` |
| Directory | `/home/opc/backups` |
| Log | `/home/opc/backups/backup.log` |
| Database | `Maskanoor_db` |
| Cron | `CRON_TZ=UTC` and `30 18 * * * /usr/local/bin/maskanoor-db-backup.sh` |
| Local wall-clock | 11:30 PM Pakistan time (UTC+5) when PK observes standard offset |

**Ownership (last recorded check):** backup script root-owned and executable only by root; latest backup artifacts and log were root-owned. **Do not change backup configuration as part of documentation work.**

| Check | Status |
|-------|--------|
| Manual compressed backup + `gzip -t` on **21 October 2026** | **Historical — succeeded** |
| Next scheduled cron run | **Pending verification** |
| Restore drill into a scratch database | **Pending verification** |
| Whether scheduled backups are required for a portfolio demo | **Decision pending** |

---

## 9. Server setup journey

### A. What this repository proves happened (CI / app)

**Verified:** push to `main` can install Composer deps, build Vite assets, clear/cache config & views, restart `maskanoor`, and curl the HTTPS homepage.

### B. Reconstructed first-time VPS procedure

> **Reconstructed** — labelled so it is not mistaken for a historical command log. Adapt package managers for Oracle Linux (dnf) vs Ubuntu (apt). Prefer verifying against the live host before production use.

High-level sequence:

1. Provision Oracle Linux 9 ARM64 VPS; create/use `opc` user; harden SSH (keys, no password root login as policy allows).  
2. Install PHP 8.4+ (cli + extensions matching CI), Composer, Node 22, MariaDB/MySQL, Nginx.  
3. Clone `https://github.com/athar-javed72/MaskanoorGroup` into `/home/opc/MaskanoorGroup` on branch `main`.  
4. Create `.env` from `.env.example`; set `APP_KEY`, DB credentials, `APP_URL=https://maskanoor.duckdns.org`.  
5. Create database `Maskanoor_db`; run `composer install --no-dev`, `php artisan migrate --force`, seed **reference** + **RBAC** only (no demo seeders in production).  
6. `php artisan storage:link`; `npm ci && npm run build`.  
7. Create `maskanoor.service` to run the Laravel HTTP server (or Octane/FPM — **confirm on host**) bound to `127.0.0.1:8000`.  
8. Configure Nginx as reverse proxy to that port; obtain TLS for the DuckDNS name.  
9. Open only required public ports (80/443); keep 8000 on loopback.  
10. Add GitHub Actions deploy secrets; push to `main`; confirm Actions + site.  
11. Optionally install host backup script + root cron (§8).

For a **generic Ubuntu + php-fpm** layout (different from this demo’s systemd + `:8000` pattern), see [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md).

---

## 10. Troubleshooting and recovery checklist

| Symptom | Things to check |
|---------|-----------------|
| CI: “Vite manifest not found” | Ensure `npm run build` runs **before** PHPUnit (current workflow order). |
| CI deploy SSH failure | Secrets `DEPLOY_*`; host key / firewall. |
| Site 502 / connection refused | `systemctl status maskanoor`; journalctl; Nginx `proxy_pass`. |
| Site 500 | `storage/logs/laravel.log`; `.env`; `config:cache` stale after env change. |
| Assets missing / unstyled | `public/build` after `npm run build`; permissions. |
| DB errors | MariaDB up; `.env` `DB_*`; migrations applied. |
| HTTPS errors | Cert validity; `nginx -t`; DNS for DuckDNS. |
| Deploy pull rejected | VPS has local commits ahead of `origin/main` — inspect before reset (see [`PROJECT_STATUS.md`](PROJECT_STATUS.md)). |

---

## 11. Security considerations and production-readiness

**Do now (demo hygiene)**

- [ ] Keep `APP_DEBUG=false` on the public host.  
- [ ] Rotate any demo passwords; never commit them.  
- [ ] Review GitHub secrets and collaborator access.  
- [ ] Confirm `.env` and backup dumps are not world-readable.  
- [ ] Before making the repo public: scrub history and settings for credentials.

**Before real customer data**

- [ ] Reassess backups (offsite + restore drills).  
- [ ] Monitoring / alerting.  
- [ ] WAF / rate limits / hardened SSH.  
- [ ] Queue workers, mail, and payment gateways configured intentionally.  
- [ ] Legal pages, KYC, and country tax settings reviewed.

**Never document or commit:** passwords, tokens, private keys, `.env` bodies, or backup file contents.

---

*This guide is documentation only. It does not change application or infrastructure configuration.*
