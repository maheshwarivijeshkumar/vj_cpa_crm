---
paths:
  - 'database/migrations/**/*.php'
---

# Migrations

## Migration standards: tenant_id, ULID PK, decimal money, composite indexes
Every tenant-owned table must have tenant_id (bigint unsigned, indexed). Use ULID as primary key ($table->ulid('id')->primary()). Money columns: decimal(20,6) — never float. Composite indexes: (tenant_id, created_at), (tenant_id, status), (tenant_id, client_id). Soft-delete only on standard records (clients, leads, tasks). Financial records are immutable — no soft delete on journal_entries, payments, invoices.
