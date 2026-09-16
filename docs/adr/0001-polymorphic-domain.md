# ADR-0001 — Polymorphic product domain

## Context

`product_attribute` was an uninterpreted string whose meaning came from
`product_type` elsewhere (form join, display switch, no backend check).

## Decision

Abstract `Product` with `DVD` / `Book` / `Furniture` subtypes, `Dimensions`
value object, single `ProductFactory` for external ↔ domain translation.
Persistence stays one relational table; the factory maps rows ↔ objects.

## Consequences

- Validation lives with the data it validates; controllers never branch on type
  (Open/Closed: adding `Software` = new class + factory arm, no controller edits).
- Existing consumers untouched: legacy `GET /` shape preserved; UI unchanged.
- Tests mirror the hierarchy (unit per subtype + API decision table).
