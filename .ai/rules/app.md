---
paths:
  - 'app/**/*.php'
---

# App

## Eloquent-first, thin controllers, Actions/Services for business logic
Use Eloquent ORM and relationships by default — never raw SQL in controllers. Controllers must be thin: Form Request → Policy → Action/Service → API Resource. All business logic lives in app/Actions/ or app/Services/. Never trust frontend tenant_id — resolve from auth()->user()->tenant_id only. Never use float/double for money — use decimal(20,6). Always eager-load with with() to prevent N+1.
