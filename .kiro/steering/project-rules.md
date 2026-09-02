---
inclusion: always
---

# CPA CRM — Project Rules (Always Enforced)

> These rules apply to **every file, every session, every agent**. No exceptions. Read before writing any code.

---

## Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3+, MySQL (dev: SQLite) |
| Frontend | Vue 3 (Composition API `<script setup>`), TypeScript |
| Routing | Inertia.js (server-driven, no separate API for pages) |
| Styling | Tailwind CSS 4 |
| Build | Vite 8 |
| Testing | Pest 4 (backend), Vitest (frontend) |
| Auth | Laravel Sanctum (current) |
| Queue | Laravel Queue + Horizon |
| Cache | Redis |
| Env File | `.smartfox` (replaces `.env` — same format, custom loader) |

---

## 20 Non-Negotiable Rules

### 1. Eloquent-First
Use Eloquent ORM and relationships by default. Raw SQL only with a documented reason (performance bottleneck, complex aggregation, recursive query). Never raw SQL in controllers.

### 2. Never Trust Frontend `tenant_id`
Resolve tenant context exclusively from the authenticated session via middleware. Any `tenant_id` submitted in a request body or query string is ignored for authorization.

### 3. Never Trust Frontend Permissions
Laravel Policies are the final authorization authority. Frontend `can()` checks only control UI visibility.

### 4. Never Use Float for Money
All financial columns must use `DECIMAL(20,6)`. Never `float`, never `double`.

```php
$table->decimal('amount', 20, 6)->default(0);
```

### 5. Never Delete Posted Financial History
Posted journal entries, payments, finalized invoices, and completed filings are **immutable**. Use `Void`, `Reverse`, `Cancel`, or `Archive` workflows instead.

### 6. Never Overwrite Historical Tax Rules
Tax rules used in posted transactions must be versioned. Create `v2` with a new effective date. Old transactions keep their rule version reference.

### 7. Never Hard-Code Template Content
All email, SMS, notification, document, and invoice templates must go through `TemplateResolverService`. The resolution order is: **Office → Tenant → Platform → System fallback**.

### 8. Never Hard-Code Form Labels / Placeholders / Errors
Every form field must be defined in `resources/js/config/forms/{module}.ts`. Components consume config, not inline strings. This enables future localization without touching components.

```typescript
// config/forms/client.ts
export const clientFields = {
  email: {
    name: 'email',
    label: 'Email Address',
    placeholder: 'Enter email address',
    errorPlaceholder: 'Please enter a valid email address',
    type: 'email' as const,
  },
}
```

### 9. Never Expose Internal Notes Through Client APIs
Portal API Resources must never include staff notes, internal comments, risk ratings, or internal health scores. Use separate `PortalClientResource` etc.

### 10. Never Put Business Logic in Controllers
Controllers are thin. They call Form Requests → Policies → Actions/Services. Business rules live in `app/Actions/` or `app/Services/`.

### 11. Never Process Large Operations Synchronously
Imports, exports, PDF generation, bulk notifications, recurring invoice generation, deadline generation, and report queries must be queued.

### 12. Never Allow Duplicate Financial Transactions from Webhooks
Payment webhooks, subscription events, and recurring job triggers must use idempotency keys. Check before creating.

### 13. Never Expose Raw Storage Paths
Every file access goes through a signed URL. `Storage::temporaryUrl()` or equivalent. Never return internal disk paths in API responses.

### 14. Never Use Browser-Native `required` Validation
Do not rely on HTML `required` attributes. Use VeeValidate + Zod on the frontend and Laravel Form Requests on the backend. Errors display inside the input placeholder area.

### 15. Always Audit Sensitive Actions
The following always emit an audit log entry: login, logout, failed login, create, update, delete, restore, import, export, print, approve, reject, financial posting, financial reversal, permission change, settings change, document access, signature event.

### 16. Always Preserve Tenant Context in Queued Jobs
Every queue job that touches tenant data must store and re-resolve tenant context. Never pass raw `tenant_id` and trust it blindly — follow the same resolution pattern as middleware.

### 17. Always Version Templates and Tax Rules
Templates use `Draft → Review → Published → Archived` with version numbers. A published template should not be mutated; create a new version. Tax rules use effective dating.

### 18. Always Prevent N+1 Queries
Use eager loading on every API endpoint that returns collections. Profile with Telescope or Debugbar before adding raw SQL. Common pattern:

```php
Client::query()
    ->with(['contacts', 'entities', 'accountManager'])
    ->withCount('engagements')
    ->paginate();
```

