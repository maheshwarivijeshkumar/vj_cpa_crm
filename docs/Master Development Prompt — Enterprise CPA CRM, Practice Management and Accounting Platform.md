# Enterprise CPA CRM, Practice Management & Accounting Platform

## Project Objective

Build a **production-grade, enterprise-level CPA CRM, Accounting Practice Management, Client Portal, Workflow, Filing, Document Management, and Financial Management SaaS platform**.

The system should provide functionality comparable to modern CPA practice management platforms while introducing more advanced capabilities, a stronger accounting architecture, better workflow automation, extensibility, security, and scalability.

Do **not** copy another company's branding, proprietary assets, UI, content, or source code.

Create an original product with a premium enterprise SaaS design.

---

# 1. Technology Architecture

## Frontend

Use **one single Next.js application** for all frontend experiences.

The same project must contain:

- Public marketing website
- Authentication
- Platform Super Admin
- Firm Administration
- Partner dashboard
- Accountant dashboard
- Staff dashboard
- Bookkeeper dashboard
- Client portal
- External secure portals where required

Do not create separate Next.js repositories for each portal.

Use:

- Next.js
- React
- TypeScript
- App Router
- Tailwind CSS
- shadcn/ui or equivalent reusable component architecture
- TanStack Query
- TanStack Table
- Zustand
- React Hook Form
- Zod
- dnd-kit
- Recharts
- Framer Motion where appropriate

Use a feature-based architecture.

---

## Backend

Use:

- Laravel
- PHP 8.4+
- Mysql/PostgreSQL
- Redis
- Laravel Queues
- Laravel Horizon
- Laravel Scheduler
- S3-compatible object storage
- REST API
- Docker
- OpenAPI / Swagger documentation

The backend must be API-first.

---

# 2. Application Architecture

```text
CPA PLATFORM
│
├── Next.js Application
│   │
│   ├── Public Website
│   ├── Authentication
│   ├── Platform Administration
│   ├── Firm Dashboard
│   ├── Staff Workspace
│   ├── Accounting Workspace
│   └── Client Portal
│
└── Laravel API
    │
    ├── Identity & Access
    ├── Multi-Tenancy
    ├── CRM
    ├── Client Management
    ├── Filing Engine
    ├── Workflow Engine
    ├── Accounting Engine
    ├── General Ledger
    ├── Documents
    ├── Communications
    ├── Notifications
    ├── Scheduling
    ├── Reporting
    ├── Import / Export
    ├── Audit Logging
    └── Integrations
```

---

# 3. Next.js Route Architecture

Use route groups.

```text
app/
│
├── (marketing)/
│   ├── page.tsx
│   ├── features/
│   ├── solutions/
│   ├── pricing/
│   ├── integrations/
│   ├── security/
│   ├── resources/
│   ├── contact/
│   └── about/
│
├── (auth)/
│   ├── login/
│   ├── register/
│   ├── forgot-password/
│   ├── reset-password/
│   ├── verify-email/
│   └── two-factor/
│
├── (platform)/
│   └── platform/
│       ├── dashboard/
│       ├── tenants/
│       ├── subscriptions/
│       ├── plans/
│       ├── feature-flags/
│       ├── users/
│       ├── audit-logs/
│       ├── system-settings/
│       └── support/
│
├── (dashboard)/
│   └── app/
│       ├── dashboard/
│       ├── clients/
│       ├── leads/
│       ├── engagements/
│       ├── services/
│       ├── filings/
│       ├── workflows/
│       ├── tasks/
│       ├── calendar/
│       ├── documents/
│       ├── communications/
│       ├── appointments/
│       ├── proposals/
│       ├── signatures/
│       ├── accounting/
│       ├── banking/
│       ├── invoices/
│       ├── payments/
│       ├── reports/
│       ├── team/
│       ├── offices/
│       ├── imports/
│       ├── exports/
│       └── settings/
│
└── (portal)/
    └── portal/
        ├── dashboard/
        ├── profile/
        ├── filings/
        ├── documents/
        ├── messages/
        ├── appointments/
        ├── proposals/
        ├── signatures/
        ├── invoices/
        └── payments/
```

