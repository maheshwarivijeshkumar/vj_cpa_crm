# VJ CPA CRM — Architecture Reference
> Living document. Read this before writing any code.
> Last updated: September 2026 | Laravel 13 + Vue 3 + Inertia.js

---

## 1. Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel | 13.x |
| Language | PHP | 8.3+ |
| Frontend | Vue 3 (Composition API, `<script setup>`) | 3.5.x |
| Router | Inertia.js | 3.x |
| Styling | Tailwind CSS 4 | 4.x |
| Build | Vite | 6.x |
| Auth | Laravel Sanctum | — |
| Queue | Laravel Queue (database driver) | — |
| Cache | Database (dev) / Redis (prod) | — |
| Database | MySQL 8 (dev: same) | — |
| Environment | `.smartfox` (NOT `.env`) | — |

---

## 2. Folder Structure Rules

```
app/
├── Actions/          ← Single-action classes for complex multi-step operations
│   ├── Auth/         ← (future: complex auth operations)
│   ├── Tenant/
│   ├── Subscription/
│   ├── Discount/
│   └── Referral/
├── Contracts/        ← Interfaces for swappable drivers (Mail, Notification, Search)
├── DTOs/             ← Immutable readonly value objects (fromArray, toModelArray)
├── Enums/            ← PHP 8.1 backed enums with label(), badgeClass(), options()
├── Events/           ← Domain events (Dispatchable, SerializesModels)
│   ├── Auth/
│   ├── Filing/
│   ├── Tenant/
│   ├── Subscription/
│   ├── Discount/
│   └── Referral/
├── Exceptions/       ← Handler.php only; custom exceptions per domain
├── Filters/          ← QueryFilter base + domain-specific subclasses
├── Http/
│   ├── Controllers/  ← HTTP-only, zero business logic
│   │   ├── Auth/
│   │   ├── Api/V1/   ← API controllers (thin, delegate to services)
│   │   ├── Platform/ ← Platform admin controllers (Inertia + JSON)
│   │   └── Web/      ← Public marketing/web controllers
│   ├── Middleware/
│   ├── Requests/     ← FormRequest per module (all validation here)
│   │   ├── Auth/
│   │   ├── Platform/
│   │   ├── Subscription/
│   │   ├── Discount/
│   │   ├── Referral/
│   │   └── Web/
│   └── Resources/    ← JsonResource + ResourceCollection per module
│       ├── Auth/
│       ├── Blog/
│       ├── Discount/
│       ├── Platform/
│       ├── Referral/
│       ├── Subscription/
│       └── User/
├── Jobs/             ← ShouldQueue, carry only IDs, failed() logs class name only
├── Listeners/        ← ShouldQueue, one concern per listener
│   ├── Auth/
│   ├── Discount/
│   ├── Filing/
│   ├── Referral/
│   ├── Subscription/
│   └── Tenant/
├── Mail/             ← Extend BaseMailable, use blade templates in resources/views/emails/
├── Models/           ← Eloquent models (relationships, scopes, casts, NO business logic)
├── Notifications/    ← Laravel Notification classes (mail channel only; in-app via NotificationLog)
├── Policies/         ← One policy per model, authorize() in controllers
├── Providers/        ← AppServiceProvider (infra only), AuthServiceProvider, CpaServiceProvider (domain wiring)
├── Repositories/
│   ├── Contracts/    ← Interfaces (extend RepositoryInterface)
│   └── Eloquents/    ← Concrete implementations (extend BaseRepository)
├── Rules/            ← Custom Illuminate\Contracts\Validation\ValidationRule
├── Services/         ← Domain business logic (NEVER in controllers)
│   ├── Audit/        ← AuditService (cross-cutting, called from all services)
│   ├── Auth/         ← LoginService, RegisterService, PasswordResetService, etc.
│   ├── Discount/     ← DiscountService (apply/validate/generate codes)
│   ├── Notification/ ← NotificationService (resolve template, dispatch, log)
│   ├── Platform/     ← PlatformService (stats, tenant management)
│   ├── Referral/     ← ReferralService (track, reward, validate)
│   ├── Seo/          ← SeoService (hierarchical meta resolution)
│   ├── Settings/     ← SettingsService (hierarchical resolution + cache)
│   └── Subscription/ ← SubscriptionService (create, renew, cancel, expire)
└── Support/          ← Stateless helpers (ApiResponse, Money, PaginationHelper, TenantContext)
```

---

## 3. Controller Rules (STRICT)

A controller method may ONLY:
1. Receive a `FormRequest` or `Request`
2. Call `$this->authorize()` (Policy check)
3. Call ONE service method
4. Return an Inertia response, JsonResponse via `ApiResponse::*`, or a redirect

```php
// CORRECT
public function store(StoreDiscountRequest $request): JsonResponse
{
    $discount = $this->discountService->create(
        DiscountData::fromArray($request->validated()),
        $request->user(),
    );
    return ApiResponse::created(new DiscountResource($discount));
}

// WRONG — business logic in controller
public function store(Request $request): JsonResponse
{
    $discount = Discount::create([...]); // NO
    event(new DiscountCreated($discount)); // NO
    DB::table('audit_logs')->insert([...]); // NO
}
```

