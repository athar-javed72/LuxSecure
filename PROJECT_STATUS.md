# Project status — Maskanoor Group

**Last updated:** 22 October 2026  

This is a **snapshot for handover**, not a live monitoring dashboard. Recheck the VPS, Actions, and Git remotes before making irreversible changes.

| Related docs | |
|--------------|--|
| Developer guide | [`README.md`](README.md) |
| Deployment & CI/CD | [`DEPLOYMENT.md`](DEPLOYMENT.md) |
| Generic Ubuntu runbook | [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) |
| Artisan backups | [`docs/BACKUP.md`](docs/BACKUP.md) |

---

## Project overview

- **Maskanoor Group** is a Laravel-based multi-country property listing and management platform (listings, bookings/tenancies, payments, KYC, RBAC admin, Sanctum API, Estate Noir UI).  
- **Developed by Nexora Labs** (in-house / portfolio product).  
- The current VPS deployment at [https://maskanoor.duckdns.org](https://maskanoor.duckdns.org) is a **demo / portfolio** environment, not a full production customer system.

---

## Status legend

| Label | Meaning |
|-------|---------|
| **Verified** | Confirmed from this repository (or a dated external check still believed current). |
| **Previously verified; needs rechecking** | Worked at last record; re-confirm before relying on it. |
| **Pending verification** | Known concern or expected behaviour; not confirmed. |
| **Not implemented / not assessed** | Out of scope for the current demo or never evaluated. |

---

## Confirmed completed work (verified in repository)

| Item | Evidence |
|------|----------|
| GitHub repository on `main` | `origin` → `https://github.com/athar-javed72/MaskanoorGroup.git` |
| Laravel 13 application + PHPUnit / Pint | `composer.lock`, `phpunit.xml`, CI |
| Vite + Tailwind 4 + Alpine CSP frontend | `package.json`, `vite.config.js`, `resources/js/app.js` |
| GitHub Actions CI: Pint → frontend build → PHPUnit | `.github/workflows/ci.yml` |
| GitHub Actions deploy to `/home/opc/MaskanoorGroup` + restart `maskanoor` + curl demo URL | Same workflow `deploy` job |
| Health route | `GET /up` in `routes/web.php` |
| Example DB name `Maskanoor_db` | `.env.example` |
| Local development docs | `README.md` |

---

## Previously verified; needs rechecking

These were reported from operational checks on the demo VPS. **Re-verify on the host** before treating them as current.

| Item | Last recorded note |
|------|--------------------|
| Oracle Linux 9 ARM64 VPS | Environment description (not encoded in repo files) |
| Nginx reverse proxy in front of the app | Architecture assumption + HTTPS demo |
| HTTPS homepage returned success | CI “Verify website” and/or manual curl — recheck after each deploy |
| `maskanoor.service` running; app on `127.0.0.1:8000` | Unit file not in repo; confirm with `systemctl status` |
| Manual compressed MariaDB backup + `gzip -t` on **21 October 2026** | Host paths under `/home/opc/backups` |
| Backup script root-owned; backups/log root-owned | `/usr/local/bin/maskanoor-db-backup.sh` |
| GitHub Actions deploy reported successful | Confirm latest Actions run after pushes |

---

## Pending verification

| Item | Notes |
|------|--------|
| Next **scheduled** DB backup (`30 18 * * *` UTC / 11:30 PM PKT) | Confirm log entry after next window |
| Database **restore** drill | Do not rely on backups until restore is tested |
| Whether scheduled host backups are necessary for this demo | Product/ops decision |
| Exact Nginx site config and Certbot renewal | Not in repository |
| Exact `maskanoor.service` `ExecStart` | Not in repository |
| Firewall / exposed ports beyond “HTTPS works” | Not assessed in-repo |
| VPS Git vs `origin/main` divergence | See below |

---

## Known Git / history issues

| Issue | Status |
|-------|--------|
| VPS-only commit **`38b6538`** (reported to touch `composer.json`) | **Pending verification** — object **not present** in the local clone of `origin/main` as of the last doc update. Inspect on the VPS with `git log` / `git show` before any reset or force-push. |
| VPS `main` previously **one commit ahead** of `origin/main` | **Previously verified; needs rechecking** — compare `git status` / `git log origin/main..HEAD` on the server before deploying or syncing. |

**Working rule:** never force-push or reset without reviewing both GitHub and the VPS and preserving any required unique commits.

---

## Outstanding tasks (essential)

1. Inspect VPS commit `38b6538` (or current ahead commits) and decide merge / cherry-pick / discard intentionally.  
2. Recheck VPS `main` vs `origin/main` before Git surgery.  
3. Confirm the next scheduled host backup and review `/home/opc/backups/backup.log`.  
4. Decide if scheduled backups remain required for the portfolio demo.  
5. Perform a restore test before trusting backups.  
6. Review repository secrets and demo credentials before making the repository public.  
7. Reassess monitoring, backups, and security controls before handling real customer data.

---

## Future optional improvements

These are **optional**, not required for a simple demo:

- Managed offsite backup replication  
- Centralized logging / uptime monitoring  
- Redis HA / queue worker systemd units (if not already present)  
- php-fpm or Octane instead of a long-running `artisan serve`-style process (only if measured need)  
- Staging environment separate from the public demo  
- Formal load / security pen-test  

Do not treat enterprise hardening as a blocker for a portfolio demo.

---

## Working rules

1. Make routine source-code changes **locally**, then push via GitHub Actions — not by editing production trees ad hoc.  
2. Never force-push or hard-reset without reviewing remotes and preserving required changes.  
3. Never commit `.env`, passwords, private keys, SQL dumps, or backup archives.  
4. After each deploy: check GitHub Actions and the live site (`https://maskanoor.duckdns.org`).  
5. Prefer evidence over memory when updating this file.

---

## Not implemented / not assessed

- Full production SLA, multi-region failover, and paid WAF — **not assessed** for this demo.  
- Payment gateways in live mode — disabled / sandbox-oriented in `.env.example`.  
- Customer data residency / compliance audit — **not assessed**.