---

# 4. Multi-Tenant Architecture

The system must support multiple accounting firms.

Every firm is a tenant.

Each tenant must have isolated:

- Users
- Clients
- Entities
- Filings
- Accounting data
- Documents
- Workflows
- Roles
- Offices
- Settings
- Branding
- Integrations

Every tenant-owned table must contain:

```text
tenant_id
```

Tenant isolation must be enforced through:

- Global scopes
- Middleware
- Policies
- Services
- Queue jobs
- Cache keys
- File storage paths
- API authorization

Never trust `tenant_id` sent from the frontend.

The authenticated tenant context must determine tenant ownership.

---

# 5. Seeded Platform Administrator

Create a proper `PlatformAdministratorSeeder`.

Seed the initial administrator using the following credentials:

```text
Username: administrator
Email: administrator@cpacrom.com
Password: administrator90@#$
```

The password must **never be stored as plain text**.

Use Laravel hashing:

```php
Hash::make('administrator90@#$')
```

The seeded administrator must receive:

```text
Role:
Platform Super Administrator
```

The administrator must have full system access.

---

# 6. Administrator Seeder Requirements

Create:

```text
database/seeders/
│
├── DatabaseSeeder.php
├── PlatformAdministratorSeeder.php
├── RoleSeeder.php
├── PermissionSeeder.php
├── ModuleSeeder.php
├── FeatureFlagSeeder.php
├── DefaultWorkflowSeeder.php
├── DefaultFilingTypeSeeder.php
├── DefaultChartOfAccountsSeeder.php
├── DefaultTaxSeeder.php
└── SystemSettingsSeeder.php
```

The main `DatabaseSeeder` must execute seeders in the correct order.

Example:

```text
1. System Settings
2. Modules
3. Permissions
4. Roles
5. Platform Administrator
6. Feature Flags
7. Default Workflows
8. Filing Types
9. Chart of Accounts
10. Tax Configuration
```

All seeders must be idempotent.

Running:

```bash
php artisan db:seed
```

multiple times must not create duplicate:

- Roles
- Permissions
- Administrator accounts
- Modules
- System settings
- Default workflows
- Chart of accounts

Use:

```php
firstOrCreate()
updateOrCreate()
```

where appropriate.

---

# 7. Platform Administrator Permissions

The Platform Super Administrator should have all permissions.

However, permissions must still exist as explicit database records.

Do not bypass the entire authorization system with scattered checks.

Use a clean authorization strategy.

Example:

```text
users.*
roles.*
permissions.*

tenants.*
subscriptions.*
plans.*
feature_flags.*

clients.*
leads.*
engagements.*

filings.*
filing_types.*
deadlines.*

workflows.*
tasks.*

documents.*
document_templates.*

communications.*
messages.*
emails.*
sms.*

appointments.*

proposals.*
signatures.*

accounting.*
chart_of_accounts.*
journal_entries.*
bank_accounts.*
reconciliation.*

invoices.*
payments.*
expenses.*

reports.*

imports.*
exports.*
print.*

settings.*
modules.*

audit_logs.*

soft_deletes.*
restore.*
force_delete.*
```

The Platform Super Administrator receives every permission.

---

# 8. Permission Categories

Each major module must support the following permissions where applicable:

```text
view
view_any
create
update
delete
restore
force_delete
import
export
print
approve
reject
assign
archive
manage
```

Example:

```text
clients.view
clients.view_any
clients.create
clients.update
clients.delete
clients.restore
clients.force_delete
clients.import
clients.export
clients.print
clients.manage
```

The frontend may hide actions based on permissions.

However, the backend must always enforce authorization.

---

# 9. Soft Delete Architecture

Implement soft deletes carefully.

Do not blindly apply soft deletes to every table.

There are three categories.

---

## Category A — Standard Soft Delete

These records may use Laravel SoftDeletes:

