# Requirements — product-quality-engineering

## 1. System overview

Full-stack CRUD for products:

```bash
React/Vite (port 3000) → PHP-Apache (port 8000) → MySQL 8.1 (port 9906) + phpMyAdmin (8080)
```

- Frontend: React 18 + Vite 4 + TypeScript + axios + react-router-dom.
- Backend: PHP 8.2 + Apache, PDO + MySQL. Single entry `backend/index.php`.
- DB: single table `products`, seeded with 3 rows.
- Docker: `compose.yml` orchestrates `mysql`, `php-apache`, `phpmyadmin`, `frontend`.

## 2. Actors

- Single user (no auth). Anyone with UI/API access can list, add, mass-delete.

## 3. Functional requirements

### FR-1 List products

- `GET /` returns JSON array of all products.
- UI route `/list-products` renders each product as SKU / Name / Price / type-specific attribute.
- Empty list renders "Found no Products."

### FR-2 Add product

- UI route `/add-product` with form: SKU, Name, Price ($), Type Switcher (DVD | Book | Furniture), type-specific inputs.
- API: `POST /` with JSON object `{sku, name, price, product_type, product_attribute}` inserts one product.
- DVD: `product_attribute` = size in MB, e.g. `"200"`.
- Book: `product_attribute` = weight in KG, e.g. `"200"`.
- Furniture: `product_attribute` = `"HxWxL"`, e.g. `"200x200x200"`.

### FR-3 Mass delete

- UI: checkbox per product (`.delete-checkbox`), `MASS DELETE` button (`#delete-product-btn`) sends array of SKUs.
- API: `POST /` with JSON **array** of SKUs deletes those rows. (DELETE method intentionally unused — legacy 000webhost workaround.)

### FR-4 SKU uniqueness

- Backend rejects duplicate SKU with HTTP 400 + `"SKU already exists!"`.

## 4. Validation rules (as-implemented + intended)

| Field | As-implemented | Required (target) |
| --- | --- | --- |
| SKU | non-empty (frontend only); uniqueness checked backend | required, trimmed, max 64, `[A-Za-z0-9-_]+`, UNIQUE, PK |
| Name | non-empty (frontend only) | required, trimmed, max 255, NOT NULL |
| Price | non-empty + `type=number min=0.01 step=0.01` (frontend only); stored as string | required, numeric, > 0, max 2 decimals, `DECIMAL(10,2)` |
| product_type | defaults to `DVD`, dropdown-constrained frontend | required, enum `DVD\|Book\|Furniture`, NOT NULL |
| DVD.size | non-empty frontend | required, numeric, positive int/float, MB |
| Book.weight | non-empty frontend | required, numeric, positive, KG |
| Furniture H/W/L | non-empty frontend, joined as `HxWxL` | required each, numeric, positive, format `HxWxL` validated server-side |

No server-side validation exists today except duplicate-SKU check. All other rules are frontend-only and bypassable.

## 5. API contract (as-is)

| Method | Path | Body | Success | Failure |
| --- | --- | --- | --- | --- |
| GET | `/` | — | `200` + JSON array | `404` on DB connect fail (plain text + HTML `</br>`) |
| POST | `/` | object | `200` + `"Successfully Inserted!"` (JSON-encoded string) | `400` SKU exists; `"Failed to parse content"` on bad JSON |
| POST | `/` | array (non-empty) | `200` + `"Successfully Deleted!"` | `"DELETE - Empty deletion string"` on `[]` |

No `PUT`, no `GET /{sku}`, no `DELETE` verb, no structured errors, no status-code discipline (`X-PHP-Response-Code` header misused).

## 6. Data model (as-is)

```sql
products(
  sku TEXT NOT NULL,              -- PK prefix (100)
  name VARCHAR(100) NOT NULL,
  price VARCHAR(20) NOT NULL,     -- should be DECIMAL
  product_type VARCHAR(20) NOT NULL,
  product_attribute TEXT NOT NULL -- polymorphic-by-convention
)
```

Seed:

- `('SKUTestSKU000','NameTest000','25','DVD','200')`
- `('SKUTestSKU001','NameTest001','25','Book','200')`
- `('SKUTestSKU002','NameTest002','25','Furniture','200x200x200')`

## 7. Non-functional requirements (target)

- NFR-1 Reproducible Docker env (`docker compose up --build`).
- NFR-2 No secrets in repo (env via example + local untracked file).
- NFR-3 Proper REST semantics + structured JSON errors (target Phase 3).
- NFR-4 Layered backend: Domain / Application / Infrastructure / API (target Phase 2).
- NFR-5 Testable: unit + API + DB + E2E + perf + CI (target Phases 4–6).

## 8. Out of scope (for now)

Auth, pagination, PUT/update, per-SKU GET, search/filter, migrations framework.

## 9. Acceptance criteria for Phase 1

- [x] Audit written
- [x] This requirements doc
- [x] Domain model (`architecture.md`)
- [x] Risks + defects (`risk-analysis.md`, `defect-reports/`)
