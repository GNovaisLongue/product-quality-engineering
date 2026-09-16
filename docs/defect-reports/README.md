# Defect reports — index (Phases 1–3; all FIXED, verified live 2026-09-10)

| ID | File | Severity | Status | Verified by |
| --- | --- | --- | --- | --- |
| BUG-001 | `BUG-001-sql-injection.md` | Critical | **FIXED** | TC-SEC-001..003 (61-run suite green) |
| BUG-002 | (below) | High | **FIXED** | TC-VAL-001..018 |
| BUG-003 | (below) | High | **FIXED** | TC-DB-001/003/004 |
| BUG-004 | (below) | Medium | **FIXED** | TC-DB-001 |
| BUG-005 | (below) | Medium | **FIXED** | TC-API-006..009/013, legacy suite |
| BUG-006 | (below) | High | **FIXED** | `.env.development` untracked (`git rm --cached`); CI builds from `.env.example` |
| BUG-007 | (below) | Medium | **FIXED** | TC-SEC-004 (no SQL/stack/HTML leak) |
| BUG-008 | DVD help text said "size in cm" (unit is MB) | Trivial | **FIXED** | visual; `FetchData.ts` corrected to "MB" |
| BUG-009 | Decimal attribute values (e.g. weight 3.2) blocked by native `step=1` validation — Save silently dead | High | **FIXED** | TC-E2E-002 caught it; `step="any"` on type-specific number inputs |

## BUG-002 — No server-side validation

Direct `POST /` with empty/negative/malformed fields bypasses frontend checks; backend inserts blindly (only duplicate-SKU checked). Fix: per-subtype `validate()` + factory + 422 envelope.

## BUG-003 — price is VARCHAR(20)

String comparison/sort, no range check. Fix: `DECIMAL(10,2) NOT NULL CHECK (price > 0)`.

## BUG-004 — sku TEXT + PREFIX PK

`ADD PRIMARY KEY (sku(100))`. Fix: `sku VARCHAR(64) PRIMARY KEY`, `utf8mb4`.

## BUG-005 — Non-REST API

Insert vs mass-delete distinguished by JSON object-vs-array on same `POST /`; DELETE verb unused; `die()` plain-text responses; no `GET /{sku}`, `PUT`, or consistent codes. Fix: `GET/POST /api/products`, `DELETE /api/products[/{sku}]`, 200/201/204/400/404/409/422/500 + JSON envelope.

## BUG-006 — Secrets in repo

`.env.development` with `root/root` committed and referenced by `compose.yml`. Fix: untrack, keep `.env.example`, rotate creds, document local setup.

## BUG-007 — Verbose errors + CORS *

`executeQuery()` dies with full `$query` + exception message; `Access-Control-Allow-Origin: *`. Fix: logged server-side, generic 500 to client; restrictive CORS.