- Clients
- Leads
- Contacts
- Tags
- Custom fields
- Tasks
- Workflow templates
- Workflow stages where not historically required
- Document folders
- Services
- Appointment types
- Proposal templates
- Users where business rules allow
- Offices
- Departments
- Teams

These records must support:

```text
Delete
Restore
View Deleted
Force Delete
```

Only authorized users may restore or permanently delete.

---

## Category B — Archive Instead of Delete

Use archival status rather than deletion for records such as:

- Filing types
- Chart of account definitions
- Tax rules
- Service definitions already used historically
- Workflow definitions with historical records

Use:

```text
status
is_active
archived_at
archived_by
```

Do not destroy historical references.

---

## Category C — Immutable / Reversal-Based Records

Do not soft delete finalized financial and compliance records.

Examples:

- Posted journal entries
- General ledger entries
- Reconciled transactions
- Payments
- Finalized invoices
- Completed e-signatures
- Audit logs
- Deadline history
- Filing status history

Instead support:

```text
Void
Reverse
Supersede
Correct
Cancel
Archive
```

Every reversal must create a historical audit trail.

Example:

```text
Journal Entry #JE-1001
        ↓
Cannot Delete
        ↓
Create Reversing Journal Entry
        ↓
JE-1002
```

Never silently remove financial history.

---

# 10. Deleted Records Management

Create a central deleted records module.

Route:

```text
/app/settings/deleted-records
```

Features:

- View deleted records
- Filter by module
- Filter by deleted user
- Filter by date
- Search
- Restore
- Bulk restore
- Permanent deletion where permitted

Display:

- Record name
- Module
- Deleted by
- Deleted at
- Original owner
- Related records

Permanent deletion must require confirmation.

For sensitive records, require:

```text
Type DELETE
```

or equivalent confirmation.

All restore and force-delete actions must be logged.

---

# 11. Audit Trail

Create a complete audit system.

Track:

- Login
- Logout
- Failed login
- Create
- Update
- Delete
- Restore
- Force delete
- Import
- Export
- Print
- Approval
- Rejection
- Assignment
- Financial posting
- Financial reversal
- Permission changes
- Settings changes

Audit record:

```text
id
tenant_id
user_id
event_type
module
resource_type
resource_id
description
old_values
new_values
ip_address
user_agent
created_at
```

`old_values` and `new_values` should use JSONB in PostgreSQL.

Audit logs must not be editable from the normal application.

---

# 12. Accounting System

The platform must include a proper accounting foundation.

This is not simply an expense tracker.

Implement a double-entry accounting architecture.

---

# 13. Chart of Accounts

Create a configurable chart of accounts.

Account categories:

```text
Assets
Liabilities
Equity
Revenue
Cost of Revenue
Expenses
Other Income
Other Expenses
```

Account fields:

```text
id
tenant_id
parent_id
account_code
account_name
account_type
account_subtype
normal_balance
description
currency_id
is_system
is_active
archived_at
created_at
updated_at
```

Support hierarchical accounts.

Example:

```text
1000 Assets
│
├── 1100 Current Assets
│   ├── 1110 Cash
│   ├── 1120 Bank Account
│   └── 1130 Accounts Receivable
│
├── 1200 Fixed Assets
│
2000 Liabilities
3000 Equity
4000 Revenue
5000 Expenses
```

Do not allow deletion of an account with posted transactions.

Allow archival.

---

# 14. Fiscal Year and Accounting Periods

Create:

```text
fiscal_years
accounting_periods
```

Features:

- Custom fiscal year start
- Monthly periods
- Quarterly periods
- Annual periods
- Open period
- Soft close
- Hard close
- Locked period

A posted transaction must not be edited inside a locked period.

To correct a transaction in a locked period:

```text
Create adjusting entry
```

Do not modify historical entries.

---

# 15. Journal Entry System

Create a proper journal entry engine.

Tables:

```text
journal_entries
journal_entry_lines
```

Each entry must contain:

