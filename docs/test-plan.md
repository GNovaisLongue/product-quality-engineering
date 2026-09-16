# Test Plan — Phase 4 (step 17)

## 1. Suites

| Suite | Mark | Contents | Gate |
| --- | --- | --- | --- |
| Smoke | `smoke` | health, list seed, create+delete one DVD | PR + main |
| API | `api` | CRUD per type, validation, conflict, 404, legacy BC | PR + main |
| Security | `security` | SQLi probes, malformed JSON, CORS/error leakage | main + nightly |
| Contract | `contract` | envelope + required fields + enum per OpenAPI | main |
| Database | `db` | column types, PK, CHECKs, round-trip persistence | main |
| Unit (PHP) | — | `backend/tests/Unit` via PHPUnit | PR + main |
| E2E | `e2e` | Playwright UI flows (skipped if no browser) | main (with browsers) |
| Regression | `regression` | smoke+api+contract+db+unit | nightly |

## 2. Schedule (roadmap order)

1. Unit + smoke first (fast feedback).
2. API + contract + DB.
3. E2E once UI stable.

## 3. Data management

- Unique SKUs: `TST-<TYPE>-<8 hex>`; never reuse seed SKUs.
- Cleanup: fixture finalizer DELETEs created SKUs (best-effort).
- No prod data; local Docker MySQL only.

## 4. Reporting

- `pytest --junitxml=reports/junit.xml -v` consumed by CI + `qa/analytics.py`.
- `docs/test-summary-report.md` updated per run; example reports in
  `docs/reports/` (Phase 8, step 44).
