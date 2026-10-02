# product-quality-engineering

Test project running on Vite + PHP + MySQL + Docker.

## Description

Object-oriented domain modeling with polymorphism + automated quality engineering against a full-stack app (React/Vite + PHP + MySQL + Docker).
Project previously used as a test assignment for a Developer position. The project is a simple CRUD application using Vite, PHP, and MySQL.
Salvaged for QA testing and learning purposes.
**analyse → refactor → harden → test → automate → CI → performance → report.**

## Getting Started

### Dependencies

* Docker
* VSCode or any other IDE
* For local tests, [PHP](https://www.php.net/), [composer](https://getcomposer.org), and [python](https://www.python.org/) are required

### Executing program

```bash
cp .env.example .env.development        # local env (untracked, never commit)
docker compose up --build -d mysql php-apache
curl http://localhost:8000/health
curl http://localhost:8000/api/products  # enveloped list
```

Database interface (optional): `docker compose up --build phpmyadmin` → `http://localhost:8080`
Frontend (optional): `docker compose up --build frontend` → `http://localhost:3000`

To stop and remove everything associated to this project from your docker:

```bash
docker compose down -v --rmi 'all'
```

### Running the tests (no local PHP/Composer/Python needed)

```bash
# PHP domain unit tests
docker compose -f compose.yml -f compose.test.yml run --rm backend-test

# Python suites (smoke/api/contract/db/integration) — needs mysql + php-apache running
docker compose -f compose.yml -f compose.test.yml run --rm qa-test

# E2E — also needs the frontend container running
docker compose -f compose.yml -f compose.test.yml up --build -d frontend
docker compose -f compose.yml -f compose.test.yml run --rm qa-test pytest tests/e2e -q
```

### Running the tests locally, outside the containers

you need to have a local PHP/Composer/Python environment set up, then run the following commands:

```shell
# PHP domain unit (needs composer install)
# (cd backend && composer install && composer test)
cd backend; composer install; composer test

# Python suites (needs live stack; venv + dependencies installed in ./qa)
# Create a virtual environment named `venv`:
python -m venv venv

qa/venv/Scripts/python.exe -m pytest tests/smoke tests/api tests/contract tests/database tests/integration -q --junitxml=reports/junit.xml
# Read results
qa/venv/Scripts/python.exe qa/analytics.py reports/junit.xml
```

E2E needs browsers (`qa/venv/Scripts/playwright.exe install chromium` once), then
`qa/venv/Scripts/python.exe -m pytest tests/e2e` with the stack + frontend up.

### Production

```bash
cp .env.example .env.production   # fill in real, non-root secrets — see the file's comments
docker compose -f compose.yml -f compose.prod.yml up --build -d mysql php-apache frontend
```

`compose.prod.yml` builds the backend's `production` target (no dev deps) and the
frontend's `production` target (static build served by nginx on :80), drops the
dev bind-mounts, and expects a scoped, non-root MySQL user instead of `root`.

## API

| Method | Path | Body | Codes |
| --- | --- | --- | --- |
| GET | `/health` | — | 200 |
| GET | `/api/products` | — | 200 `{success,data,meta}` |
| POST | `/api/products` | product object | 201 / 409 dup / 422 validation / 400 malformed |
| GET | `/api/products/{sku}` | — | 200 / 404 |
| DELETE | `/api/products` | `["SKU",…]` | 200 `{deleted}` / 422 empty |
| DELETE | `/api/products/{sku}` | — | 200 |
| GET | `/` (legacy) | — | 200 bare array |
| POST | `/` (legacy) | object = create, array = bulk delete | 201 / 200 |

## Layout

| Folder/File | Content |
| --- | --- |
| backend/App/{Domain,Application,Infrastructure,Api} | PHP layers |
| database/schema.sql + seed/ | DDL |
| mysql/products.sql | docker init (mirrors schema) |
| openapi/openapi.yaml | contract under test |
| tests/{smoke,api,contract,database,integration,e2e} | pytest suites |
| qa/{clients,data,database,schemas,reporting} + analytics.py | pytest and playwright |
| docs/ | requirements, architecture, and example artifacts |
| .github/workflows/ | tests, regression, nightly |

## Acknowledgments

* [awesome-readme](https://github.com/matiassingers/awesome-readme)
* [README-Template.md](https://gist.github.com/DomPizzie/7a5ff55ffa9081f2de27c315f5018afc)
* [architecture-decision-record](https://github.com/architecture-decision-record/architecture-decision-record)
