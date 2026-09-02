---
paths:
  - 'app/Models/**/*.php'
---

# Models

## Model relationships, scopes, and immutability rules
Every model must define all meaningful Eloquent relationships (belongsTo, hasMany, morphMany etc). Create reusable scopes: scopeForTenant(), scopeActive(), scopeOverdue(). Models with financial history (JournalEntry, Payment, Invoice) are immutable after posting — no delete, use void/reverse. SoftDeletes only on: Client, Lead, Contact, Task, User, DocumentFolder, Office. Archive via status field for: FilingType, TaxRule, Service, WorkflowTemplate.