```text
Entry Number
Date
Reference
Description
Status
Currency
Exchange Rate
Created By
Posted By
Posted At
```

Each line:

```text
Account
Description
Debit
Credit
Tax
Client
Entity
Cost Center
Project
Department
```

Validation:

```text
SUM(debit) = SUM(credit)
```

Never use floating-point numbers for money.

Use PostgreSQL numeric/decimal fields.

Example:

```text
numeric(20, 6)
```

or a carefully defined money precision standard.

---

# 16. Journal Entry Statuses

Support:

```text
Draft
Pending Approval
Approved
Posted
Reversed
Voided
```

Rules:

- Draft can be edited
- Posted cannot be edited directly
- Posted cannot be deleted
- Reversal creates a reversing entry
- Voiding requires authorization and audit logging

---

# 17. General Ledger

Generate the general ledger from posted journal entries.

Do not manually edit ledger balances.

The ledger must be derived from transaction history.

Support:

- Account ledger
- Client ledger
- Entity ledger
- Department ledger
- Project ledger
- Cost center ledger

Reports:

- General Ledger
- Trial Balance
- Balance Sheet
- Profit and Loss
- Cash Flow
- Account Activity

---

# 18. Accounts Receivable

Create:

```text
customers
invoices
invoice_items
credit_notes
payments
payment_allocations
```

Invoice statuses:

```text
Draft
Sent
Viewed
Partially Paid
Paid
Overdue
Cancelled
Voided
```

Do not delete finalized invoices.

Support:

- Credit notes
- Refunds
- Write-offs
- Partial payments
- Payment allocation
- Multi-currency

---

# 19. Accounts Payable and Expenses

Create support for:

- Vendors
- Bills
- Expenses
- Expense categories
- Payment schedules
- Approvals

Statuses:

```text
Draft
Pending Approval
Approved
Partially Paid
Paid
Overdue
Voided
```

---

# 20. Banking

Create:

```text
bank_accounts
bank_transactions
bank_reconciliations
reconciliation_lines
```

Support:

- Manual transaction import
- CSV import
- OFX architecture
- Future bank API integrations
- Transaction matching
- Categorization
- Reconciliation

Reconciled transactions must not be deleted.

Corrections must follow controlled reversal or unreconciliation workflows.

---

# 21. Multi-Currency

The platform should support:

- Base currency per tenant
- Transaction currency
- Exchange rates
- Historical exchange rate
- Realized gain/loss
- Unrealized gain/loss

Example:

```text
Firm Base Currency: USD

Transaction:
CAD 1,000

Exchange Rate:
1 CAD = 0.74 USD
```

The exchange rate used for a posted transaction must be preserved historically.

---

# 22. Tax Engine

Create a configurable tax architecture.

Support:

- VAT
- GST
- Sales tax
- Withholding tax
- Provincial/state tax
- Custom taxes

Do not hard-code one country.

Allow configuration by:

```text
Country
State
Province
Jurisdiction
Effective Date
```

Tax rules must support effective dating.

Historical transactions must preserve the tax rule/version used at posting time.

---

# 23. Client and Entity Architecture

Separate:

```text
Person
Organization
Tax Entity
Contact
Relationship
```

A person can be connected to multiple entities.

Example:

```text
John Smith
│
├── Individual Tax Client
│
├── Director
│   └── ABC Corporation
│
├── Shareholder
│   └── XYZ Holdings
│
└── Trustee
    └── Smith Family Trust
```

Do not duplicate the same person unnecessarily.

Create a flexible relationship model.

---

# 24. CRM and Lead Management

Create a full CRM pipeline.

Stages:

```text
New
Contacted
Qualified
Meeting Scheduled
Proposal Sent
Negotiation
Won
Lost
```

Support:

- Lead sources
- Activities
- Notes
- Calls
- Emails
- Meetings
- Follow-ups
- Assignment
- Conversion

Conversion:

```text
Lead
↓
Client
↓
Engagement
↓
Services
↓
Filings / Accounting Setup
```

