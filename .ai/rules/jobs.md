---
paths:
  - 'app/Jobs/**/*.php'
---

# Jobs

## Queue jobs: tenant context preservation and idempotency on financial jobs
All queue jobs touching tenant data must carry tenant context and re-resolve it safely (never rely on raw tenant_id passed as constructor param without re-validating). Large imports, exports, PDF generation, bulk notifications, recurring invoices, deadline generation, and report queries must be queued — never synchronous. Use idempotency keys on financial jobs (payment webhooks, recurring invoices) to prevent duplicate transactions.
