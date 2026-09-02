---
paths:
  - 'app/Services/**/*.php'
---

# Services

## Core services: SettingsService, TemplateResolverService, TaxCalculationService — always use, never bypass
Key services that must be used (never bypass): SettingsService::get('key') for all config resolution (User→Office→Tenant→Platform→Default). TemplateResolverService::resolve('code', tenantId, officeId) for all email/SMS/notification/document templates (Office→Tenant→Platform→System). TaxCalculationService for all tax calculations — never hard-code rates. AuditService for all sensitive and financial events. NotificationService for all outbound notifications via queue.
