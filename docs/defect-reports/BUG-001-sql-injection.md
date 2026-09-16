# BUG-001 — SQL Injection Through Product SKU

- **Location:** `backend/App/Db/Database.php::exists($param)`
- **Code:**

  ```php
  $query = 'select if(exists(select * from products where sku="'.$param.'"), 1, 0) as result;';
  ```
  
- **Repro (conceptual, do not run against prod):** `POST /` with `{"sku":"\" OR \"1\"=\"1", ...}` alters query logic; error path echoes query.
- **Impact:** auth bypass equivalent / data exfiltration primitive; blocks any security sign-off.
- **Fix (Phase 3, step 10):** prepared statement `WHERE sku = ?` with bound param; remove query echo from `executeQuery()` error path.
- **Regression test (Phase 5):** POST injection SKU → expect 4xx, no row created, table intact; direct `exists()` unit test with malicious input returns 0/1 safely.
- **Status:** OPEN (documented Phase 1, fix queued Phase 3).
