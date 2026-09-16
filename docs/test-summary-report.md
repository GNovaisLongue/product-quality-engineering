# Test Summary Report — full green run (live Docker stack)

Date: 2026-09-10 · Env: `docker compose up --build` (MySQL 8.1 :9906, API :8000, UI :3000) · Seed: 3 rows

## Results

| Suite | Tool | Result |
| --- | --- | --- |
| Unit (domain polymorphism) | PHPUnit `backend/tests/Unit` | **11/11 pass (19 assertions)** |
| Smoke | pytest `tests/smoke` | **3/3 pass** |
| API (CRUD, validation, security, legacy) | pytest `tests/api` | pass |
| Contract (OpenAPI) | pytest `tests/contract` | pass |
| Database (schema) | pytest `tests/database` | 4/4 pass |
| Integration (round-trip) | pytest `tests/integration` | 3/3 pass |
| E2E (Playwright, Chromium) | pytest `tests/e2e` | **3/3 pass** (DVD / Book / Furniture via UI) |
| **Total pytest** | | **67/67 pass, 100% (~8 s)** |

Artefacts: `reports/junit.xml`, `reports/qa-metrics.json` (live, git-ignored);
frozen copies in `docs/reports/`. Analytics: `qa/analytics.py` → by_area +
by_product_type (DVD 10, Book 9, Furniture 11 passing).

## Defects closed by this run

BUG-001 (SQLi) → TC-SEC-001..003 pass; BUG-002 → TC-VAL-*pass; BUG-003/004 →
TC-DB-001/003/004 pass; BUG-005/007 → TC-API-* + TC-SEC-004 pass; BUG-006 →
`.env.development` untracked (verify: `git status` clean of it); BUG-008 (DVD
help text) fixed; **BUG-009 (decimal weights blocked by native `step=1`) was
caught by TC-E2E-002 during this run and fixed** (`step="any"`).

## Exit criteria

Smoke 100%, regression 100% (incl. E2E), no Critical/High open.
