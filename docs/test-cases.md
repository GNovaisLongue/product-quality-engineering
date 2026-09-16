# Test Cases — Phase 4 (steps 18–20)

Conventions: `TC-<AREA>-<NNN>`. Precondition for API tests: stack healthy,
unique SKU via `qa.data.factory`.

## TC-API — CRUD & polymorphism

| ID | Title | Steps | Expected |
| --- | --- | --- | --- |
| TC-API-001 | List returns seed products | GET /api/products | 200, `success=true`, 3 seed SKUs present, each has `product_type` + `product_attribute` |
| TC-API-002 | Create DVD | POST valid DVD | 201, `product_type=DVD`, `product_attribute="500"` |
| TC-API-003 | Create Book | POST valid Book | 201, `Weight` payload preserved |
| TC-API-004 | Create Furniture (H/W/L fields) | POST height/width/length | 201, `product_attribute="HxWxL"` |
| TC-API-005 | Create Furniture (legacy string) | POST `product_attribute="10x20x30"` | 201, parsed to H/W/L |
| TC-API-006 | Get one | POST then GET /{sku} | 200, fields equal |
| TC-API-007 | Delete one | POST then DELETE /{sku} then GET | 200 deleted=1, then 404 |
| TC-API-008 | Bulk delete | POST 2, DELETE /api/products [skus] | 200 deleted=2 |
| TC-API-009 | Get missing | GET /NOPE | 404 `NOT_FOUND` |
| TC-API-010 | Duplicate SKU | POST same SKU twice | 2nd → 409 `CONFLICT` |
| TC-API-011 | Legacy list BC | GET / | 200 bare JSON array |
| TC-API-012 | Legacy create BC | POST / object | 201 envelope (UI navigates on 2xx) |
| TC-API-013 | Legacy bulk delete BC | POST / array | 200 deleted=N |

## TC-VAL — Equivalence partitions + BVA

| ID | Input | Expected |
| --- | --- | --- |
| TC-VAL-001 | DVD size `0` | 422 `size` |
| TC-VAL-002 | DVD size `-3` | 422 |
| TC-VAL-003 | DVD size `"abc"` | 422 |
| TC-VAL-004 | DVD missing size | 422 |
| TC-VAL-005 | Book weight `0` / negative / NaN / missing | 422 (×4) |
| TC-VAL-006 | Furniture `"200x100"` (2 parts) | 422 `dimensions` |
| TC-VAL-007 | Furniture `"200xaxb"` | 422 |
| TC-VAL-008 | Furniture zero segment `"0x10x10"` | 422 |
| TC-VAL-009 | Furniture missing all dims | 422 |
| TC-VAL-010 | Unknown `product_type=Software` | 422 `product_type` |
| TC-VAL-011 | Missing `product_type` | 422 |
| TC-VAL-012 | Empty SKU | 422 `sku` |
| TC-VAL-013 | SKU `65 chars` | 422 (BVA: 64 ok / 65 fail) |
| TC-VAL-014 | SKU `"bad sku!"` | 422 |
| TC-VAL-015 | Price `0` | 422 (BVA: 0 fail / 0.01 pass) |
| TC-VAL-016 | Price `-1` / `"free"` / missing | 422 |
| TC-VAL-017 | Malformed JSON body | 400 `BAD_REQUEST` |
| TC-VAL-018 | Empty object `{}` | 422 or 400 |

## TC-SEC — Security regression (BUG-001 etc.)

| ID | Probe | Expected |
| --- | --- | --- |
| TC-SEC-001 | SKU `" OR "1"="1` | 4xx, no error SQL leak, table intact |
| TC-SEC-002 | SKU `'; DROP TABLE products;--` | 4xx, table intact (list still 200) |
| TC-SEC-003 | `product_attribute` with SQL fragment | stored literally or 422, list intact |
| TC-SEC-004 | Error body inspection | no `SELECT`/`SQLSTATE`/stack in any 4xx/5xx |
| TC-SEC-005 | DELETE `[]` | 422, nothing deleted |

## TC-DB — Schema & persistence

| ID | Check |
| --- | --- |
| TC-DB-001 | `sku` VARCHAR(64) PK, `price` DECIMAL(10,2), `product_type` enum CHECK, `product_attribute` NOT NULL, charset utf8mb4 |
| TC-DB-002 | Round-trip DVD/Book/Furniture: POST → SELECT row → GET → equal attribute |
| TC-DB-003 | Duplicate PK insert at SQL level rejected |
| TC-DB-004 | `price <= 0` rejected by CHECK (SQL level) |

## TC-E2E — UI (Playwright)

| ID | Flow |
| --- | --- |
| TC-E2E-001 | Add DVD via `/add-product` → appears in `/list-products` with `Size: X MB` |
| TC-E2E-002 | Add Book → `Weight: X KG` |
| TC-E2E-003 | Add Furniture → `Dimensions: HxWxL` |
| TC-E2E-004 | Mass delete via checkboxes → removed |

## Decision table (type × attribute shape → status)

| product_type | attribute shape | result |
| --- | --- | --- |
| DVD | numeric size | 201 |
| DVD | missing/zero/negative/alpha | 422 |
| Book | numeric weight | 201 |
| Book | missing/zero/negative/alpha | 422 |
| Furniture | H+W+L fields | 201 |
| Furniture | valid `HxWxL` string | 201 |
| Furniture | malformed/zero-part/missing | 422 |
| *other/missing* | any | 422 |
