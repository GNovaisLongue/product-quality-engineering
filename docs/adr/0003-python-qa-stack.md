# ADR-0003 — Python + pytest for QA automation (not PHP)

## Context

Application is PHP/React; test language was a free choice.

## Decision

Python + pytest + requests + PyMySQL for API/contract/DB/integration;
PHPUnit only for domain unit tests; Playwright for E2E.

## Consequences

- Tests exercise the system as a black box over HTTP/SQL — same stance as an
  independent QA team; no shared code with the implementation.
- Reusable `qa/` package (client, data factory, DB helper, contract asserts,
  analytics) works against any environment via `API_BASE_URL` / `MYSQL_*`.
- Cost: two toolchains in CI (PHP for unit, Python for the rest).
