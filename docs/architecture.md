# Architecture & Domain Model — Phase 1 Understand

## 1. Audit: how the app actually works

### 1.1 Request flow (as-is)

```bash
ProductForm (React)
  → builds IProductProps {sku,name,price,product_type,product_attribute:string}
  → axiosService.post → POST http://localhost:8000/
    → backend/index.php switch(REQUEST_METHOD)
      → POST + object → new Product → setX() → insertProduct()
        → Database::exists(sku) → Database::insert([...])
      → POST + array  → Product::deleteProduct(array)
        → Database::delete(skus)
      → GET           → Product::getAllProducts()
        → Database::select()->fetchAll(PDO::FETCH_ASSOC) → die(json_encode(...))
```

### 1.2 Frontend (`vite/src/`)

- `main.tsx`: routes `/` → `/list-products`, `/list-products`, `/add-product`.
- `pages/`: thin wrappers over `Components/ProductsList` and `Components/ProductForm`.
- `Components/ProductsList`: `getAll()` on mount, checkbox state `productsDeletion: string[]`, `remove(skus)` then `navigate(0)` refresh. No loading/error state (errors only `console.log`).
- `Components/ProductsList/ProductItem`: string-switch on `product_type` to prefix attribute (`Size: X MB` / `Weight: X KG` / `Dimensions: HxWxL`).
- `Components/ProductForm`: uncontrolled refs for sku/name/price + `prodType`/`prodAttr` state. Empty-check only, then joins Furniture as `${h}x${w}x${l}`. `handlePost` → `navigate(-1)` after 600ms.
- `Components/ProductForm/ProductType`: dropdown + dynamic inputs from `model/data/FetchData.ts` (DVD=size, Book=weight, Furniture=height/width/length). Number inputs frontend-only.
- `model/ProductProps.ts`: flat `IProductProps` (all strings) + unused discriminated `ProductAttributes` type.
- `model/Product.ts`: `abstract class Product implements IProductProps` — abstract in name only; no subclasses, no polymorphism; `createProduct`/`deleteProduct` are thin axios wrappers with `alert()` on error.
- `api/axios/`: `AxiosConfig` (baseURL from `VITE_DOMAIN_DB`), `AxiosService` (`getAll` GET /, `post` POST /, `remove` POST / with array — DELETE deliberately unused).

### 1.3 Backend (`backend/`)

- `index.php`: CORS `*`, method switch GET/POST only. Distinguishes insert vs delete by `is_object` vs `is_array` on decoded JSON — fragile (empty array, scalar, malformed all edge cases). Uses `die()` for every response; no JSON error envelope; no content-type header.
- `App/Model/Product.php`: anemic model — 5 private fields + getters/setters, no validation, no behavior. `insertProduct()` checks `exists()` then inserts; `getAllProducts()` / `deleteProduct()` are static pass-throughs to `Database`.
- `App/Db/Database.php`: PDO wrapper. `insert()` and `delete()` correctly use prepared placeholders. **`exists($param)` interpolates directly**: `'... where sku="'.$param.'"'` → SQL injection (BUG-001). `select()` concatenates `$where/$order/$limit` raw. `executeQuery()` leaks raw SQL + exception message in response body. Connection credentials via `getenv('PHP_*')`.
- `composer.json`: PSR-4 `App\` only, no dependencies, no dev/test tooling.
- `Dockerfile`: two-stage (composer:2.5 → php:8.2-apache), installs `pdo_mysql`, copies `index.php` + `App/`. No opcache tuning, no healthcheck, commented-out rewrite config.

### 1.4 Database (`mysql/products.sql`)

- Single table, no FKs. `sku TEXT NOT NULL` with `PRIMARY KEY (sku(100))` prefix index — should be `VARCHAR(64..191) PRIMARY KEY`. `price VARCHAR(20)` — should be `DECIMAL(10,2)`. No `NOT NULL` gaps otherwise OK, but no `UNIQUE` beyond PK, no `CHECK` (price > 0, type enum), charset `utf8_unicode_ci` (legacy; prefer `utf8mb4`).
- Seed: 3 rows, one per type.

### 1.5 Docker / config

- `compose.yml`: `mysql:8.1` (9906:3306, volume `mysql_data` + `./mysql:/docker-entrypoint-initdb.d`), `php-apache` (build `./backend`, 8000:80, bind-mount source), `phpmyadmin:5` (8080:80), `frontend` (build `./vite`, `npm run dev`, 3000:3000, polling watch). Networks `apache-db` + `react-apache`. Healthcheck on mysql. `version: "4.9"` key obsolete (Compose v2 warns).
- `.env.development` committed with `root/root` credentials and used as `env_file` for 3 services. `.gitignore` ignores `.env` but **not** `.env.development` / `.env.example` nuance — secrets effectively in repo (BUG-006).

## 2. Domain model

### 2.1 Current (implicit, stringly-typed)

```bash
Product { sku:string, name:string, price:string, product_type:string, product_attribute:string }
```

`product_attribute` meaning resolved by `product_type` at every consumer (frontend form join, ProductItem display, no backend check). Classic "type code + uninterpreted string" smell.

### 2.2 Target — polymorphism

```bash
                    Product (abstract)
                    /      |       \
                 DVD     Book    Furniture
                  |        |          |
                size:    weight:   dimensions:
                float    float     Dimensions{height,width,length: float}
```

- `Product`: `sku, name, price: Decimal`, `abstract validate(): void`, `abstract getAttribute(): mixed`, `abstract getType(): string`.
- `Dimensions` value object: `__toString(): "HxWxL"`, `fromString()`, own validation (positive numerics).
- `ProductFactory::create(array|Request): Product` — single place converting `product_type` + raw attribute into subtype. Controller never `if DVD / if Book`.
- Architecture layers:

```bash
Domain:          Product, DVD, Book, Furniture, Dimensions, ProductRepository (interface)
Application:     CreateProduct, DeleteProduct(s), ListProducts (use cases)
Infrastructure:  MySQLProductRepository, DatabaseConnection (PDO)
API:             ProductController, Request/Response DTOs, ProductFactory, error envelope
```

## 3. Decisions deferred to Phase 2/3

- Keep single `products` table (vs. class-table inheritance) — simplest, preserves seed + UI.
- REST shape: `GET/POST /api/products`, `DELETE /api/products` (bulk) + `DELETE /api/products/{sku}`, optional `GET /{sku}` + `PUT`. Legacy `POST`-with-array kept temporarily behind controller for BC, then removed.
- `price` as string in API, `DECIMAL` in DB, float/string boundary validated in domain.