---

# 25. Filing Engine

The filing system must be configurable.

A filing record should support:

```text
Client
Entity
Filing Type
Tax Year
Period
Jurisdiction
Due Date
Extended Due Date
Priority
Workflow
Status
Assigned Users
Reviewer
Approver
Documents
Tasks
Notes
History
```

Do not permanently delete completed filing history.

Support cancellation or archival.

---

# 26. Advanced Deadline Engine

Deadline rules must support:

- Calendar year
- Fiscal year
- Entity type
- Jurisdiction
- Filing frequency
- Extension
- Weekend adjustment
- Holiday adjustment
- Custom offsets

Architecture:

```text
Deadline Rule
       ↓
Determine Entity
       ↓
Determine Filing Period
       ↓
Apply Rule
       ↓
Apply Extension
       ↓
Weekend Adjustment
       ↓
Holiday Adjustment
       ↓
Final Deadline
```

Every generated deadline should store the rule/version used.

---

# 27. Workflow Automation Engine

Create a reusable automation engine.

Trigger examples:

```text
Client Created
Filing Created
Stage Changed
Document Uploaded
Task Completed
Deadline Approaching
Proposal Accepted
Payment Received
Appointment Completed
```

Actions:

```text
Create Task
Assign User
Send Email
Send SMS
Create Reminder
Move Workflow
Request Document
Create Filing
Create Journal Draft
Create Notification
Call Webhook
```

Architecture:

```text
Trigger
↓
Conditions
↓
Actions
↓
Execution Log
```

Every automation execution must be logged.

---

# 28. Import System

Create a reusable import engine.

Supported modules:

- Clients
- Contacts
- Leads
- Entities
- Filings
- Tasks
- Services
- Chart of Accounts
- Vendors
- Customers
- Transactions
- Users where authorized

Support:

- CSV
- XLSX

Import process:

```text
Upload
↓
Detect Columns
↓
Map Fields
↓
Validate
↓
Preview
↓
Show Errors
↓
Confirm
↓
Queue Import
↓
Import Result
```

Never perform a large import synchronously.

Use queue jobs.

Create:

```text
imports
import_jobs
import_errors
```

Features:

- Download template
- Field mapping
- Validation preview
- Error report
- Partial import strategy
- Duplicate detection
- Import history
- Rollback where safely supported

---

# 29. Export System

Create a centralized export service.

Support:

- CSV
- XLSX
- PDF

Exports should support:

- Current filters
- Selected records
- All permitted records
- Custom columns
- Saved export templates

Large exports must run asynchronously.

When complete:

```text
Notify User
↓
Generate Secure Download URL
↓
Expire URL
```

Log every export.

---

# 30. Print System

Every major business record should support a print-friendly layout.

Examples:

- Client profile
- Filing summary
- Task list
- Proposal
- Invoice
- Financial statements
- Trial balance
- General ledger
- Audit report

Use dedicated print layouts.

Do not simply print the entire dashboard UI.

---

# 31. Data Table Standard

Create a reusable enterprise table component.

Every major listing must support where applicable:

- Search
- Sorting
- Filters
- Advanced filters
- Date range
- Column visibility
- Saved views
- Pagination
- Page sizes:
  - 10
  - 25
  - 50
  - 100
- Record count
- Row selection
- Bulk actions
- Import
- Export
- Print
- Restore deleted records where authorized

Actions:

```text
View
Edit
Delete
Restore
Archive
Assign
Export
Print
Duplicate where appropriate
```

Do not allow duplication of sensitive financial transactions without a controlled workflow.

---

# 32. Dashboard System

Create configurable dashboards.

Widgets:

- Total clients
- New leads
- Active engagements
- Upcoming deadlines
- Overdue filings
- Pending documents
- Staff workload
- Unpaid invoices
- Revenue
- Cash flow
- Tasks
- Recent activity

Users can:

- Reorder widgets
- Hide widgets
- Configure date range
- Save preferences

Use a widget registry architecture so new widgets can be added later.

