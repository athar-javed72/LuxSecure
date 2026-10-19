# Maskanoor Group — Agent Operating Manual

Spec-driven workflow for this repo (ported from the Nexora Lab GSD / Ralph-loop setup).

## Where truth lives

| File | Role |
|------|------|
| `PRD.md` | Current phase requirements + task checklist |
| `progress.txt` | Append-only work log (never rewrite old entries) |
| `.planning/STATE.md` | What exists now, metrics, known gaps |
| `.agents/` | Plugins, skills, workflows, rules |
| `.agent/` | Runtime workflows + bin helpers (GSD / Ralph) |

## Plugins installed (`.agents/plugins/`)

| Plugin | Purpose |
|--------|---------|
| **gsd** | Get Shit Done — plan / discuss / execute / verify / progress |
| **ralph-loop** | Tight implement → verify → log loop |
| **context7** | Up-to-date library docs (Laravel, Tailwind, etc.) |
| **serena** | Semantic code navigation MCP |
| **coderabbit** | Review / autofix skills |
| **server-validation** | Form Request & validation discipline |

## Hard rules

1. One task at a time from `PRD.md`. Verify, append `progress.txt`, update `.planning/STATE.md` when a phase ends, then STOP.
2. Do not invent scope outside the active PRD.
3. Do not commit or push unless the user asks.
4. Re-read files before editing (file-first context).
5. Verification gate: `php -l` (changed PHP) → `php artisan test` → `npm run build` (if UI/assets changed).

## Stack reminder

Laravel 10 · Blade · Tailwind 4 (Vite) · Alpine.js · Sanctum · PHPUnit · MySQL