### 19. Every Module Is a Full Vertical Slice
A module is not done until it has: migrations, model, relationships, factory, seeder, form request, policy, action/service, controller, API resource, route file, tests, Vue list/create/edit/view pages, loading/empty/error states, and a Postman collection entry.

### 20. Build Frontend and Backend Together Per Module
Never finish all backend modules first. Each module delivers both layers simultaneously. The UI can use loading/skeleton states while the API is refined, but both must ship together per phase.

---

## Architecture Rules

### Controllers

```php
// CORRECT
public function store(StoreClientRequest $request): JsonResponse
{
    $client = app(CreateClientAction::class)->execute($request->validated());
    return response()->json(['success' => true, 'data' => new ClientResource($client)], 201);
}

// WRONG — business logic in controller
public function store(Request $request): JsonResponse
{
    $client = Client::create([...]);
    Mail::send(...);
    AuditLog::create([...]);
    return response()->json($client);
}
```

### Multi-Tenancy

```php
// CORRECT — resolve from auth context
$tenantId = auth()->user()->tenant_id;

// WRONG — trust request input
$tenantId = $request->input('tenant_id');
```

### Money

```php
// CORRECT
'amount' => 'decimal:20,6'

// WRONG
'amount' => 'float'
'amount' => 'double'
```

### Settings

```php
// CORRECT
$prefix = SettingsService::get('invoice.prefix'); // resolves hierarchy

// WRONG
$prefix = Tenant::find($id)->settings['invoice_prefix'];
```

### Template Resolution

```php
// CORRECT
$template = TemplateResolverService::resolve('invoice.created', $tenantId, $officeId);

// WRONG
$template = Template::where('code', 'invoice.created')->first();
```

---

## API Response Standard

Always use this wrapper. Never return raw Eloquent models from controllers.

```php
// Success
return ApiResponse::success($data, 'Client created.', 201);

// Validation (handled automatically by FormRequest)
// { "success": false, "message": "Validation failed", "errors": {...} }

// Business Error
return ApiResponse::error('Cannot post to a locked period.', 'PERIOD_LOCKED', 423);
```

---

## Testing Minimums Per Module

Every module must have at minimum:

```php
// 1. Happy path CRUD
it('firm owner can create a client')

// 2. Tenant isolation
it('cannot access another tenants client')

// 3. Permission gate
it('staff member cannot delete a client')

// 4. Soft delete + restore (where applicable)
it('deleted client can be restored')

// 5. Financial integrity (accounting modules)
it('journal entry lines must balance')
it('cannot edit a posted journal entry')
it('cannot post to a locked period')
```

---

## Soft Delete Rules

| Record Type | Strategy |
|------------|----------|
| Clients, Leads, Contacts, Tasks, Users | `SoftDeletes` trait |
| Filing Types, Tax Rules, Services (historical) | Archive status (`archived_at`) |
| Journal Entries, Payments, Invoices (posted/finalized) | Immutable — void/reverse only |
| Audit Logs, Signature Certificates | Permanently immutable |

---

## File Naming

| Type | Convention | Example |
|------|-----------|---------|
| PHP Classes | PascalCase | `CreateClientAction.php` |
| PHP Interfaces | PascalCase + Interface | `EmailProviderInterface.php` |
| Vue Components | PascalCase | `ClientDetailCard.vue` |
| Vue Pages | PascalCase | `ClientIndex.vue` |
| TypeScript files | camelCase | `clientFields.ts` |
| Route files | kebab-case | `crm.php` |
| DB migrations | timestamp + description | `2026_09_01_000001_create_clients_table.php` |

---

## Environment (.smartfox)

The project uses `.smartfox` instead of `.env`. It loads automatically via the custom bootstrap loader. Use it exactly like `.env`:

```bash
# .smartfox
APP_NAME="CPA CRM"
APP_ENV=local
DB_CONNECTION=mysql
REDIS_HOST=127.0.0.1
```

Never reference `.env` in new code. Use `.smartfox`. The example file is `.smartfox.example`.

---

## Security Checklist (Per Endpoint)

- [ ] Route protected by `auth:sanctum`
- [ ] Tenant context resolved from auth (not request)
- [ ] Policy `authorize()` called before action
- [ ] Form Request validates all inputs
- [ ] No raw user input concatenated into SQL
- [ ] File uploads: extension + MIME + size validated
- [ ] Signed URLs for any file served
- [ ] Audit log entry created
- [ ] Sensitive data not returned in response (tax IDs masked, internal notes excluded)