---

# 33. Document Management

Support:

- Folder hierarchy
- Client folders
- Filing folders
- Versioning
- Required documents
- Expiration
- Approval
- Sharing
- Secure links
- Upload requests
- Access permissions

File storage path must be tenant-aware.

Example:

```text
tenant/{tenant_uuid}/clients/{client_uuid}/documents/
```

Never expose raw internal storage paths.

Use signed access URLs.

---

# 34. Document Soft Delete

Documents should support a recycle-bin workflow.

Fields:

```text
deleted_at
deleted_by
purge_after
```

Before permanent deletion:

- Verify permissions
- Verify retention policy
- Verify legal hold
- Verify document is not required by finalized records

A document under legal hold cannot be permanently deleted.

---

# 35. Legal Hold and Retention

Add a data retention architecture.

Support:

```text
Retention Policies
Legal Hold
Retention Expiration
Purge Schedule
```

Examples:

- Tax records retained for configured number of years
- Audit logs retained according to system policy
- Temporary files automatically removed

This must be configurable by jurisdiction and organization policy.

---

# 36. Client Portal

Route:

```text
/portal/*
```

Clients may:

- View profile
- View filing status
- Upload documents
- Download authorized documents
- Send secure messages
- Book appointments
- Accept proposals
- Sign documents
- View invoices
- Make payments

Internal staff notes must never be exposed.

The API must return portal-specific resources rather than exposing the full internal resource.

---

# 37. E-Signature System

Support:

- Multiple signers
- Signature order
- Signature fields
- Initial fields
- Date fields
- Text fields
- Secure signing URLs
- Expiration
- Reminders
- Audit certificate

Statuses:

```text
Draft
Sent
Viewed
Partially Signed
Completed
Declined
Expired
Cancelled
```

Completed signatures and audit history are immutable.

---

# 38. Notification System

Support:

- In-app
- Email
- SMS
- Push architecture for future support

Notification preferences should exist per user.

Queue all external notification delivery.

Track:

```text
Queued
Sent
Delivered
Failed
Read
```

where the provider supports those statuses.

---

# 39. User Management

User profile:

```text
UUID
Username
First Name
Last Name
Email
Phone
Avatar
Timezone
Locale
Status
Last Login
Two Factor Enabled
```

Statuses:

```text
Active
Inactive
Suspended
Invited
Archived
```

Never hard-delete a user with historical records unless data retention policy explicitly allows it.

Instead deactivate or archive.

---

# 40. Security

Implement:

- Password hashing
- Rate limiting
- Login throttling
- Email verification
- Password reset
- MFA / 2FA
- Session management
- Device/session listing
- Forced logout
- IP logging where appropriate
- Audit logging
- Tenant isolation
- RBAC
- Policies
- Secure file access
- Signed URLs
- Input validation
- Output sanitization
- CSRF protection where applicable

All sensitive actions require backend authorization.

---

# 41. Seeder Requirements

Create realistic development seeders.

## Core Seed Data

Seed:

- Platform settings
- Modules
- Feature flags
- Permissions
- Roles
- Administrator
- Default workflow
- Filing types
- Sample chart of accounts
- Tax categories
- Notification templates
- Email templates
- Document folder templates

Do not seed thousands of unnecessary fake records in production.

Create separate:

```text
ProductionSeeders
DevelopmentSeeders
DemoSeeders
```

architecture.

---

# 42. Default Roles

Seed:

```text
Platform Super Administrator
Platform Support Administrator

Firm Owner
Firm Administrator
Partner
Manager
Accountant
Senior Accountant
Junior Accountant
Reviewer
Bookkeeper
Staff Member

Client
```

The Platform Super Administrator has all permissions.

Firm roles receive permissions based on the permission matrix.

---

# 43. Platform Administrator Seeder Example

Implement equivalent logic:

```php
User::updateOrCreate(
    [
        'email' => 'administrator@cpacrm.com',
    ],
    [
        'username' => 'administrator',
        'first_name' => 'Platform',
        'last_name' => 'Administrator',
        'password' => Hash::make('administrator90@#$'),
        'email_verified_at' => now(),
        'status' => 'active',
    ]
);
```

Then assign:

```text
Platform Super Administrator
```

The seeder must safely synchronize the role and permissions.

Do not duplicate assignments on repeated seed runs.

---

# 44. Recommended Security Amendment for Seeder Credentials

The exact credentials above may be used for the initial development/bootstrap administrator as requested.

However, support environment configuration for production:

```env
INITIAL_ADMIN_USERNAME=
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
```

In production:

- Do not commit real credentials to source control.
- Require password change on first login when configured.
- Support disabling the bootstrap seeder after installation.

Add:

```text
must_change_password
```

to the user model.

---

# 45. Module Management

Create a module registry.

Example:

```text
CRM
Filings
Workflow
Tasks
Documents
Client Portal
Appointments
Proposals
E-Signatures
Accounting
Banking
Invoicing
Reports
AI Assistant
```

Each module:

```text
id
code
name
description
is_core
is_enabled
```

Use feature flags.

A Platform Administrator can enable or disable optional modules.

Core modules cannot be disabled if dependencies exist.

Implement dependency validation.

Example:

```text
Accounting
depends on
→ Clients
→ Permissions

Client Portal
depends on
→ Clients
→ Documents
→ Communications
```

---

# 46. System Settings

Create hierarchical settings.

```text
Platform Settings
↓
Tenant Settings
↓
Office Settings
↓
User Preferences
```

Examples:

- Timezone
- Locale
- Currency
- Fiscal year
- Date format
- Number format
- Branding
- Notification settings
- Security policy
- Data retention
- File limits

Settings should support caching with invalidation.

---

# 47. API Standards

Use:

```text
/api/v1/
```

Example:

```text
/api/v1/clients
/api/v1/filings
/api/v1/tasks
/api/v1/documents
/api/v1/accounting/journal-entries
/api/v1/accounting/chart-of-accounts
/api/v1/reports
```

Support:

- Pagination
- Search
- Filtering
- Sorting
- Includes where authorized
- Field selection where appropriate

Never expose sensitive relationships by default.

---

# 48. Error Handling

Use standardized API responses.

Success:

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {}
}
```

Validation:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {}
}
```

Use meaningful HTTP status codes.

---

# 49. Background Jobs

Use queues for:

- Imports
- Exports
- Email
- SMS
- Notifications
- PDF generation
- Large report generation
- Deadline generation
- Workflow automation
- File processing
- Virus scanning integration
- Scheduled reminders

Jobs must preserve tenant context safely.

---

# 50. Search Architecture

Create a global search system.

Search authorized resources:

- Clients
- Contacts
- Entities
- Filings
- Tasks
- Documents
- Invoices

Use a provider abstraction.

Initial implementation may use PostgreSQL full-text search.

Allow future migration to:

- Meilisearch
- Elasticsearch
- OpenSearch

without rewriting the application.

---

# 51. Reports

Create:

## Practice Reports

- Client growth
- Filing completion
- Overdue filings
- Staff productivity
- Workflow bottlenecks
- Document turnaround
- Revenue by service

## Accounting Reports

- Trial Balance
- General Ledger
- Balance Sheet
- Profit and Loss
- Cash Flow
- Accounts Receivable Aging
- Accounts Payable Aging

Reports must support:

- Filters
- Date ranges
- Drill-down
- Export
- Print
- Scheduled reports

---

# 52. AI Architecture

AI features must be optional.

Capabilities:

- Summarize client activity
- Identify missing documents
- Identify overdue work
- Detect workflow bottlenecks
- Suggest workload balancing
- Draft client communications

AI must never silently:

- Post journal entries
- Delete data
- Send payments
- Change financial records
- Submit tax filings

Use:

```text
AI Suggestion
↓
User Review
↓
User Approval
↓
Action
```

---

# 53. Database Standards

Use:

- UUID or ULID for public-facing identifiers
- Numeric/decimal for financial values
- JSONB for flexible metadata
- Foreign keys where appropriate
- Proper indexes
- Composite indexes for tenant queries

Example:

```text
INDEX (tenant_id, created_at)
INDEX (tenant_id, status)
INDEX (tenant_id, client_id)
```

Use database constraints for critical financial integrity.

---

# 54. Testing Requirements

Create:

## Backend Tests

- Unit tests
- Feature tests
- API tests
- Permission tests
- Tenant isolation tests
- Soft delete tests
- Restore tests
- Financial integrity tests
- Journal balancing tests
- Locked period tests

## Frontend Tests

- Component tests
- Form validation
- Permission rendering
- Critical workflow tests

Critical accounting test:

```text
Create Journal Entry
↓
Validate Debits = Credits
↓
Approve
↓
Post
↓
Generate Ledger Impact
↓
Lock Period
↓
Attempt Edit
↓
Reject
↓
Create Reversal
```

---

# 55. Development Phases

## Phase 1 — Foundation

- Laravel API
- Next.js
- PostgreSQL
- Docker
- Authentication
- RBAC
- Permissions
- Administrator Seeder
- Multi-tenancy
- Audit logging
- Soft delete framework

## Phase 2 — CRM

- Leads
- Clients
- Contacts
- Entities
- Relationships
- Services

## Phase 3 — Practice Management

- Filings
- Deadline engine
- Workflow
- Tasks
- Calendar

## Phase 4 — Documents

- Storage
- Versioning
- Requests
- Secure sharing
- Retention

## Phase 5 — Client Portal

- Authentication
- Filing status
- Documents
- Messaging
- Appointments

## Phase 6 — Accounting

- Chart of Accounts
- Fiscal periods
- Journal entries
- General ledger
- Trial balance

## Phase 7 — Billing

- Proposals
- Engagements
- Invoices
- Payments
- AR/AP

## Phase 8 — Banking

- Bank accounts
- Imports
- Reconciliation

## Phase 9 — Automation

- Workflow automation
- Reminders
- Notifications

## Phase 10 — Reporting and AI

- Analytics
- Financial reports
- Scheduled reports
- AI assistant

---

# 56. Final Development Rules

The system must be:

- Enterprise-grade
- Modular
- API-first
- Multi-tenant
- Accounting-safe
- Fully auditable
- Secure
- Responsive
- Accessible
- Scalable
- Testable

Never place complex business logic inside controllers.

Use the following flow:

```text
Next.js UI
↓
Feature Component
↓
API Service
↓
Laravel Controller
↓
Form Request Validation
↓
Policy Authorization
↓
Action / Service
↓
Domain Logic
↓
Database Transaction
↓
Event
↓
Queue / Listener
↓
Audit Log
```

For financial operations:

```text
Request
↓
Authorization
↓
Validation
↓
Database Transaction
↓
Financial Integrity Validation
↓
Post Transaction
↓
Generate Ledger Entries
↓
Commit
↓
Audit Event
```

If any financial operation fails, the complete database transaction must roll back.

Do not use floating-point values for money.

Do not allow cross-tenant data access.

Do not hard-delete accounting history.

Do not expose administrator-only APIs to standard users.

Do not trust frontend permissions.

The backend is always the final authority.

Every module must include:

1. Database migrations
2. Models
3. Relationships
4. Factories
5. Seeders where required
6. Form Requests
7. Policies
8. Services / Actions
9. API Controllers
10. API Resources
11. Routes
12. Tests
13. Frontend API service
14. TanStack Query hooks
15. Create page
16. Edit page
17. View page
18. Listing page
19. Import where applicable
20. Export where applicable
21. Print where applicable
22. Soft delete / archive behavior
23. Restore behavior where applicable
24. Audit logging
25. Permission enforcement

Build the platform module by module.

Do not generate a fake prototype.

Generate production-quality architecture, migrations, relationships, seeders, authorization, APIs, frontend pages, reusable components, tests, and business logic.