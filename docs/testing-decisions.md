# Testing Decisions (Phase 8, step 43)

1. **Fix-then-prove, not rewrite-then-pray.** Phase 1 defects were kept as
   executable evidence: each BUG maps to TCs that failed (conceptually) before
   the fix and pass after (traceability matrix).
2. **Validation in two layers.** Domain `validate()` (user-friendly 422) +
   SQL CHECKs (last-resort integrity). Tests hit both levels independently.
3. **Legacy BC as a contract.** `GET /` bare array and `POST /` overload are
   pinned by `tests/api/test_legacy.py`, so the shipped React UI keeps working
   while new clients use `/api/products`.
4. **Fail-closed SKU rule.** Allow-list regex + 64-char cap rejects injection
   payloads before they reach SQL; prepared statements make the second layer.
5. **Isolation via unique SKUs + best-effort cleanup** instead of DB resets —
   suites are order-independent and re-runnable against a live stack.
6. **Skips are explicit.** E2E skips without browsers; perf runs nightly only.
   `test-summary-report.md` states what ran, not just what passed.
