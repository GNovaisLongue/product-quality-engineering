# Traceability Matrix — Phase 4 (step 21)

| Requirement | Test cases | Defects |
| --- | --- | --- |
| FR-1 List products | TC-API-001, TC-API-011, TC-DB-002 | — |
| FR-2 Add DVD | TC-API-002, TC-VAL-001..004, TC-E2E-001 | BUG-002 |
| FR-2 Add Book | TC-API-003, TC-VAL-005, TC-E2E-002 | BUG-002 |
| FR-2 Add Furniture | TC-API-004/005, TC-VAL-006..009, TC-E2E-003 | BUG-002 |
| FR-3 Mass delete | TC-API-008/013, TC-E2E-004, TC-SEC-005 | BUG-005 |
| FR-4 SKU uniqueness | TC-API-010, TC-DB-003 | — |
| Validation (server) | TC-VAL-001..018 | BUG-002 |
| SQLi fix | TC-SEC-001..003 | BUG-001 |
| Schema hardening | TC-DB-001/003/004 | BUG-003, BUG-004 |
| REST + error envelope | TC-API-006/007/009, TC-VAL-017, TC-SEC-004 | BUG-005, BUG-007 |
| Secrets removal | (process check: `.env.development` untracked) | BUG-006 |
| Polymorphism (unit) | PHPUnit `ProductTest` (11 tests) | — |
| Contract (OpenAPI) | `tests/contract` | — |
| E2E UI | TC-E2E-001..004 | — |