---

## 4. Service Rules

- All services are `final` classes
- Business logic, DB transactions, event dispatch live HERE
- Use `DB::transaction()` for multi-step writes
- Use `DB::afterCommit()` to dispatch events after commit (broadcast-safe)
- Use `forceFill()->saveQuietly()` for controlled attribute writes
- Dependencies injected via constructor (concrete classes, not interfaces, unless swappable)

---

## 5. Repository Rules

- All repositories extend `Eloquents/BaseRepository`
- All repositories implement their own `Contracts/*RepositoryInterface`
- `BaseRepository` provides: `find`, `findOrFail`, `all`, `paginate`, `create`, `update`, `delete`, `restore`
- Concrete repos override `allowedSortColumns()` and `applyFilters()` (delegated to QueryFilter)
- Filters are applied via `XxxFilters::applyTo($query, $request)` in `applyFilters()`
- **Repository folder**: `app/Repositories/Eloquents/` (namespace: `App\Repositories\Eloquents`)

---

## 6. DTO Rules

- All DTOs are `final readonly class`
- Provide `static fromArray(array $validated): self`
- Provide `toModelArray(): array` (returns only non-null fields)
- Used in services: `ServiceName->method(XxxData::fromArray($request->validated()))`

---

## 7. Enum Rules

Every enum must have:
- `label(): string` — human-readable
- `badgeClass(): string` — CSS badge class (badge-success, badge-warning, etc.)
- `static options(): array` — for frontend dropdowns `['value' => 'label']`

---

## 8. Database Rules (3NF)

- **Primary key**: ULID (`$table->ulid('id')->primary()`) on business tables
- **Money columns**: `DECIMAL(20,6)` — NEVER float/double
- **Reference data FKs**: country_id → countries, currency_id → currencies, timezone_id → timezones, language_id → languages (NEVER store raw strings)
- **Every tenant-owned table**: has `tenant_id` column
- **Soft delete**: standard records only (clients, leads, users, tasks)
- **Immutable**: financial history (journal_entries, payments, invoices) — void/reverse only
- **Permanently immutable**: audit_logs, notification_logs — block update/delete in model boot
- **Indexes**: always add `(tenant_id, created_at)`, `(tenant_id, status)` on tenant-scoped tables

---

## 9. Form Request Rules

- One FormRequest per action (Store, Update — separate classes)
- `authorize()` returns bool based on user type or Policy
- `prepareForValidation()` normalizes inputs (lowercase email, trim strings)
- `messages()` provides user-friendly field-level errors
- `Rule::enum(XxxEnum::class)` for all enum-backed fields
- Custom validation rules go in `app/Rules/`

---

## 10. API Response Contract

Always use `App\Support\ApiResponse` — never `response()->json()` directly:

```php
ApiResponse::success($data, 'message', 200, $meta)
ApiResponse::created($data, 'message')
ApiResponse::noContent('message')
ApiResponse::error('message', 'ERROR_CODE', 400)
ApiResponse::forbidden()
ApiResponse::notFound()
ApiResponse::locked()
ApiResponse::validationError($errors)
ApiResponse::serverError()
```

**Response envelope:**
```json
{
  "success": true,
  "message": "...",
  "data": {},
  "meta": { "current_page": 1, "total": 50, "per_page": 25, ... }
}
```

---

## 11. Frontend Structure Rules

```
resources/js/
├── app.ts                    ← Inertia bootstrap, Pinia, Ziggy
├── layouts/                  ← AppLayout, AuthLayout, MarketingLayout, PlatformLayout, GuestLayout
├── pages/                    ← Inertia page components (match controller render() path)
│   ├── Auth/
│   ├── Blog/
│   ├── Dashboard/
│   ├── Errors/               ← 403.vue, 404.vue, 500.vue, 503.vue
│   ├── Marketing/
│   └── Platform/
├── components/
│   ├── layout/               ← AppNavbar, AppSidebar, AppTopbar
│   └── ui/                   ← Reusable: StatusBadge, EmptyState, LoadingSkeleton, etc.
├── composables/               ← usePermission, useToast, useFlash
├── stores/                   ← Pinia: auth.ts, ui.ts
├── types/                    ← shared.ts (AuthUser, AuthTenant, PageProps)
└── config/
    └── forms/                ← Field configs per module: {module}.ts
        ├── discount.ts
        ├── referral.ts
        └── subscription.ts
```

**Layout assignment:**
- Default (AppLayout): authenticated firm dashboard pages
- `defineOptions({ layout: AuthLayout })`: login/register/etc.
- `defineOptions({ layout: MarketingLayout })`: public marketing pages
- `defineOptions({ layout: PlatformLayout })`: platform admin pages

---

## 12. Color Palette (CPA Brand)

