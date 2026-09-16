# Risk Analysis — Phase 1 (steps 2–4)

## 1. Risks (likelihood × impact)

| ID | Risk | L × I | Notes / mitigated by |
| --- | --- | --- | --- | --- |
| R-1 | SQL injection via SKU (`Database::exists`) | High × Critical | Fix in Phase 3 (prepared stmt); regression test BUG-001 |
| R-2 | No server-side validation → corrupt data (empty, negative, malformed `HxWxL`, wrong type) | High × High | Domain `validate()` + factory + API 422 (Phase 2–3) |
| R-3 | `price` stored as VARCHAR → sorting/rounding/comparison bugs | Med × High | Schema `DECIMAL(10,2)` + CHECK (Phase 3) |
| R-4 | `sku TEXT + prefix PK` → index bloat, collation/case surprises | Med × Med | `VARCHAR(64) PK`, `utf8mb4` (Phase 3) |
| R-5 | Delete/insert disambiguation by JSON shape (`is_object` vs `is_array`) → accidental deletes, `[]` / scalar edge cases | Med × High | Proper REST verbs + routes (Phase 3) |
| R-6 | Secrets committed (`.env.development` with root/root) | High × High | Remove + `.env.example` only + rotation note (Phase 3, step 15) |
| R-7 | CORS `*` + verbose SQL errors leak internals | Med × Med | Restrict origin, error envelope without SQL (Phase 3) |
| R-8 | No tests / CI → regressions invisible | High × High | Phases 4–6 |
| R-9 | Frontend-only validation bypassable; `alert()`-based error UX, no loading states | High × Low | Keep for E2E negative tests, then improve messages |
| R-10 | Docker dev-only (`npm run dev`, bind mounts, no prod build, obsolete `version` key) | Low × Med | Test env + prod notes (Phase 6, step 29) |

## 2. Current defects (summary → detail in `defect-reports/`)

| ID | Title | Severity |
| --- | --- | --- | --- | --- |
| BUG-001 | SQL injection through `Database::exists()` SKU | Critical |
| BUG-002 | No server-side validation (empty/negative/malformed accepted if API called directly) | High |
| BUG-003 | `price` is VARCHAR(20), no numeric constraint | High |
| BUG-004 | `sku TEXT` + prefix primary key instead of VARCHAR PK | Medium |
| BUG-005 | Non-REST API: POST-overload for delete, no status codes / error envelope, `die()` plain text | Medium |
| BUG-006 | Secrets committed (`.env.development`) | High |
| BUG-007 | Verbose SQL errors + CORS `*` | Medium |

## 3. Test strategy implications (forward ref Phases 4–5)

- Equivalence partitions per subtype (valid / zero / negative / non-numeric / malformed `HxWxL` / missing field / duplicate SKU).
- Boundary: price `0.00/0.01`, size/weight `0`, SKU length limits, `HxWxL` with `0` segment.
- Security regression: `sku: '" OR "1"="1'` must not dump/alter; assert 4xx + no data leak.
- DB contract: DECIMAL enforcement, PK uniqueness, NOT NULL, enum CHECK after hardening.
- E2E polymorphic: DVD→size, Book→weight, Furniture→H/W/L each create + appear with correct suffix.
