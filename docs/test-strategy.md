# Test Strategy — product-quality-engineering (Phase 4, step 16)

## 1. Objectives

Prove that the polymorphic product domain (DVD | Book | Furniture) survives
API → Application → Domain → Database → API intact, that hardening (SQLi fix,
schema, validation, REST errors) holds, and that regressions are caught by CI.

## 2. Scope

- In: REST API (`/api/products`, legacy `/` BC), domain polymorphism, MySQL
  persistence + constraints, E2E UI flows, performance smoke, CI regression.
- Out: auth (none exists), visual testing, production hardening.

## 3. Levels & types

| Level | What | Tool | Where |
| --- | --- | --- | --- |
| Unit | DVD/Book/Furniture/Dimensions/Factory validation + polymorphism | PHPUnit | `backend/tests/Unit` |
| API | CRUD, validation 422, conflict 409, 404, security regression | pytest + requests | `tests/api` |
| Contract | Responses match `openapi/openapi.yaml` shapes | pytest + jsonschema | `tests/contract` |
| Database | Schema types, PK/CHECK/NOT NULL, persistence round-trip | pytest + PyMySQL | `tests/database`, `tests/integration` |
| E2E | Create DVD/Book/Furniture via UI, mass delete | Playwright | `tests/e2e` |
| Regression | Full suite on `main` + nightly | GitHub Actions | `.github/workflows` |

## 4. Test design techniques (steps 18–20)

- **Equivalence partitioning** per subtype: valid / zero / negative /
  non-numeric / missing / malformed `HxWxL` / unknown type / duplicate SKU.
- **Boundary-value analysis**: price `0 / 0.01`, size/weight `0 / epsilon`,
  SKU length `64 / 65`, dimensions with `0` segment.
- **Decision table**: `(type × attribute-shape)` → 201 vs 422 matrix.
- **State transition**: product `absent → created → retrieved → deleted →
  404`, covered by round-trip tests.

## 5. Environments

- `dev`: `docker compose up --build` (MySQL 9906, API 8000, UI 3000).
- `ci`: same stack via GitHub Actions service containers / compose.
- Test data: generated `TST-<uuid8>` SKUs, cleaned up per-test (DELETE).

## 6. Entry/exit criteria

- Entry: `GET /health` 200, seed rows present.
- Exit: 100% smoke pass, ≥95% regression pass, no Critical/High defects open,
  perf within baseline (see `docs/test-summary-report.md`).

## 7. Risks & mitigation

See `docs/risk-analysis.md`. Highest: SQLi regression (covered by
`tests/api/test_security.py`), schema drift (covered by
`tests/database/test_schema.py`).