```
--cpa-very-light:   #E6F5F4  (tints, hover fills)
--cpa-light:        #C5E8E5  (hover states, tag backgrounds)
--cpa-medium-light: #8CD3CF  (secondary accents)
--cpa-medium:       #48BCB9  (focus rings, interactive)
--cpa-medium-dark:  #1D9792  (primary CTA, links, active nav)
--cpa-dark:         #055E5A  (sidebar, dark buttons)
--cpa-very-dark:    #023E3C  (active states, hover on dark)
--cpa-bg:           #F4FAFA  (page background)
--cpa-white:        #FEFDFD  (card backgrounds)
--cpa-border:       #D4ECEA  (borders, dividers)
--cpa-text-primary: #0D2B2A  (body text)
--cpa-text-muted:   #6B9294  (muted text)
Font: Inter (Google Fonts, weights 400/500/600/700)
Icons: @lucide/vue (ONLY — no mixing)
```

---

## 13. Tenant Context Rule

**NEVER** trust `tenant_id` from request input:
```php
// CORRECT
$tenantId = auth()->user()->tenant_id;
// OR
$tenantId = TenantContext::id(); // resolved by EnsureTenantContext middleware

// WRONG
$tenantId = $request->input('tenant_id'); // NEVER
```

---

## 14. Event Dispatch Rule

Events that affect financial or broadcast state MUST dispatch AFTER DB commit:

```php
DB::afterCommit(fn () => SubscriptionCreated::dispatch($subscription));
```

Events that are internal domain signals (email, notification) can dispatch inside transaction if they're queued listeners.

---

## 15. Module Registry

Platform-level modules (no business data):

| Code | Name | Status |
|------|------|--------|
| `auth` | Authentication & Authorization | ✅ Complete |
| `platform` | Platform Administration | ✅ Complete |
| `settings` | Hierarchical Settings | ✅ Complete |
| `notifications` | Notification Templates + Log | ✅ Complete |
| `seo` | SEO Meta Management | ✅ Complete |
| `audit` | Audit Logging | ✅ Complete |
| `blog` | Blog / Content | ✅ Complete |
| `subscription` | Tenant Subscriptions | 🔄 In Progress |
| `discount` | Discount Codes | 🔄 In Progress |
| `referral` | Referral & Rewards | 🔄 In Progress |

Business modules (Phase 2+):

| Code | Name | Status |
|------|------|--------|
| `crm` | CRM — Leads | 📋 Planned |
| `clients` | Client Management | 📋 Planned |
| `contacts` | Contacts | 📋 Planned |
| `entities` | Entities | 📋 Planned |
| `services` | Service Catalog | 📋 Planned |
| `engagements` | Engagement Management | 📋 Planned |
| `filings` | Filing Engine | 📋 Planned |
| `taxation` | Taxation Engine | 📋 Planned |
| `deadlines` | Deadline Engine | 📋 Planned |
| `workflows` | Workflow Engine | 📋 Planned |
| `tasks` | Task Management | 📋 Planned |
| `time` | Time Tracking | 📋 Planned |
| `documents` | Document Management | 📋 Planned |
| `accounting` | Double-Entry Accounting | 📋 Planned |
| `invoicing` | Invoicing | 📋 Planned |
| `payments` | Payments | 📋 Planned |

---

## 16. Bootstrap Credentials

```
Platform Admin: administrator@cpacrm.com / administrator90@#$
Demo Firm Owner: owner@demo.cpacrm.com / Owner@Demo2026!
Demo Manager:   manager@demo.cpacrm.com / Manager@Demo2026!
```

---

## 17. Session Commands

When given a command like `PHASE2 clients full`, execute the full vertical slice:

1. Migration (3NF, proper indexes)
2. Eloquent Model (relationships, scopes, casts)
3. Enum(s) for status fields
4. DTO (fromArray, toModelArray)
5. Repository Contract + Eloquent implementation
6. QueryFilter subclass
7. FormRequest(s) (Store, Update)
8. Service (all business logic)
9. Action(s) (complex multi-step if needed)
10. Policy (all gate methods)
11. Events + Listeners
12. Controller (HTTP only, thin)
13. HTTP Resource + Collection
14. Route registration in `routes/api/v1/{module}.php`
15. Vue: Index.vue, Create.vue, Edit.vue, Show.vue (all 3 states: loading/empty/error)
16. Vue: `config/forms/{module}.ts` field definitions
17. Seeder (idempotent)
18. Tests (happy path, tenant isolation, policy gate)

---

## 18. Key Non-Negotiables

1. **Eloquent-first** — raw SQL only for performance-critical aggregations
2. **Never trust frontend `tenant_id`** — resolve from `auth()->user()`
3. **Never use `float`/`double` for money** — always `DECIMAL(20,6)`
4. **Never delete financial history** — void/reverse only
5. **Never hard-code template content** — use NotificationTemplateService
6. **Never hard-code form labels/placeholders** — use `config/forms/{module}.ts`
7. **Never expose internal storage paths** — signed URLs only
8. **Never process large operations synchronously** — queue everything
9. **Always eager-load** — use `with()` to prevent N+1
10. **Every module = full vertical slice** — no half-built modules
