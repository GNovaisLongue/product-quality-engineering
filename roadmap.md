# The revised implementation order

## Phase 1 — Understand

**1.** Audit existing application
**2.** Define requirements
**3.** Define domain model
**4.** Identify risks and current defects

## Phase 2 — Architecture

**5.** Introduce `Product` abstraction
**6.** Implement `DVD`, `Book`, `Furniture` polymorphically
**7.** Introduce value objects where appropriate, e.g. `Dimensions`
**8.** Introduce `ProductFactory`
**9.** Separate domain/application/infrastructure/API responsibilities

## Phase 3 — Harden

**10.** Fix SQL injection
**11.** Fix database schema
**12.** Add validation
**13.** Implement proper REST semantics
**14.** Implement structured API responses/errors
**15.** Remove secrets from repository

## Phase 4 — Test Design

**16.** Test strategy
**17.** Test plan
**18.** Equivalence partitioning
**19.** Boundary-value analysis
**20.** Decision tables/state transitions where appropriate
**21.** Traceability matrix
**22.** Defect documentation

## Phase 5 — Automation

**23.** Build reusable test framework
**24.** API tests
**25.** Schema/contract tests
**26.** Database/integration tests
**27.** Unit tests for domain polymorphism
**28.** E2E tests

## Phase 6 — Delivery

**29.** Dockerized test environment
**30.** GitHub Actions
**31.** Smoke suite
**32.** Regression suite
**33.** Test reporting

## Phase 7 — Advanced QA

**34.** JMeter load testing
**35.** Stress testing
**36.** Performance analysis
**37.** QA metrics
**38.** Analytics dashboard

## Phase 8 — Final Touch

**39.** Architecture diagrams
**40.** Final README
**41.** Document discovered defects
**42.** Document architectural decisions
**43.** Document testing decisions
**44.** Add example CI/test reports
