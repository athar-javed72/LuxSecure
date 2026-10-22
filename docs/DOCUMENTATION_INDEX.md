# Documentation Index

Status values: Done, Partial, Not started, Existing (already in the repo, not written by this program), Not applicable (reason).

## Documents written by the architecture program

| Document | Status | Notes |
|---|---|---|
| docs/PROJECT_AUDIT.md | Partial (v1.0) | Repository based. Tests not executed, history shallow, VPS and staging DB not inspected. Open questions in section 15 |
| docs/DOCUMENTATION_INDEX.md | Done | This file |
| docs/PROGRAM_STATUS.md | Done | Progress tracker for this program. The repo already has a root `PROJECT_STATUS.md` (ops handover), so the program tracker uses a different name to avoid overwriting it |
| docs/PROJECT_OVERVIEW.md | Not started | Next. Source: README, audit sections 2 and 5, owner answers |
| docs/REQUIREMENTS.md | Not started | Confirmed vs proposed split is drafted in audit section 5 |
| docs/NON_FUNCTIONAL_REQUIREMENTS.md | Not started | No measurable targets exist today |
| docs/ARCHITECTURE.md | Not started | Blocked on owner answers 3 and 4 in the audit |
| docs/DOMAIN_MODEL.md | Not started | Start from audit D-01 |
| docs/DATABASE_DESIGN.md | Not started | Start from audit D-02 to D-06 |
| docs/API_SPECIFICATION.md | Not started | Reuse `docs/API.md` and Scribe output |
| docs/USER_FLOWS.md | Not started | |
| docs/UX_AND_DESIGN_SYSTEM.md | Not started | Estate Noir tokens documented in README |
| docs/INTERNATIONALIZATION_AND_LOCALIZATION.md | Not started | Start from audit U-03 to U-05 |
| docs/SEO_AND_MARKETING.md | Not started | Start from audit U-01, U-02, U-07 |
| docs/SECURITY_AND_PRIVACY.md | Not started | Start from audit S-01 to S-07 |
| docs/INTEGRATIONS.md | Not started | All integrations beyond JazzCash, Easypaisa, Sentry to be marked Proposed |
| docs/TESTING_STRATEGY.md | Not started | Start from audit B-02, O-01, O-05 |
| docs/DEPLOYMENT_AND_OPERATIONS.md | Not started | Merge with existing deployment docs, see below |
| docs/IMPLEMENTATION_ROADMAP.md | Not started | After architecture and requirements |
| docs/ARCHITECTURE_DECISIONS.md | Not started | |
| README.md | Existing | Developer guide. Needs a docs link and the PHP version fix (audit A-02) |

## Existing documents in the repository

| Document | Status | Notes |
|---|---|---|
| PROJECT_STATUS.md (root) | Existing | Ops and handover snapshot dated 2026-10-22. Not modified |
| DEPLOYMENT.md and docs/DEPLOYMENT.md | Existing | VPS CI/CD and generic Ubuntu runbook. Reuse for the operations doc |
| docs/BACKUP.md, docs/GEO_DATA.md, docs/API.md | Existing | Reuse |
| PRD.md, AGENTS.md, .planning/STATE.md, progress.txt | Existing, stale | Refer to Laravel 10 and LuxSecure (audit A-03). Not modified |
