---
paths:
  - 'tests/**/*.php'
---

# Tests

## Test minimums per module: CRUD, tenant isolation, permissions, financial integrity
Every module must have at minimum: (1) happy-path CRUD as firm owner, (2) tenant isolation test (cannot access another tenant's data via REST, search, export), (3) permission gate test (staff cannot delete), (4) soft-delete + restore where applicable. Accounting modules additionally need: journal lines must balance, cannot edit posted JE, cannot post to locked period. Use Pest 4. Run: php artisan test. Env: .smartfox (not .env).
