# Enterprise CPA CRM — Kiro Master Development Guide

> **Version:** 1.0  
> **Created:** 2026-09-01  
> **Purpose:** Single source of truth for all development sessions, phases, commands, and rules.  
> **Project:** mycpacrm.com — Enterprise CPA CRM, Practice Management & Accounting Platform  

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack (Actual)](#2-technology-stack-actual)
3. [Brand & Design System](#3-brand--design-system)
4. [Architecture Overview](#4-architecture-overview)
5. [Multi-Tenant Architecture](#5-multi-tenant-architecture)
6. [Development Phases](#6-development-phases)
7. [Parallel Workstreams](#7-parallel-workstreams)
8. [Session Command Reference](#8-session-command-reference)
9. [Module Registry](#9-module-registry)
10. [Database Standards](#10-database-standards)
11. [API Standards](#11-api-standards)
12. [Frontend Standards](#12-frontend-standards)
13. [Security Standards](#13-security-standards)
14. [Non-Negotiable Rules](#14-non-negotiable-rules)
15. [Definition of Done](#15-definition-of-done)
16. [Bootstrap Credentials](#16-bootstrap-credentials)
17. [File & Folder Conventions](#17-file--folder-conventions)

---

## 1. Project Overview

Build a **production-grade, enterprise-level, multi-tenant CPA CRM, Accounting Practice Management, Tax/Compliance, Workflow, Document Management, Billing, Accounting, Client Portal, Communications, Reporting and Automation SaaS platform with AI Chatbot**.

The platform must feel like a premium international CPA practice-management product — original design, no copying of competitors' branding or proprietary assets.

Reference: **mycpacrm.com** — use as inspiration for scope, layout patterns and feature completeness. Do not copy proprietary UI.

### Core Characteristics

- Enterprise-grade
- Multi-tenant (every accounting firm = one tenant)
- API-first
- Accounting-safe (double-entry, immutable posting history)
- Taxation-aware (country-neutral, rule-versioned)
- Fully auditable
- Modular with feature flags
- Configuration-driven (not hard-coded)
- Permission-driven (RBAC, Policies)
- Responsive & Accessible (WCAG 2.2 AA target)
- Scalable, Testable, Localization-ready

---

## 2. Technology Stack (Actual)

> ⚠️ **Important:** The docs specify Next.js but the **actual project** uses **Laravel + Inertia.js + Vue 3**. All development follows the actual stack.

### Backend

| Item | Choice |
|------|--------|
| Framework | Laravel 13.x |
| Language | PHP 8.3+ |
| Database | MySQL (primary) / SQLite (dev/test) |
| Auth | Laravel Sanctum → migrate to Passport for OAuth |
| Queue | Laravel Queue + Horizon |
| Scheduler | Laravel Scheduler |
| Cache | Redis |
| Storage | Local (dev) → S3-compatible (production) |
| Real-time | Laravel Multiplex / Pusher |
| Search | Database FTS → Meilisearch (future) |
| Testing | Pest + PHPUnit |
| Code Style | Pint (PSR-12) |
| Static Analysis | PHPStan (Larastan) |
| API Docs | Swagger/OpenAPI |
| AI Chat | Configurable provider (OpenAI/Gemini abstraction) |

### Frontend

| Item | Choice |
|------|--------|
| Framework | Vue 3 (Composition API, `<script setup>`) |
| Router | Inertia.js (server-driven routing) |
| Language | TypeScript |
| Build | Vite 8 |
| Styling | Tailwind CSS 4 |
| Components | shadcn-vue / Radix Vue |
| State | Pinia (replaces Zustand mentioned in docs) |
| Forms | VeeValidate + Zod |
| Tables | TanStack Table Vue |
| Charts | Chart.js / Vue-Chartjs or Recharts-equivalent |
| Icons | Lucide Vue |
| Drag & Drop | Vue Draggable / dnd-kit equivalent |
| Animation | GSAP / Framer Motion Vue (where UX warrants) |
| Rich Text | TipTap |

### DevOps & Tooling

| Item | Choice |
|------|--------|
| Dev Server | `php artisan serve` + `npm run dev` (concurrently) |
| Env File | `.smartfox` (replaces `.env` — see §17) |
| Linting | ESLint 9 + Prettier |
| API Testing | Postman collection (generated per module) |
| Module Routes | Per-module route files (see §17) |

---

## 3. Brand & Design System

### Color Palette

```
--cpa-very-light:   #E6F5F4   (backgrounds, tints)
--cpa-light:        #C5E8E5   (hover states, badges)
--cpa-medium-light: #8CD3CF   (secondary accents)
--cpa-medium:       #48BCB9   (interactive elements)
--cpa-medium-dark:  #1D9792   (primary actions, links)
--cpa-dark:         #055E5A   (headers, sidebar)
--cpa-very-dark:    #023E3C   (deep accents, active states)
--cpa-text-secondary: #4D7374 (muted text)
--cpa-bg:           #F4FAFA   (page background)
--cpa-white:        #FEFDFD   (card backgrounds)
```

### Design Personality

Calm · Focused · Balanced · Premium · Professional · Trustworthy · Modern · Clean · Financial · Enterprise

### Typography

- **Primary Font:** Inter (Google Fonts)
- Page Title: 28–32px / 600
- Section Heading: 20–24px / 600
- Card Heading: 16–18px / 600
- Body: 14–16px / 400
- Label: 13–14px / 500
- Helper: 12–13px / 400

### Icons

- **Library:** Lucide Vue (only)
- Consistent stroke width, aligned sizes
- 16px table/actions · 18px inputs/buttons · 20px nav · 24px cards · 28–32px dashboard highlights

### Avoid

- Excessive gradients · Neon colors · Over-rounded cartoon UI
- Heavy shadows · Excessive animations · Cluttered dashboards

---

## 4. Architecture Overview

```
CPA PLATFORM
│
├── Laravel API (Backend)
│   ├── Identity & Access
│   ├── Multi-Tenancy
│   ├── CRM (Leads, Clients, Contacts, Entities)
│   ├── Engagement & Practice Management
│   ├── Filing & Deadline Engine
│   ├── Taxation Engine
│   ├── Workflow & Task Engine
│   ├── Time Tracking & Capacity
│   ├── Document Management
│   ├── Template Engine
│   ├── Communications (Email, SMS, Messaging)
│   ├── Notification System
│   ├── Scheduling & Calendar
│   ├── Proposals & E-Signatures
│   ├── Accounting (Double-Entry GL)
│   ├── Banking & Reconciliation
│   ├── Invoicing, Payments, AR/AP
│   ├── Reports & Analytics
│   ├── Import / Export Engine
│   ├── Audit Logging
│   ├── Subscription / SaaS Billing
│   ├── Integrations & Webhooks
│   └── AI Assistant
│
└── Vue 3 + Inertia Frontend
    ├── Public Marketing Website
    ├── Authentication
    ├── Platform Super Admin
    ├── Firm Administration
    ├── Partner / Manager Dashboard
    ├── Accountant / Bookkeeper Workspace
    ├── Staff Workspace
    └── Client Portal
```

### Request Flow

```
Browser (Vue 3 + Inertia)
  → Inertia Request / Axios API call
  → Laravel Router
  → Middleware (auth, tenant, throttle)
  → Form Request (validation)
  → Policy (authorization)
  → Controller (thin)
  → Action / Service (business logic)
  → Eloquent Models + Relationships
  → Database Transaction
  → Domain Event
  → Queue / Listener (async work)
  → Notification / Integration
  → Audit Log
  → API Resource / Inertia Response
```

---

## 5. Multi-Tenant Architecture

Every accounting firm = one tenant.

### Rules

1. Every tenant-owned table has `tenant_id` column
2. **Never** trust `tenant_id` from the frontend
3. Tenant context resolved from authenticated user in middleware
4. Global Eloquent scopes enforce isolation
5. Cache keys prefixed: `tenant:{id}:*`
6. File paths: `tenant/{uuid}/...`
7. Queue jobs carry tenant context (not raw IDs)

### Hierarchy

```
Platform
  └── Tenant (Accounting Firm)
      ├── Offices
      ├── Users
      ├── Clients → Entities → Engagements
      ├── Filings → Tax Profiles
      ├── Accounting → GL → AR/AP
      ├── Documents → Templates
      ├── Workflows → Tasks
      ├── Notifications
      └── Settings
```

### Settings Resolution Order

```
User Preference
  → Office Setting
    → Tenant Setting
      → Platform Setting
        → System Default
```

Use `SettingsService::get('key')` — never read settings directly.

---

## 6. Development Phases

> Phases are **logical milestones**. Implementation proceeds in **parallel workstreams** (§7). Frontend and backend are developed simultaneously per module.

---

### Phase 1 — Foundation *(Start Here)*

**Goal:** Working authenticated multi-tenant application skeleton with design system.

| Track | Backend | Frontend |
|-------|---------|----------|
| B | Laravel API structure, routing, middleware | — |
| B | Authentication (Sanctum → Passport) | Login, Register, Password Reset, 2FA pages |
| B | Multi-tenancy middleware + global scopes | — |
| B | RBAC: Roles, Permissions, Policies | Permission helper `can('module.action')` |
| B | Module registry + feature flags | — |
| B | Settings service (hierarchical) | — |
| B | Audit logging foundation | — |
| B | Standard API response format | API service layer |
| F | — | Design system: colors, typography, Tailwind config |
| F | — | Layout: sidebar, topbar, breadcrumbs, notifications bell |
| F | — | Base UI components (Button, Input, Select, Modal, Drawer, Table, Badge, StatCard) |
| F | — | Platform Super Admin shell |
| F | — | Firm dashboard shell |
| B+F | Platform administrator seeder | Platform admin login + dashboard |

**Deliverables:**
- Working login → dashboard flow
- Tenant-isolated API
- Design system documented
- Base component library

---

### Phase 2 — CRM

**Goal:** Full lead-to-client conversion pipeline.

| Module | Backend | Frontend |
|--------|---------|----------|
| Leads | migrations, model, CRUD API, pipeline stages, activities | Kanban board, list, create/edit forms, activity timeline |
| Clients | full profile, contacts, addresses, tags, custom fields | Client detail page, 360° view |
| Contacts | person/org model, relationships | Contact management |
| Entities | tax entities, entity types, relationships | Entity profiles |
| Services | service catalog CRUD | Service management |
| Onboarding | onboarding workflow stages | Onboarding dashboard widget |

**Deliverables:**
- Lead → Client → Entity → Engagement conversion flow
- Full 360° client profile
- Service catalog

---

### Phase 3 — Practice Management

**Goal:** Core CPA practice engine.

| Module | Backend | Frontend |
|--------|---------|----------|
| Engagements | types, team, billing, status history | Engagement manager, letters |
| Filings | filing engine, type config, assignments | Filing list, status board, detail |
| Deadlines | rule engine, weekend/holiday adj, jurisdictions | Deadline calendar, heatmap |
| Workflows | template engine, triggers, actions, execution log | Workflow builder (drag & drop) |
| Tasks | hierarchy, dependencies, checklists, recurrence | Task board (Kanban), list |
| Time Tracking | timers, manual entry, approvals, locking | Timesheet, utilization charts |
| Capacity | team availability, workload, alerts | Capacity dashboard |
| Calendar | appointment types, booking, reminders | Calendar view (month/week/day) |

---

### Phase 4 — Taxation Engine

**Goal:** Production-grade, country-neutral taxation system.

| Module | Backend | Frontend |
|--------|---------|----------|
| Tax Profiles | multi-jurisdiction, identifiers, residency | Client tax profile |
| Jurisdictions | country/state/province config | Platform tax config |
| Tax Authorities | authority management | — |
| Tax Types/Categories | VAT, GST, WHT, custom | — |
| Tax Rules | versioned rules, effective dating | Tax rule editor |
| Tax Rates | rate management with history | — |
| Exemptions | client/product/jurisdiction exemptions | — |
| Tax Transactions | every taxable event linked | Transaction viewer |
| Tax Returns | return workflow, submission tracking | Return management UI |
| Tax Obligations | recurring obligation generation | Obligation calendar |
| Tax Reports | liability, transactions, returns | Tax report suite |

---

### Phase 5 — Document Management

**Goal:** Secure, version-controlled document system.

| Module | Backend | Frontend |
|--------|---------|----------|
| Storage | tenant-aware paths, signed URLs | — |
| Folders | hierarchy, permissions | Folder tree |
| Documents | upload, metadata, MIME validation | Document browser |
| Versioning | version history, rollback | Version history panel |
| Requests | checklist, due dates, client upload | Document request UI |
| Sharing | secure links, expiration | Share management |
| Retention | policies, legal hold | Retention settings |
| Recycle Bin | soft delete, restore, purge | Deleted files UI |
| E-Signatures | multi-signer, order, fields, audit cert | Signature request builder |

---

### Phase 6 — Accounting

**Goal:** Production double-entry accounting foundation.

| Module | Backend | Frontend |
|--------|---------|----------|
| Chart of Accounts | hierarchical, system accounts | CoA manager |
| Fiscal Years | custom periods, open/close/lock | Period management |
| Journal Entries | debit=credit validation, approval, posting | JE editor |
| General Ledger | derived from posted JEs | GL viewer |
| Trial Balance | real-time | Trial balance report |
| P&L | income statement | P&L report |
| Balance Sheet | — | Balance sheet report |
| Cash Flow | — | Cash flow report |
| Multi-Currency | exchange rates, realized G/L | Currency settings |

---

### Phase 7 — Billing

**Goal:** Full AR/AP and invoicing.

| Module | Backend | Frontend |
|--------|---------|----------|
| Proposals/Quotations | workflow, acceptance, conversion | Proposal builder |
| Engagement Letters | template-driven | Letter generator |
| Invoices | full lifecycle, recurring, multi-currency | Invoice manager |
| Payments | allocation, partial, refunds, write-offs | Payment recording |
| Credit Notes | — | Credit note UI |
| AR Aging | — | AR aging report |
| Expenses | employee/vendor, approval, billable | Expense management |
| Vendors | vendor management, bills | Vendor/AP management |
| AP Aging | — | AP aging report |

---

### Phase 8 — Banking

**Goal:** Bank reconciliation and transaction management.

| Module | Backend | Frontend |
|--------|---------|----------|
| Bank Accounts | multi-account, multi-currency | Account management |
| Transactions | manual import, CSV, OFX | Transaction list |
| Categorization | rules, auto-match | Categorization UI |
| Reconciliation | matching, unreconciled queue | Reconciliation UI |

---

### Phase 9 — Templates & Communications

**Goal:** Event-driven communication with template hierarchy.

| Module | Backend | Frontend |
|--------|---------|----------|
| Template Engine | platform/tenant/office hierarchy, versioning | Template editor (rich text) |
| Email Templates | all event types, variable system | Email template list |
| SMS Templates | — | SMS template list |
| Notification Templates | in-app, severity, action URLs | Notification template list |
| Document Templates | engagement letters, proposals, invoices | Document template editor |
| Messaging | secure conversations, read state | Messaging inbox |
| Notifications | in-app bell, preferences, digests | Notification center |
| Email Delivery | queue, tracking, history | Email log viewer |

---

### Phase 10 — Reporting, Analytics & AI

**Goal:** Comprehensive analytics and AI capabilities.

| Module | Backend | Frontend |
|--------|---------|----------|
| Practice Reports | client growth, filing completion, utilization | Dashboard charts |
| Tax Reports | liability, obligations, jurisdiction breakdown | Tax report suite |
| Financial Reports | all accounting reports | Financial report suite |
| Staff Analytics | productivity, utilization, workload | Staff dashboard |
| Scheduled Reports | delivery, export | Report scheduler |
| Import Engine | CSV/XLSX, validation, preview, queue | Import wizard |
| Export Engine | CSV/XLSX/PDF, async, signed URL | Export manager |
| AI Assistant | client summary, missing docs, draft comms | AI chat panel |
| Webhooks | endpoints, delivery, retry, logs | Webhook management |

---

## 7. Parallel Workstreams

Each session command triggers work across both backend and frontend simultaneously.

```
Track A — Platform Foundation      (Auth, RBAC, Tenant, Settings, Modules)
Track B — CRM                      (Leads, Clients, Contacts, Entities)
Track C — Practice Management      (Engagements, Filings, Deadlines, Workflows, Tasks)
Track D — Taxation                 (Profiles, Rules, Transactions, Returns)
Track E — Accounting               (CoA, GL, AR, AP, Banking)
Track F — Templates & Communications
Track G — Documents & E-Signatures
Track H — Client Portal
Track I — Frontend Design System   (Components, Layout, Forms, Charts)
Track J — Reporting & Analytics
Track K — Integrations             (Email, SMS, Storage, Calendar, Payments)
Track L — QA & Security            (Tests, Audit, Performance, Accessibility)
```

### Per-Module Vertical Slice (Always Complete All Layers)

```
1. Migration + DB schema
2. Model + Eloquent relationships
3. Factory + Seeder
4. Form Request (validation)
5. Policy (authorization)
6. Action / Service (business logic)
7. Events + Listeners
8. Queue Jobs (async)
9. API Controller (thin)
10. API Resource
11. Route file (module-based)
12. OpenAPI documentation
13. Unit + Feature + Tenant isolation tests
14. Frontend API service (TypeScript)
15. Pinia store (if needed)
16. Vue components (List, Create, Edit, View, Delete)
17. Loading / Empty / Error states
18. Import / Export (where applicable)
19. Postman collection entry
20. Audit logging
```

---

## 8. Session Command Reference

When given a session command, execute the full vertical slice for that module.

### Format

```
[PHASE] [MODULE] [ACTION]
```

### Examples

```
PHASE1 foundation setup               → Bootstrap auth, tenancy, RBAC, design system
PHASE2 leads full                     → Full leads vertical slice (BE + FE)
PHASE2 clients full                   → Full clients vertical slice
PHASE3 engagements full               → Full engagements vertical slice
PHASE4 tax-rules full                 → Tax rules with versioning
PHASE6 journal-entries full           → Journal entry engine
build fresh                           → Clear build artifacts, rebuild
postman generate [module]             → Generate Postman collection for module
seed dev                              → Run development seeders
seed demo                             → Run demo data seeders
```

### Before Each Session

1. Review last completed module
2. Check that all 20 vertical slice layers are complete for previous module
3. Proceed to next module in phase order

---

## 9. Module Registry

Seed this data in `ModuleSeeder.php`.

| Code | Name | Core | Dependencies |
|------|------|------|-------------|
| `crm` | CRM | yes | — |
| `clients` | Client Management | yes | crm |
| `contacts` | Contacts | yes | clients |
| `entities` | Entities | yes | clients |
| `engagements` | Engagement Management | yes | clients |
| `services` | Service Catalog | yes | — |
| `filings` | Filing Engine | yes | clients, entities |
| `taxation` | Taxation Engine | yes | clients, entities, filings |
| `deadlines` | Deadline Engine | yes | filings |
| `workflows` | Workflow Engine | yes | — |
| `tasks` | Task Management | yes | — |
| `time` | Time Tracking | no | engagements |
| `capacity` | Capacity Planning | no | time |
| `documents` | Document Management | yes | clients |
| `esignatures` | E-Signatures | no | documents |
| `templates` | Template Engine | yes | — |
| `communications` | Communications | yes | clients |
| `notifications` | Notifications | yes | templates |
| `calendar` | Scheduling & Calendar | no | — |
| `proposals` | Proposals & Quotations | no | clients, services |
| `accounting` | Accounting Engine | yes | clients |
| `banking` | Banking | no | accounting |
| `invoicing` | Invoicing | yes | clients, services, accounting |
| `payments` | Payments | yes | invoicing |
| `expenses` | Expenses | no | accounting |
| `reports` | Reports & Analytics | yes | — |
| `portal` | Client Portal | no | clients, documents, communications |
| `imports` | Import Engine | yes | — |
| `exports` | Export Engine | yes | — |
| `ai` | AI Assistant | no | — |
| `subscriptions` | Subscription Billing | yes | — |
| `webhooks` | Webhooks | no | — |

---

## 10. Database Standards

### Identifiers

- Public-facing: `ULID` (preferred) or `UUID`
- Internal keys: `bigint` (auto-increment)
- Use `$table->ulid('id')->primary()` as default

### Financial Columns

```sql
-- NEVER use float or double for money
amount DECIMAL(20, 6) NOT NULL DEFAULT 0
```

### Required Columns (tenant tables)

```sql
tenant_id BIGINT UNSIGNED NOT NULL
created_at TIMESTAMP
updated_at TIMESTAMP
deleted_at TIMESTAMP NULL  -- only where soft-delete applies
```

### Common Indexes

```sql
INDEX (tenant_id, created_at)
INDEX (tenant_id, status)
INDEX (tenant_id, client_id)
INDEX (tenant_id, due_date)
INDEX (tenant_id, is_active)
```

### Naming

- Tables: `snake_case`, plural (e.g., `journal_entry_lines`)
- Columns: `snake_case`
- Pivots: alphabetical order (e.g., `engagement_user`)
- Foreign keys: `{table_singular}_id`

### Soft Delete vs Archive vs Immutable

| Category | Strategy | Examples |
|----------|----------|----------|
| Standard records | `SoftDeletes` trait | clients, leads, tasks, users |
| Historical config | Archive status field | filing_types, tax_rules, services |
| Financial records | Reversal / Void (immutable) | journal_entries, payments, invoices |
| Compliance records | Immutable | audit_logs, signature_certificates |

---

## 11. API Standards

### Base URL

```
/api/v1/
```

### Response Format

**Success:**
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {},
  "meta": { "pagination": {} }
}
```

**Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { "field": ["message"] }
}
```

**Business Error:**
```json
{
  "success": false,
  "message": "Cannot post to a locked period.",
  "code": "PERIOD_LOCKED",
  "errors": {}
}
```

### HTTP Status Codes

| Status | Use |
|--------|-----|
| 200 | Success |
| 201 | Created |
| 204 | No Content (delete) |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |
| 423 | Locked (financial period) |
| 429 | Rate Limited |
| 500 | Server Error |

### Architecture Rules

- Controllers are thin — no business logic
- All mutations use Form Requests
- All responses use API Resources
- All authorization uses Policies
- Business logic lives in Actions/Services
- Events trigger async work via Listeners/Jobs

### Route Organization

Each module has its own route file:

```
routes/
├── auth.php
├── platform.php
├── api/
│   ├── v1/
│   │   ├── crm.php
│   │   ├── clients.php
│   │   ├── engagements.php
│   │   ├── filings.php
│   │   ├── taxation.php
│   │   ├── accounting.php
│   │   ├── documents.php
│   │   ├── templates.php
│   │   ├── notifications.php
│   │   ├── invoicing.php
│   │   ├── payments.php
│   │   ├── banking.php
│   │   ├── reports.php
│   │   ├── settings.php
│   │   ├── users.php
│   │   ├── imports.php
│   │   ├── exports.php
│   │   └── portal.php
```

---

## 12. Frontend Standards

### Component Architecture

```
resources/js/
├── app.ts                          ← Inertia bootstrap
├── bootstrap.ts
├── types/
│   ├── global.d.ts
│   ├── inertia.d.ts
│   └── models/                     ← TypeScript model types
├── lib/
│   ├── utils.ts
│   ├── api.ts                      ← Axios instance
│   └── permissions.ts              ← can() helper
├── stores/                         ← Pinia stores
│   ├── auth.ts
│   ├── tenant.ts
│   └── notifications.ts
├── composables/                    ← Reusable Vue composables
│   ├── useDataTable.ts
│   ├── useForm.ts
│   ├── usePermission.ts
│   └── useToast.ts
├── components/
│   ├── ui/                         ← Base design system components
│   │   ├── Button.vue
│   │   ├── Input.vue
│   │   ├── Select.vue
│   │   ├── DataTable.vue
│   │   ├── Modal.vue
│   │   ├── Drawer.vue
│   │   ├── Badge.vue
│   │   ├── StatCard.vue
│   │   ├── EmptyState.vue
│   │   ├── LoadingSkeleton.vue
│   │   └── ErrorState.vue
│   ├── layout/
│   │   ├── AppSidebar.vue
│   │   ├── AppTopbar.vue
│   │   ├── AppBreadcrumb.vue
│   │   └── NotificationDrawer.vue
│   └── shared/
│       ├── AuditTimeline.vue
│       ├── FileUploader.vue
│       └── CommandPalette.vue
├── features/
│   ├── auth/
│   ├── crm/
│   │   ├── leads/
│   │   │   ├── api/
│   │   │   ├── components/
│   │   │   └── pages/
│   │   └── clients/
│   ├── engagements/
│   ├── filings/
│   ├── taxation/
│   ├── accounting/
│   ├── documents/
│   ├── templates/
│   ├── invoicing/
│   ├── payments/
│   ├── reports/
│   └── settings/
├── pages/
│   ├── Auth/
│   ├── Platform/
│   ├── Dashboard/
│   ├── Portal/
│   └── Marketing/
└── config/
    ├── navigation.ts               ← Permission-aware nav config
    ├── forms/                      ← Centralized field configs
    │   ├── client.ts
    │   ├── lead.ts
    │   ├── filing.ts
    │   └── invoice.ts
    └── permissions.ts              ← Permission constants
```

### Form Field Configuration (Mandatory)

Every form field must be defined in `config/forms/{module}.ts`:

```typescript
export const clientFields = {
  first_name: {
    name: 'first_name',
    label: 'First Name',
    placeholder: 'Enter first name',
    errorPlaceholder: 'Please enter first name',
    type: 'text' as const,
  },
  email: {
    name: 'email',
    label: 'Email Address',
    placeholder: 'Enter email address',
    errorPlaceholder: 'Please enter a valid email address',
    type: 'email' as const,
  },
}
```

### Validation UX

- **Do not** use HTML `required` attribute as validation
- Error message replaces placeholder text inside the input
- Field border turns error-colored
- First invalid field receives focus
- `aria-invalid="true"` and `aria-describedby` set on error

### Permission Helper

```typescript
// lib/permissions.ts
export const can = (permission: string): boolean => {
  const auth = useAuthStore()
  return auth.permissions.includes(permission)
}

// Usage in templates
<Button v-if="can('clients.create')">Add Client</Button>
```

### Data Tables

Every listing page must support:
- Search, sort, filter, advanced filters, date range
- Column visibility toggle, saved views
- Pagination (10/25/50/100)
- Row selection + bulk actions
- Import, Export, Print buttons (permission-gated)
- Loading skeleton, Empty state with action, Error state

---

## 13. Security Standards

### Authentication

- Email + password with rate limiting (5 attempts → lockout)
- Email verification required
- Password reset via signed email link
- MFA/2FA (TOTP)
- Session management with device listing
- Force logout all sessions
- Security event audit logging

### Authorization

- Every API route protected by `auth:sanctum` middleware
- Every action gated by a Policy (`authorize()` in controller)
- Frontend permissions control visibility only
- Backend is always the final authority

### File Security

- Validate extension + MIME + size on every upload
- Store outside `public/` web root
- Use signed URLs for every file access
- Virus scanning integration architecture (pluggable)
- Tenant-aware storage paths

### Data Protection

- Financial money: `decimal(20,6)` — never float
- Tax identifiers: encrypted at rest where required
- Audit logs: immutable, not user-editable
- No raw SQL with user input concatenation
- Parameter binding always

---

## 14. Non-Negotiable Rules

These rules are enforced in every session. No exceptions.

1. **Eloquent-first.** Use ORM relationships as default. Raw SQL only with documented reason.
2. **Never trust frontend tenant_id.** Resolve from authenticated session.
3. **Never trust frontend permissions.** Backend Policy is always the authority.
4. **Never use float for money.** Use `decimal(20,6)`.
5. **Never delete posted financial history.** Void or reverse instead.
6. **Never overwrite historical tax rules** used in posted transactions. Version them.
7. **Never hard-code template content** throughout the codebase. Use TemplateResolverService.
8. **Never hard-code form labels/placeholders/errors** in components. Use `config/forms/`.
9. **Never expose internal notes** through portal/client API Resources.
10. **Never put complex business logic in controllers.** Use Actions/Services.
11. **Never process large imports/exports synchronously.** Queue them.
12. **Never allow duplicate webhook-driven financial transactions.** Use idempotency keys.
13. **Never expose raw storage paths.** Use signed URLs.
14. **Never use browser-native `required` validation** as the app's validation system.
15. **Always audit** every sensitive and financial action.
16. **Always preserve tenant context** in queued jobs and cache.
17. **Always version** templates and tax rules where historical consistency matters.
18. **Always prevent N+1.** Use eager loading (`with()`, `load()`).
19. **Every module = full vertical slice.** No half-built modules.
20. **Build frontend + backend together** per module, not backend-first then frontend.

---

## 15. Definition of Done

A module is **not complete** until all 40 items are checked:

### Backend (20)
- [ ] Migrations with FK constraints and indexes
- [ ] Eloquent model with all relationships
- [ ] Factory with realistic fake data
- [ ] Seeder (idempotent, uses `firstOrCreate`)
- [ ] Form Request(s) with full validation
- [ ] Policy with all permission checks
- [ ] Action/Service class(es) for business logic
- [ ] Events + Listeners (where needed)
- [ ] Queue Jobs (async operations)
- [ ] API Controller (thin, delegates to Action)
- [ ] API Resource(s) (no raw model returns)
- [ ] Route file entry (module route file)
- [ ] OpenAPI documentation block
- [ ] Unit tests (business logic)
- [ ] Feature tests (API endpoints)
- [ ] Tenant isolation test
- [ ] Permission policy test
- [ ] Soft delete / restore test (where applicable)
- [ ] Financial integrity test (where applicable)
- [ ] Audit logging active

### Frontend (20)
- [ ] TypeScript model type in `types/models/`
- [ ] API service function in `features/{module}/api/`
- [ ] Form field config in `config/forms/{module}.ts`
- [ ] Zod validation schema
- [ ] Pinia store slice (if state needed)
- [ ] List/Index page with DataTable
- [ ] Create page with validated form
- [ ] Edit page with pre-filled form
- [ ] View/Detail page
- [ ] Delete/Archive behavior with confirm dialog
- [ ] Restore behavior (where applicable)
- [ ] Import page/dialog (where applicable)
- [ ] Export button with async feedback (where applicable)
- [ ] Print layout (where applicable)
- [ ] Loading skeleton state
- [ ] Empty state with relevant action
- [ ] Error state with retry
- [ ] Permission-gated UI (`v-if="can(...)"`)
- [ ] Responsive layout (mobile + desktop)
- [ ] Postman collection entry updated

---

## 16. Bootstrap Credentials

### Platform Administrator

```
Email:    administrator@cpacrm.com
Password: administrator90@#$  (NEVER store plaintext — use Hash::make())
Role:     Platform Super Administrator
```

**Production:** Use environment variables:
```
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
```
Password must be changed on first login (`must_change_password = true`).

### Development Tenant

```
Firm:     Demo Accounting Firm
Email:    firm@cpacrm.com
Password: firm_demo_2026
Role:     Firm Owner
```

---

## 17. File & Folder Conventions

### .smartfox (Environment File)

The project uses `.smartfox` instead of `.env`.

```
# .smartfox — Application Environment
# Loaded by: bootstrap/app.php custom loader
# Never commit real credentials
# Format: KEY=VALUE (same as .env)
```

Reference in code exactly as you would `.env` — the custom loader maps it transparently.

**Files:**
- `.smartfox` — actual environment (gitignored)
- `.smartfox.example` — committed example

### Route Files

Module-based route organization:

```
routes/
├── auth.php              ← login, register, password reset, 2FA
├── platform.php          ← platform admin routes (inertia)
├── dashboard.php         ← firm dashboard routes (inertia)  
├── portal.php            ← client portal routes (inertia)
├── web.php               ← marketing website
└── api/
    └── v1/
        ├── crm.php
        ├── clients.php
        ├── entities.php
        ├── engagements.php
        ├── services.php
        ├── filings.php
        ├── taxation.php
        ├── deadlines.php
        ├── workflows.php
        ├── tasks.php
        ├── time.php
        ├── calendar.php
        ├── documents.php
        ├── templates.php
        ├── communications.php
        ├── notifications.php
        ├── proposals.php
        ├── signatures.php
        ├── accounting.php
        ├── banking.php
        ├── invoicing.php
        ├── payments.php
        ├── expenses.php
        ├── reports.php
        ├── imports.php
        ├── exports.php
        ├── users.php
        ├── settings.php
        └── portal.php
```

### Backend Folder Structure

```
app/
├── Actions/
│   ├── CRM/
│   ├── Clients/
│   ├── Engagements/
│   ├── Taxation/
│   ├── Accounting/
│   └── ...
├── Events/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── V1/
│   │   │       ├── CRM/
│   │   │       ├── Clients/
│   │   │       ├── Accounting/
│   │   │       └── ...
│   │   └── Platform/
│   ├── Middleware/
│   │   ├── EnsureTenantContext.php
│   │   └── HandleInertiaRequests.php
│   ├── Requests/
│   │   ├── CRM/
│   │   ├── Clients/
│   │   └── ...
│   └── Resources/
│       ├── CRM/
│       ├── Clients/
│       └── ...
├── Jobs/
├── Listeners/
├── Models/
│   ├── CRM/
│   ├── Accounting/
│   └── ...
├── Policies/
├── Providers/
├── Services/
│   ├── TaxCalculationService.php
│   ├── TemplateResolverService.php
│   ├── SettingsService.php
│   ├── NotificationService.php
│   ├── AuditService.php
│   └── ...
└── Support/
    ├── Money.php
    ├── TenantContext.php
    └── ...
```

### Postman Collection

One collection per module, organized in folders:

```
CPA CRM API/
├── Auth/
├── Platform/
├── CRM - Leads/
├── CRM - Clients/
├── Engagements/
├── Filings/
├── Taxation/
├── Accounting/
├── Documents/
├── Templates/
├── Invoicing/
├── Payments/
├── Reports/
└── Settings/
```

Export after each module is complete.

---

## Appendix A — Seeder Order

```
1. SystemSettingsSeeder
2. ModuleSeeder
3. PermissionSeeder
4. RoleSeeder
5. PlatformAdministratorSeeder
6. FeatureFlagSeeder
7. DefaultWorkflowSeeder
8. DefaultFilingTypeSeeder
9. DefaultChartOfAccountsSeeder
10. DefaultTaxSeeder
11. DefaultTemplateSeeder
12. DefaultNotificationSeeder
--- Development only ---
13. DevelopmentSeeder (demo tenant, sample data)
14. DemoSeeder (full demo dataset)
```

---

## Appendix B — Critical Tests

### Double-Entry Accounting

```
Create JE → Validate debit=credit → Approve → Post
→ Lock Period → Attempt Edit → Reject
→ Create Reversal → Verify Ledger → Verify Audit
```

### Tax Rule Versioning

```
Create Rule v1 → Post Transaction → Use v1
→ Create Rule v2 (future effective date)
→ Post new Transaction → Uses v2
→ Old transaction still shows v1
→ Generate Tax Return → Correct aggregation
```

### Template Hierarchy

```
Office template → resolves to office
Remove office → resolves to tenant
Remove tenant → resolves to platform
```

### Tenant Isolation

```
Tenant A creates Client A
Tenant B creates Client B
Tenant A cannot access Client B (REST, search, export, queue, file, cache)
```

---

## Appendix C — Navigation Structure

### Platform Admin

```
Dashboard · Tenants · Subscriptions · Plans · Modules
· Feature Flags · Users · Templates · Tax Config
· System Settings · Audit Logs · Support
```

### Firm Dashboard

```
Dashboard · CRM (Leads, Clients, Entities)
· Engagements · Services · Filings · Taxation
· Deadlines · Workflows · Tasks · Time · Calendar
· Documents · Templates · Communications
· Appointments · Proposals · Signatures
· Accounting · Banking · Invoices · Payments
· Expenses · Vendors · Reports · Team · Offices
· Imports · Exports · Settings
```

### Client Portal

```
Dashboard · Profile · Tax Profile · Filings
· Documents · Messages · Appointments
· Proposals · Engagements · Signatures
· Invoices · Payments · Notifications
```

---

*End of Kiro Master Development Guide — v1.0*
