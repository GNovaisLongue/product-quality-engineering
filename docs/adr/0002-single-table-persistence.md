# ADR-0002 — Single-table persistence (domain ≠ database model)

## Context

Polymorphic domain tempts class-table inheritance (one table per subtype).

## Decision

Keep one `products` table, hardened in place: `sku VARCHAR(64) PK`,
`price DECIMAL(10,2) CHECK (price > 0)`, `product_type CHECK (IN ...)`,
`utf8mb4`. Canonical DDL in `database/schema.sql`, Docker init in
`mysql/products.sql` mirrors it.

## Consequences

- No migration risk to seed data/UI; constraints now enforce at SQL level
  what the domain enforces in PHP (defence in depth, verified by TC-DB-*).
- If subtypes diverge further, revisit (e.g. JSON attribute column).
