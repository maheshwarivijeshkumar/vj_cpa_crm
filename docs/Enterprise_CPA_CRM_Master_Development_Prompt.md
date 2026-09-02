# Enterprise CPA CRM — Complete Master Development Prompt
## Production-Grade Multi-Tenant CPA CRM, Practice Management, Tax, Accounting & Client Portal SaaS

> **Purpose:** This document is the master implementation prompt/specification for building a production-grade CPA CRM and accounting-practice platform. It expands the existing architecture with a complete template system, taxation engine, notifications, settings hierarchy, configurable forms, enterprise UI standards, Eloquent-first Laravel architecture, and parallel delivery rules.

---

# 1. Product Vision

Build a **production-grade, enterprise-level, multi-tenant CPA CRM, accounting practice management, tax/compliance, workflow, document management, billing, accounting, client portal, communications, reporting and automation SaaS platform, AI Chatbot**.

The product must feel like a modern international CPA/practice-management product while remaining an original product. Do not copy another company's branding, proprietary assets, source code, UI, content or protected design.

The platform must be:

- Enterprise-grade
- Multi-tenant
- API-first
- Accounting-safe
- Taxation-aware
- Fully auditable
- Modular
- Configurable
- Permission-driven
- Responsive
- Accessible
- Scalable
- Testable
- Localization-ready
- International-market ready
- Eloquent-first on Laravel
- Designed for parallel development

The implementation must produce a **real production architecture**, not a static prototype or fake dashboard.

Every module must be implemented end-to-end:

```text
Database
→ Models
→ Eloquent Relationships
→ Factories
→ Seeders
→ Form Requests
→ Policies
→ Actions / Services
→ Events / Listeners
→ Jobs where required
→ API Controllers
→ API Resources
→ Routes
→ Tests
→ Frontend API Service
→ TanStack Query Hooks
→ Listing
→ Create
→ Edit
→ View
→ Delete / Archive
→ Restore where applicable
→ Import / Export where applicable
→ Print where applicable
→ Audit Logging
→ Permissions
→ Empty / Loading / Error States
```

---

# 2. Technology Stack

## Frontend

Use one single Next.js application containing:

- Public marketing website
- Authentication
- Platform Super Admin
- Firm Administration
- Partner dashboard
- Manager dashboard
- Accountant workspace
- Bookkeeper workspace
- Staff workspace
- Client portal
- Secure external signing / document-request experiences

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
- Framer Motion only where motion improves UX
- Lucide React for icons

Use a feature-based frontend architecture.

Example:

```text
src/
├── app/
├── components/
├── features/
│   ├── auth/
│   ├── dashboard/
│   ├── crm/
│   ├── clients/
│   ├── engagements/
│   ├── filings/
│   ├── taxation/
│   ├── accounting/
│   ├── invoicing/
│   ├── templates/
│   ├── documents/
│   ├── workflows/
│   ├── notifications/
│   ├── settings/
│   └── reports/
├── config/
│   ├── forms/
│   ├── fields/
│   ├── navigation/
│   ├── permissions/
│   └── modules/
├── hooks/
├── lib/
├── services/
├── stores/
├── types/
└── utils/
```

---

# 3. Backend

Use:

- Laravel
- PHP 8.4+
- PostgreSQL preferred for the primary production implementation
- MySQL compatibility where practical
- Passport
- Redis
- Laravel Queue
- Laravel Horizon
- Laravel Scheduler
- S3-compatible object storage / Local Too (Initial Local Storage later go with S3)
- REST API
- OpenAPI / Swagger documentation
- Pusher
- Mail configuration
- Service Layer
- Repository Patterns
- AI based Chat Bot (Whatsapp)

The backend must be **API-first**.

---

# 4. Laravel ORM / Eloquent Development Rule

## Mandatory Rule

Use **Laravel Eloquent ORM and relationships as the default database access strategy**.

Do not use raw SQL merely because it is shorter.

Prefer:

- Eloquent models
- `belongsTo`
- `hasOne`
- `hasMany`
- `belongsToMany`
- `morphTo`
- `morphMany`
- `morphOne`
- `morphToMany`
- custom pivot models
- scopes
- query builders
- repositories only where abstraction provides real value
- Actions / Services for business logic
- eager loading
- constrained eager loading
- `with()`
- `load()`
- `withCount()`
- `withSum()`
- `withExists()`
- `whereHas()`
- `whereRelation()`
- pagination
- chunking / lazy collections for large datasets

## Raw Query Exception

Raw SQL or DB expressions are allowed only when there is a clear reason such as:

- proven performance bottleneck
- complex reporting aggregation
- database-specific feature
- recursive hierarchy query
- full-text search optimization
- bulk operation where Eloquent would be materially inefficient
- financial reporting requiring a specialized SQL projection
- database-level locking / concurrency requirement

Whenever raw SQL is used:

1. Document why.
2. Keep it inside a Repository / Query Service / dedicated reporting query class.
3. Never place raw SQL inside controllers.
4. Never concatenate untrusted user input.
5. Use parameter binding.
6. Keep tenant isolation explicit.
7. Add tests for the query.

## N+1 Prevention

Never build an API that executes one query per row.

Use proper eager loading and query planning:

```php
Client::query()
    ->with([
        'contacts',
        'entities',
        'services',
        'accountManager',
    ])
    ->withCount('engagements')
    ->paginate();
```

Do not load huge relationships unnecessarily.

---

# 5. Application Architecture

```text
CPA PLATFORM
│
├── Next.js Application
│   ├── Public Website
│   ├── Authentication
│   ├── Platform Administration
│   ├── Firm Dashboard
│   ├── Accounting Workspace
│   ├── Practice Management Workspace
│   └── Client Portal
│
└── Laravel API
    ├── Identity & Access
    ├── Multi-Tenancy
    ├── CRM
    ├── Client Management
    ├── Engagement Management
    ├── Tax Profiles
    ├── Taxation Engine
    ├── Filing Engine
    ├── Deadline Engine
    ├── Workflow Engine
    ├── Task Management
    ├── Time Tracking
    ├── Capacity Management
    ├── Documents
    ├── Templates
    ├── Communications
    ├── Notifications
    ├── Scheduling
    ├── Proposals
    ├── E-Signatures
    ├── Accounting
    ├── General Ledger
    ├── Banking
    ├── Invoicing
    ├── Payments
    ├── AR/AP
    ├── Reports
    ├── Import / Export
    ├── Audit Logging
    ├── Integrations
    ├── Subscription / Billing
    └── AI Assistant
```

---

# 6. Multi-Tenant Architecture

Every accounting firm is a tenant.

Every tenant-owned table must contain:

```text
tenant_id
```

Tenant isolation must be enforced through:

- authenticated tenant context
- middleware
- Eloquent global scopes where appropriate
- Policies
- Services / Actions
- queue jobs
- cache keys
- file paths
- API authorization
- database constraints
- tests

Never trust `tenant_id` supplied by the frontend.

The authenticated tenant context is authoritative.

Example:

```text
Platform
  └── Tenant / Accounting Firm
      ├── Offices
      ├── Users
      ├── Clients
      ├── Entities
      ├── Engagements
      ├── Filings
      ├── Tax Profiles
      ├── Accounting
      ├── Documents
      ├── Templates
      ├── Workflows
      ├── Notifications
      ├── Settings
      └── Integrations
```

---

# 7. Platform vs Tenant vs Office vs User Configuration

Implement a hierarchical configuration model:

```text
Platform Default
       ↓
Tenant Override
       ↓
Office Override
       ↓
User Preference
```

Do not duplicate platform configuration into every tenant unless required.

Resolve configuration through a dedicated `SettingsService`.

Example:

```text
get('invoice.prefix')
```

Resolution:

```text
User Setting
→ Office Setting
→ Tenant Setting
→ Platform Setting
→ System Default
```

Cache resolved settings and invalidate the appropriate cache whenever settings change.

---

# 8. Module Registry and Feature Flags

Create a module registry.

Modules should include:

```text
CRM
Clients
Contacts
Entities
Engagements
Services
Filings
Taxation
Deadlines
Workflows
Tasks
Time Tracking
Capacity Planning
Documents
Templates
Communications
Notifications
Appointments
Calendar
Proposals
E-Signatures
Accounting
Banking
Invoicing
Payments
Expenses
Vendors
AR/AP
Reports
Client Portal
Integrations
Subscription Billing
AI Assistant
```

Module fields:

```text
id
code
name
description
is_core
is_enabled
sort_order
settings
created_at
updated_at
```

Support dependency rules:

```text
Accounting
→ Clients
→ Permissions

Client Portal
→ Clients
→ Documents
→ Communications

Taxation
→ Clients
→ Entities
→ Filings

Invoicing
→ Clients
→ Services
→ Accounting
```

A platform administrator may enable/disable optional modules.

Core modules cannot be disabled if active dependencies exist.

---

# 9. Roles and Permissions

Seed:

```text
Platform Super Administrator
Platform Support Administrator
Platform Accounting Administrator

Firm Owner
Firm Administrator
Partner
Manager
Senior Accountant
Accountant
Bookkeeper
Reviewer
Staff Member

Client
```

Permission format:

```text
module.action
```

Support:

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
configure
post
reverse
void
send
sign
```

Examples:

```text
clients.view
clients.view_any
clients.create
clients.update
clients.delete
clients.restore
clients.import
clients.export
clients.manage

taxes.view
taxes.create
taxes.update
taxes.archive
taxes.manage

templates.view
templates.create
templates.update
templates.publish
templates.archive
templates.preview
templates.test_send
templates.manage
```

Frontend permissions only control visibility.

Backend authorization is always mandatory.

---

# 10. Authentication and Security

Implement:

- Email/password authentication
- Email verification
- Password reset
- MFA / 2FA
- Session management
- Device/session list
- Forced logout
- Login throttling
- Rate limiting
- Secure password hashing
- Security event logging
- Tenant isolation
- RBAC
- Policies
- Signed document URLs
- Secure external links
- CSRF protection where applicable
- Input validation
- Output sanitization
- Secure headers
- File type validation
- File size validation
- Virus scanning integration architecture

---

# 11. Bootstrap Administrator

Create:

```text
database/seeders/PlatformAdministratorSeeder.php
```

Development/bootstrap credentials from the existing project specification:

```text
Username: administrator
Email: administrator@cpacrom.com
Password: administrator90@#$
```

Never store the password in plaintext.

Use:

```php
Hash::make(...)
```

Production must support:

```env
INITIAL_ADMIN_USERNAME=
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
```

Add:

```text
must_change_password
```

Production credentials must never be committed to source control.

---

# 12. Database Naming and Standards

Use:

- UUID or ULID for public-facing identifiers
- integer/bigint internal keys where useful
- decimal/numeric for financial values
- JSON/JSONB for flexible metadata
- foreign keys
- unique constraints
- composite indexes
- tenant-aware indexes
- timestamps
- appropriate status fields
- archive fields where required

Common indexes:

```text
(tenant_id, created_at)
(tenant_id, status)
(tenant_id, client_id)
(tenant_id, entity_id)
(tenant_id, due_date)
(tenant_id, is_active)
```

Money must never use floating point.

Use:

```text
numeric(20, 6)
```

or a clearly defined tenant/platform precision standard.

---

# 13. CRM Architecture

## Leads

Support:

- Lead source
- Campaign
- Lead owner
- Status
- Pipeline
- Activities
- Notes
- Calls
- Emails
- Meetings
- Follow-ups
- Tags
- Custom fields
- Attachments
- Conversion

Default stages:

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

Conversion:

```text
Lead
→ Person / Organization
→ Client
→ Entity
→ Engagement
→ Service
→ Filing / Accounting Setup
```

---

# 14. Person, Organization and Entity Model

Do not duplicate the same person unnecessarily.

Support:

```text
Person
Organization
Tax Entity
Contact
Relationship
```

Example:

```text
Person
├── Individual Tax Client
├── Director → Company
├── Shareholder → Holding Company
└── Trustee → Trust
```

Create flexible relationship types:

```text
Director
Shareholder
Owner
Partner
Trustee
Beneficiary
Officer
Authorized Representative
Spouse
Dependent
Parent
Child
Employee
Bookkeeper
Accountant
Other
```

Relationship fields:

```text
id
tenant_id
from_party_id
to_party_id
relationship_type_id
start_date
end_date
is_primary
metadata
created_at
updated_at
```

---

# 15. Client Management

Client profile should include:

- Personal/company information
- Contact Types
- Contact details
- Addresses Types
- Addresses details
- Tax identifiers
- Tax residency
- Client classification
- Client risk rating
- Status
- Tags
- Assigned partner
- Assigned manager
- Assigned accountant
- Source
- Onboarding status
- KYC/identity status where applicable
- Engagements
- Filings
- Documents
- Invoices
- Payments
- Tasks
- Communications
- Appointments
- Notes
- Custom fields
- Portal access

---

# 16. Client Onboarding

Create an onboarding workflow.

Stages:

```text
Lead Converted
→ Invitation Sent
→ Client Registered
→ Profile Completion
→ Identity / Compliance Review
→ Documents Requested
→ Documents Received
→ Engagement Accepted
→ Accounting Setup
→ Active Client
```

Allow custom onboarding workflows.

Track completion percentage.

Create onboarding dashboard widgets:

- Pending invitations
- Incomplete profiles
- Missing documents
- Pending approvals
- Clients waiting for engagement acceptance

---

# 17. Engagement Management

Create engagement management comparable to a professional CPA practice platform.

Support:

- Engagement types
- Engagement letters
- Scope
- Service packages
- Start/end dates
- Recurring engagements
- Assigned team
- Partner
- Manager
- Billing model
- Fee structure
- Budget
- Time budget
- Tasks
- Documents
- Filing relationships
- Approval
- Renewal
- Status history

Statuses:

```text
Draft
Proposed
Sent
Accepted
Active
On Hold
Completed
Cancelled
Expired
Archived
```

---

# 18. Service Catalog

Create tenant-configurable services.

Examples:

```text
Tax Preparation
Tax Filing
Bookkeeping
Payroll
Accounting
Financial Statements
Audit
Review
Compilation
Advisory
CFO Services
Business Registration
Tax Planning
Consulting
Compliance
Other
```

Each service may have:

```text
code
name
description
category
default_price
billing_method
tax_category
revenue_account
default_duration
is_recurring
is_active
```

Billing methods:

```text
Fixed Fee
Hourly
Milestone
Retainer
Recurring
Custom
```

---

# 19. Taxpayer / Tax Profile Architecture

The taxation system must support **tax practice management**, not merely invoice taxes.

Create:

```text
tax_profiles
tax_profile_identifiers
tax_residencies
tax_registrations
tax_classifications
tax_obligations
tax_years
tax_periods
```

A client/entity may have multiple tax profiles depending on jurisdiction and tax regime.

Support:

- Tax residency
- Tax identification numbers
- Registration numbers
- Filing frequency
- Tax year
- Fiscal year
- Tax authority
- Jurisdiction
- Entity classification
- Tax status
- Exemptions
- Withholding profile
- Effective dates

Sensitive tax identifiers must be encrypted or protected according to the field's sensitivity.

---

# 20. Taxation Engine

Build a flexible, country-neutral taxation engine.

Support:

```text
VAT
GST
Sales Tax
Withholding Tax
Provincial / State Tax
Municipal Tax
Custom Tax
Compound Tax
```

Tax configuration must support:

```text
Country
State
Province
Region
Jurisdiction
Tax Authority
Tax Type
Tax Code
Rate
Fixed Amount
Calculation Method
Effective From
Effective To
Priority
Compound
Inclusive / Exclusive
Exemption
Reverse Charge
Withholding
Status
```

## Core Tax Tables

Create at minimum:

```text
tax_jurisdictions
tax_authorities
tax_types
tax_categories
tax_rates
tax_rules
tax_rule_components
tax_exemptions
tax_registrations
tax_profiles
tax_transactions
tax_transaction_lines
tax_periods
tax_returns
tax_return_lines
```

## Tax Rule Versioning

Never overwrite a historical tax rule that has already been used.

Use versioning/effective dating:

```text
Tax Rule v1
Effective: 2026-01-01 → 2026-06-30

Tax Rule v2
Effective: 2026-07-01 → Present
```

Historical invoices, transactions and filings must retain the tax rule/version used at the time of posting.

## Tax Calculation

Support:

```text
Exclusive tax:
Net + Tax = Gross

Inclusive tax:
Gross contains tax

Compound tax:
Tax B calculated after Tax A

Withholding:
Gross
→ Withholding
→ Net Payable
```

Do not hard-code formulas into controllers.

Create:

```text
TaxCalculationService
TaxRuleResolver
TaxRateResolver
TaxJurisdictionResolver
TaxExemptionResolver
TaxPostingService
```

---

# 21. Tax Exemptions

Support:

- Client-specific exemption
- Product/service exemption
- Jurisdiction exemption
- Date-based exemption
- Full exemption
- Partial exemption
- Reverse charge
- Zero-rated
- Non-taxable

Track:

```text
certificate_number
effective_from
effective_to
document_id
approved_by
approved_at
```

---

# 22. Tax Transactions

Every taxable financial transaction should be traceable.

Create:

```text
tax_transactions
tax_transaction_lines
```

Relations should connect to:

- Invoice
- Invoice item
- Payment
- Expense
- Bill
- Journal entry
- Client
- Entity
- Tax profile
- Tax rule
- Jurisdiction

Never rely only on current tax configuration to reconstruct historical tax amounts.

Store the applied tax information on the transaction.

---

# 23. Tax Filing / Tax Return Management

Support tax return workflow.

Example:

```text
Tax Return Created
→ Data Collection
→ Review
→ Calculation
→ Documents Attached
→ Prepared
→ Reviewed
→ Approved
→ Submitted
→ Accepted / Rejected
→ Archived
```

Support:

- Filing period
- Due date
- Extended due date
- Jurisdiction
- Tax authority
- Tax type
- Return number
- Assigned preparer
- Reviewer
- Approver
- Submission reference
- Submission date
- Payment/refund
- Status history
- Supporting documents

---

# 24. Tax Obligations

Create recurring obligations per client/entity.

Examples:

```text
VAT Return
GST Return
Sales Tax Return
Payroll Tax
Withholding Return
Corporate Tax
Income Tax
Estimated Tax
Annual Return
Other Compliance
```

Generate future obligations based on:

- Entity type
- Jurisdiction
- Filing frequency
- Tax year
- Tax profile
- Effective rule
- Extensions
- Holidays
- Weekend adjustments

---

# 25. Filing Engine

Filing record:

```text
client_id
entity_id
filing_type_id
tax_year_id
period_id
jurisdiction_id
due_date
extended_due_date
priority
workflow_id
status
assigned_users
reviewer_id
approver_id
documents
tasks
notes
history
```

Statuses:

```text
Not Started
Data Collection
In Progress
Waiting for Client
Ready for Review
Under Review
Changes Requested
Approved
Ready to File
Submitted
Accepted
Rejected
Extended
Completed
Cancelled
Archived
```

Completed filing history must never be silently deleted.

---

# 26. Deadline Engine

Deadline rules must support:

- Calendar year
- Fiscal year
- Entity type
- Jurisdiction
- Filing frequency
- Tax type
- Extension
- Weekend adjustment
- Holiday adjustment
- Custom offsets

Flow:

```text
Deadline Rule
→ Determine Entity
→ Determine Tax Profile
→ Determine Filing Period
→ Apply Rule
→ Apply Extension
→ Weekend Adjustment
→ Holiday Adjustment
→ Final Deadline
→ Save Rule Version
```

Store the rule/version used to calculate every generated deadline.

---

# 27. Workflow Engine

Build reusable workflow templates.

Triggers:

```text
Client Created
Client Converted
Engagement Created
Engagement Accepted
Filing Created
Filing Status Changed
Document Uploaded
Document Approved
Task Completed
Deadline Approaching
Proposal Accepted
Payment Received
Appointment Completed
Invoice Overdue
Tax Return Rejected
Client Portal Registration
```

Actions:

```text
Create Task
Assign User
Assign Team
Send Email
Send SMS
Create Notification
Create Reminder
Move Workflow Stage
Request Document
Create Filing
Create Journal Draft
Create Invoice
Create Appointment
Send Proposal
Request Signature
Call Webhook
```

Every workflow execution must be logged.

Create:

```text
workflow_templates
workflow_versions
workflow_stages
workflow_rules
workflow_actions
workflow_runs
workflow_run_steps
```

---

# 28. Task Management

Support:

- Task title
- Description
- Assignee
- Team
- Priority
- Status
- Due date
- Start date
- Estimated time
- Actual time
- Client
- Entity
- Engagement
- Filing
- Workflow
- Parent task
- Subtasks
- Dependencies
- Checklist
- Comments
- Attachments
- Recurrence
- Time entries

Statuses:

```text
Backlog
Todo
In Progress
Waiting
Review
Completed
Cancelled
```

---

# 29. Time Tracking and Practice Profitability

Add professional time tracking because CPA practices often need staff utilization and engagement profitability.

Support:

- Manual time entry
- Timer
- Billable/non-billable
- Staff
- Client
- Engagement
- Service
- Task
- Rate
- Duration
- Notes
- Approval
- Locking
- Billing conversion

Reports:

- Billable hours
- Non-billable hours
- Utilization
- Realization
- Revenue per employee
- Engagement profitability
- Budget vs actual hours
- Staff workload

Do not allow approved/locked time entries to be silently edited.

---

# 30. Capacity Planning

Add:

- Team capacity
- Staff availability
- Holiday calendars
- Leave integration architecture
- Workload by employee
- Workload by client
- Workload by engagement
- Deadline heatmap
- Over-allocation alerts
- Under-utilization reports

Dashboard:

```text
Available Hours
Booked Hours
Billable Hours
Utilization %
Overdue Work
Upcoming Deadline Load
```

---

# 31. Document Management

Support:

- Folder hierarchy
- Client folders
- Engagement folders
- Filing folders
- Document requests
- Required documents
- Upload requests
- Versioning
- Approvals
- Sharing
- Secure links
- Expiration
- Legal hold
- Retention
- Recycle bin
- Download tracking

Storage:

```text
tenant/{tenant_uuid}/clients/{client_uuid}/documents/
```

Never expose raw internal storage paths.

Use signed URLs.

---

# 32. Document Request System

This should be a first-class CPA feature.

Support:

```text
Document Request Template
→ Required Documents
→ Client Notification
→ Secure Upload
→ Review
→ Approved / Rejected
→ Reminder
→ Complete
```

Features:

- Checklist
- Due date
- Priority
- Required/optional
- Client visibility
- Internal notes
- Reminder schedule
- File validation
- Multiple uploads
- Version replacement
- Review status

Statuses:

```text
Requested
Partially Received
Received
Under Review
Changes Requested
Approved
Expired
Cancelled
```

---

# 33. Template Management System

Build a **central reusable template engine**.

Templates must exist at multiple scopes:

```text
Platform Template
      ↓
Tenant Template
      ↓
Office Template
```

Platform templates are defaults.

Tenant templates may override platform defaults.

Office templates may override tenant templates.

Never overwrite the original platform template when a tenant customizes it.

## Template Categories

Create templates for:

### Email

```text
Welcome Email
Email Verification
Password Reset
OTP
Invitation
Client Portal Invitation
Document Request
Document Reminder
Filing Reminder
Deadline Reminder
Proposal Sent
Proposal Accepted
Engagement Letter
Signature Request
Signature Reminder
Invoice Created
Invoice Reminder
Payment Received
Payment Failed
Overdue Invoice
Tax Filing Submitted
Tax Filing Accepted
Tax Filing Rejected
Appointment Confirmation
Appointment Reminder
Appointment Cancellation
Account Suspension
Subscription Expiry
```

### SMS

Provide the same event architecture with SMS-safe content.

### In-App Notifications

Create notification templates with:

- Title
- Body
- Icon
- Severity
- Action URL
- Variables

### Document Templates

Support:

```text
Engagement Letter
Proposal
Quotation
Invoice
Credit Note
Receipt
Tax Organizer
Document Request
Client Summary
Financial Statement
Tax Return Cover Letter
Appointment Letter
Other Custom Documents
```

### Workflow Templates

Support reusable:

- Task checklists
- Workflow stages
- Task assignments
- Reminder schedules
- SLA rules

### Invoice / Quotation Templates

Support:

- Header
- Logo
- Footer
- Tax display
- Payment details
- Terms
- Notes
- Currency
- Number format
- Custom fields
- Signature block
- QR/payment information where configured

---

# 34. Template Engine Data Model

Create:

```text
templates
template_versions
template_variables
template_categories
template_assignments
template_localizations
template_render_logs
```

Recommended template fields:

```text
id
tenant_id nullable
office_id nullable
category_id
code
name
description
type
channel
subject
body
layout
variables_schema
scope
is_system
is_active
is_default
version
published_at
published_by
archived_at
created_by
updated_by
created_at
updated_at
```

## Template Scope

```text
platform
tenant
office
```

## Template Resolution

When the application needs a template:

```text
Office Template
→ Tenant Template
→ Platform Template
→ System Fallback
```

Use a `TemplateResolverService`.

Never scatter template lookup logic throughout controllers.

---

# 35. Template Variables

Create a safe variable system.

Examples:

```text
{{client.first_name}}
{{client.last_name}}
{{client.full_name}}
{{firm.name}}
{{firm.email}}
{{firm.phone}}
{{filing.type}}
{{filing.due_date}}
{{invoice.number}}
{{invoice.total}}
{{invoice.balance_due}}
{{payment.amount}}
{{appointment.date}}
{{appointment.time}}
{{portal.url}}
{{user.name}}
```

Variables must be validated before publishing.

Provide:

- Variable browser
- Copy variable button
- Preview
- Sample data
- Missing variable detection
- Invalid variable detection
- Safe rendering
- HTML sanitization where applicable

Do not permit arbitrary executable code inside templates.

---

# 36. Template Versioning

Templates must support:

```text
Draft
Review
Published
Archived
```

A published version should not be mutated in place when historical consistency matters.

Create a new version.

Track:

```text
version_number
created_by
created_at
published_by
published_at
change_summary
```

Allow rollback by publishing a previous version as a new current version.

---

# 37. Template Preview and Test Sending

Every email/SMS/document template should support where applicable:

- Preview
- Desktop preview
- Mobile preview
- Test recipient
- Sample variables
- Render validation
- Send test
- Version comparison
- Publish
- Archive

All test sends must be clearly identified as test messages.

---

# 38. Notification System

Build a centralized event-driven notification system.

Channels:

```text
In-App
Email
SMS
Push-ready architecture
```

Notification lifecycle:

```text
Queued
Processing
Sent
Delivered
Failed
Read
```

Track provider message IDs where supported.

Create:

```text
notifications
notification_templates
notification_deliveries
notification_preferences
notification_events
notification_digests
```

External delivery must be queued.

---

# 39. Notification Preferences

Each user must control preferences.

Example:

```text
Email
SMS
In-App
```

Categories:

```text
Security
Client Activity
Filing
Deadline
Documents
Tasks
Workflow
Invoices
Payments
Appointments
System
Marketing
```

Support:

- Immediate
- Daily digest
- Weekly digest
- Disabled

Security-critical notifications cannot be disabled if policy requires them.

---

# 40. System Settings

Create a comprehensive settings framework.

## Platform Settings

```text
Platform Identity
Branding
Default Currency
Default Locale
Timezone
Date Format
Number Format
Email Provider
SMS Provider
File Storage
Security Policy
Password Policy
MFA Policy
Notification Defaults
Data Retention
Maintenance Mode
Registration
Subscription Billing
Feature Flags
AI Configuration
```

## Tenant Settings

```text
Firm Information
Logo
Brand Colors
Address
Phone
Email
Website
Timezone
Locale
Currency
Fiscal Year
Invoice Settings
Quotation Settings
Tax Settings
Accounting Settings
Filing Settings
Workflow Settings
Document Settings
Portal Settings
Notification Settings
Security Settings
Numbering
Payment Settings
Email Signature
Business Hours
Holiday Calendar
```

## Office Settings

Support overrides for:

- Address
- Phone
- Branding
- Numbering
- Business hours
- Timezone
- Tax settings
- Invoice settings
- Templates
- Notification rules

## User Preferences

```text
Theme
Language
Timezone
Date Format
Number Format
Dashboard Layout
Notification Preferences
Default Filters
Default Page Size
Table Preferences
Calendar Preferences
```

---

# 41. Configuration Registry

Do not hard-code every field label, placeholder, validation message, or UI string into individual components.

Create centralized frontend configuration.

Example:

```text
src/config/forms/
├── client.ts
├── lead.ts
├── entity.ts
├── filing.ts
├── tax.ts
├── invoice.ts
├── payment.ts
├── template.ts
├── user.ts
└── settings.ts
```

And:

```text
src/config/fields/
├── common.ts
├── client-fields.ts
├── tax-fields.ts
├── invoice-fields.ts
└── ...
```

The configuration should contain:

```ts
{
  first_name: {
    name: "first_name",
    label: "First Name",
    placeholder: "Enter first name",
    errorPlaceholder: "Please enter first name",
    type: "text"
  }
}
```

The UI should render field metadata from configuration wherever practical.

---

# 42. Form Validation and Error UX

## Important Rule

Do **not** rely on browser-native required validation.

Do not use:

```html
required
```

as the primary validation mechanism.

Do not depend on browser-generated validation messages.

Use:

- React Hook Form
- Zod
- backend Laravel Form Requests
- standardized API validation errors
- custom error handling

## Error Message Placement

The requested UX is:

```text
Normal:
┌──────────────────────────────┐
│ Enter client email           │
└──────────────────────────────┘

Error:
┌──────────────────────────────┐
│ Please enter a valid email   │
└──────────────────────────────┘
```

When validation fails, the error message should be displayed **inside the input placeholder area**, while preserving the field's accessible label above or associated with the field.

The error state should also use:

- border/state styling
- accessible `aria-invalid`
- `aria-describedby`
- screen-reader-friendly error text
- focus on the first invalid field where appropriate

Do not remove the field label just to display the error.

## Error Configuration

Keep field labels, placeholders and error messages in centralized configuration.

Example:

```ts
email: {
  name: "email",
  label: "Email Address",
  placeholder: "Enter email address",
  errorPlaceholder: "Please enter a valid email address"
}
```

Allow future localization without changing components.

---

# 43. Backend Validation

Every API mutation must use Laravel Form Requests or a dedicated validation layer.

Example:

```text
StoreClientRequest
UpdateClientRequest
StoreTaxRuleRequest
UpdateTaxRuleRequest
StoreTemplateRequest
PublishTemplateRequest
StoreInvoiceRequest
PostJournalEntryRequest
```

Never trust frontend Zod validation as the final validation authority.

Backend validation must include:

- Required fields
- Type validation
- Format validation
- Business rules
- Authorization-aware validation
- Tenant ownership
- Date rules
- Currency rules
- Tax rules
- Financial integrity

---

# 44. Standard API Response

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
  "errors": {
    "email": [
      "Please enter a valid email address."
    ]
  }
}
```

Error:

```json
{
  "success": false,
  "message": "Unable to complete the operation.",
  "errors": {},
  "code": "BUSINESS_RULE_VIOLATION"
}
```

Use correct HTTP status codes.

Never expose stack traces in production.

---

# 45. API Architecture

Use:

```text
/api/v1/
```

Examples:

```text
/api/v1/auth/login
/api/v1/clients
/api/v1/clients/{client}
/api/v1/entities
/api/v1/engagements
/api/v1/filings
/api/v1/tax/profiles
/api/v1/tax/rules
/api/v1/tax/returns
/api/v1/templates
/api/v1/templates/{template}
/api/v1/notifications
/api/v1/settings
/api/v1/accounting/journal-entries
/api/v1/accounting/chart-of-accounts
/api/v1/invoices
/api/v1/payments
/api/v1/reports
```

Use:

- Pagination
- Search
- Filters
- Sorting
- Includes where authorized
- Field selection where useful
- Consistent resource responses
- Authorization
- Tenant isolation

---

# 46. API Resources

Use Laravel API Resources.

Do not return raw Eloquent models directly from controllers.

Create resources such as:

```text
ClientResource
EntityResource
FilingResource
TaxProfileResource
TaxRuleResource
TaxReturnResource
TemplateResource
NotificationResource
InvoiceResource
PaymentResource
JournalEntryResource
```

Only expose relationships that the current user is authorized to see.

---

# 47. Business Logic Architecture

Do not place complex business logic inside controllers.

Use:

```text
Controller
→ Form Request
→ Policy
→ Action / Service
→ Domain Logic
→ Eloquent Models
→ Transaction
→ Event
→ Queue / Listener
→ Audit Log
```

Examples:

```text
CreateClientAction
ConvertLeadAction
CalculateTaxAction
PublishTemplateAction
GenerateFilingDeadlinesAction
PostJournalEntryAction
AllocatePaymentAction
GenerateInvoiceAction
SendNotificationAction
```

Use database transactions for multi-step operations.

---

# 48. Accounting Foundation

Implement real double-entry accounting.

Modules:

```text
Chart of Accounts
Fiscal Years
Accounting Periods
Journal Entries
General Ledger
Trial Balance
Balance Sheet
Profit & Loss
Cash Flow
Accounts Receivable
Accounts Payable
Expenses
Banking
Reconciliation
Multi-Currency
```

---

# 49. Chart of Accounts

Account types:

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

Fields:

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
```

Support hierarchical accounts.

Do not delete an account with posted transactions.

Archive it instead.

---

# 50. Fiscal Years and Accounting Periods

Create:

```text
fiscal_years
accounting_periods
```

Support:

```text
Monthly
Quarterly
Annual
Custom
```

Statuses:

```text
Open
Soft Closed
Hard Closed
Locked
```

Posted transactions in locked periods cannot be edited.

Corrections require adjusting/reversing entries.

---

# 51. Journal Entry Engine

Tables:

```text
journal_entries
journal_entry_lines
```

Journal header:

```text
entry_number
date
reference
description
status
currency
exchange_rate
created_by
approved_by
posted_by
posted_at
```

Line:

```text
account_id
description
debit
credit
tax_id
client_id
entity_id
cost_center_id
project_id
department_id
```

Mandatory rule:

```text
SUM(debit) = SUM(credit)
```

Statuses:

```text
Draft
Pending Approval
Approved
Posted
Reversed
Voided
```

Posted entries:

- cannot be edited directly
- cannot be deleted
- can be reversed
- must preserve audit history

---

# 52. General Ledger

The general ledger must be derived from posted accounting transactions.

Never manually edit ledger balances.

Support:

- Account ledger
- Client ledger
- Entity ledger
- Department ledger
- Project ledger
- Cost center ledger

Reports:

```text
General Ledger
Trial Balance
Balance Sheet
Profit & Loss
Cash Flow
Account Activity
```

---

# 53. Accounts Receivable

Tables:

```text
customers
invoices
invoice_items
credit_notes
payments
payment_allocations
refunds
write_offs
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

Support:

- Partial payments
- Credit notes
- Refunds
- Write-offs
- Payment allocation
- Multi-currency
- Tax
- Recurring invoices
- Payment terms
- Late fees
- Automated reminders

Finalized invoices must not be deleted.

---

# 54. Quotations and Proposals

CPA firms need a professional proposal/quotation workflow.

Support:

```text
Draft
Sent
Viewed
Accepted
Rejected
Expired
Cancelled
```

Features:

- Client
- Services
- Scope
- Pricing
- Taxes
- Discounts
- Terms
- Validity period
- Attachments
- Approval
- E-signature
- Conversion to engagement
- Conversion to invoice

Workflow:

```text
Lead
→ Proposal / Quotation
→ Acceptance
→ Engagement
→ Invoice
```

---

# 55. Recurring Billing

Support:

- Recurring invoice templates
- Billing frequency
- Start date
- End date
- Auto-generation
- Payment terms
- Auto-reminders
- Pause
- Resume
- Cancellation
- Failed payment handling

Use scheduled jobs.

Never generate duplicate recurring invoices.

Use idempotency keys / unique business constraints.

---

# 56. Accounts Payable

Support:

```text
Vendors
Bills
Bill Items
Expenses
Expense Categories
Approvals
Payment Schedules
Vendor Payments
```

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

# 57. Banking

Create:

```text
bank_accounts
bank_transactions
bank_reconciliations
reconciliation_lines
```

Support:

- CSV
- OFX architecture
- Future bank API integrations
- Transaction matching
- Categorization
- Rules
- Reconciliation
- Unreconciled queue
- Duplicate detection

Reconciled transactions cannot be casually deleted.

---

# 58. Multi-Currency

Support:

- Tenant base currency
- Transaction currency
- Exchange rates
- Historical exchange rates
- Realized gain/loss
- Unrealized gain/loss

A posted transaction must retain the historical exchange rate used.

---

# 59. Expense Management

Support:

- Employee expenses
- Client expenses
- Vendor expenses
- Receipts
- Categories
- Tax
- Approval
- Reimbursement
- Billable expense
- Non-billable expense
- Accounting posting

Workflow:

```text
Draft
→ Submitted
→ Review
→ Approved / Rejected
→ Reimbursed
→ Posted
```

---

# 60. Client Portal

Client portal must expose only client-authorized information.

Features:

```text
Dashboard
Profile
Tax Profile
Filing Status
Document Requests
Documents
Messages
Appointments
Proposals
Engagements
Signatures
Invoices
Payments
Notifications
```

Internal notes must never be exposed.

Use portal-specific API Resources.

---

# 61. Secure Messaging

Create:

```text
conversations
conversation_participants
messages
message_attachments
message_reads
```

Support:

- Client ↔ Firm
- Staff ↔ Client
- Internal staff conversations
- Attachments
- Read state
- Search
- Notifications
- Audit history

Clearly separate internal and client-visible communication.

---

# 62. Email Communication

Build an email communication architecture.

Support:

- Transactional email
- Client communication
- Template-driven email
- Attachments
- Reply tracking architecture
- Delivery status
- Open/click tracking where legally/configurably permitted
- Email history
- Threading architecture

External email delivery must be queued.

---

# 63. Appointments and Calendar

Support:

- Appointment types
- Staff calendars
- Client booking
- Availability
- Business hours
- Timezones
- Buffer time
- Rescheduling
- Cancellation
- Reminders
- Google Calendar integration architecture
- Microsoft Calendar integration architecture

Statuses:

```text
Requested
Confirmed
Completed
Cancelled
No Show
Rescheduled
```

---

# 64. E-Signature

Support:

- Multiple signers
- Signature order
- Signature fields
- Initials
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

# 65. Audit Trail

Track:

```text
Login
Logout
Failed Login
Create
Update
Delete
Restore
Force Delete
Import
Export
Print
Approval
Rejection
Assignment
Financial Posting
Financial Reversal
Permission Change
Settings Change
Template Publish
Tax Rule Change
Tax Filing Submission
Document Access
Signature
Payment
```

Audit fields:

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

Audit logs cannot be edited through normal application interfaces.

---

# 66. Soft Delete / Archive / Immutable Records

## Soft Delete

Use Laravel SoftDeletes for records such as:

- Clients
- Leads
- Contacts
- Tasks
- Document folders
- Services
- Appointment types
- Proposal templates
- Offices
- Teams
- Departments
- Configurable records where appropriate

Support:

```text
Delete
Restore
View Deleted
Force Delete
```

## Archive

Use archive status for historical configuration:

- Filing types
- Tax rules
- Chart of account definitions
- Service definitions
- Workflow definitions
- Templates

## Immutable / Reversal

Do not delete:

- Posted journal entries
- General ledger history
- Reconciled transactions
- Payments
- Finalized invoices
- Completed signatures
- Audit logs
- Filing history
- Tax transaction history

Use:

```text
Void
Reverse
Supersede
Correct
Cancel
Archive
```

---

# 67. Deleted Records / Recycle Bin

Create:

```text
/app/settings/deleted-records
```

Support:

- Module filter
- User filter
- Date filter
- Search
- Restore
- Bulk restore
- Permanent deletion where allowed

Permanent deletion of sensitive data should require explicit confirmation such as:

```text
DELETE
```

All restore and force-delete actions must be audited.

---

# 68. Data Retention and Legal Hold

Create:

```text
retention_policies
legal_holds
retention_events
purge_jobs
```

Support:

- Retention period
- Jurisdiction
- Module
- Record type
- Legal hold
- Retention expiration
- Purge schedule

A record under legal hold cannot be permanently deleted.

---

# 69. Import Engine

Reusable import engine for:

```text
Clients
Contacts
Leads
Entities
Filings
Tasks
Services
Chart of Accounts
Vendors
Customers
Transactions
Tax Profiles
```

Formats:

```text
CSV
XLSX
```

Flow:

```text
Upload
→ Detect Columns
→ Map Fields
→ Validate
→ Preview
→ Error Report
→ Confirm
→ Queue
→ Process
→ Results
```

Large imports must be queued.

Create:

```text
imports
import_jobs
import_errors
```

Support:

- Duplicate detection
- Partial import
- Rollback where safely possible
- Import history
- Downloadable error report

---

# 70. Export Engine

Support:

```text
CSV
XLSX
PDF
```

Allow:

- Current filters
- Selected records
- All permitted records
- Custom columns
- Saved export templates

Large exports must be asynchronous.

When complete:

```text
Generate File
→ Store Securely
→ Notify User
→ Generate Temporary Signed URL
→ Expire URL
```

Log exports.

---

# 71. Print System

Create dedicated print layouts for:

```text
Client Profile
Filing Summary
Tax Return Summary
Task List
Proposal
Quotation
Engagement Letter
Invoice
Receipt
Financial Statements
Trial Balance
General Ledger
Audit Report
Tax Report
```

Do not print the dashboard UI directly.

---

# 72. Enterprise Data Table

Create a reusable table component.

Support:

- Search
- Sorting
- Filters
- Advanced filters
- Date range
- Column visibility
- Saved views
- Pagination
- 10 / 25 / 50 / 100 rows
- Record count
- Row selection
- Bulk actions
- Import
- Export
- Print
- Restore
- Archive

Actions:

```text
View
Edit
Delete
Restore
Archive
Assign
Approve
Reject
Export
Print
Duplicate where appropriate
```

Do not duplicate sensitive financial transactions without controlled workflow.

---

# 73. Dashboard Architecture

Create a widget registry.

Dashboard widgets:

```text
Total Clients
New Leads
Active Engagements
Upcoming Deadlines
Overdue Filings
Pending Documents
Staff Workload
Unpaid Invoices
Revenue
Cash Flow
AR Aging
AP Aging
Tax Obligations
Tax Returns Due
Tasks
Time Utilization
Engagement Profitability
Recent Activity
Notifications
```

Users can:

- Reorder widgets
- Hide widgets
- Configure date range
- Save preferences
- Reset dashboard

---

# 74. Dashboard UI and Charts

Use Recharts.

Recommended charts:

### Practice Overview

- Client growth line chart
- Revenue area/bar chart
- Filing status donut
- Staff workload bar chart
- Deadline trend
- New leads funnel

### Accounting

- Revenue vs expenses
- Cash flow
- AR aging
- AP aging
- P&L trend
- Account balances

### Tax

- Tax obligations by status
- Returns by jurisdiction
- Filing completion
- Upcoming deadlines
- Tax liability trend

Charts must:

- Have clear labels
- Have useful tooltips
- Support responsive sizing
- Avoid visual overload
- Provide accessible alternatives where appropriate
- Match the product palette
- Use consistent number formatting

---

# 75. UI / UX Design System

Use the supplied color palette as the **primary brand system**.

The supplied palette is based around calm teal/green tones:

```text
Very Light      #E6F5F4
Light           #C5E8E5
Medium Light    #8CD3CF
Medium          #48BCB9
Medium Dark     #1D9792
Dark            #055E5A
Very Dark       #023E3C
Text Secondary  #4D7374
Background      #F4FAFA
White           #FEFDFD
```

The exact sampled values may be adjusted slightly during implementation for accessibility, but the visual identity must remain faithful to the supplied palette.

## Design Personality

```text
Calm
Focused
Balanced
Premium
Professional
Trustworthy
Modern
Clean
Financial
Enterprise
```

Avoid:

- Excessive gradients
- Neon colors
- Excessive shadows
- Over-rounded cartoon-style UI
- Excessive animations
- Cluttered dashboards

---

# 76. Typography

Use a professional enterprise font such as:

```text
Inter
```

Suggested hierarchy:

```text
Page Title: 28–32px / 600
Section Heading: 20–24px / 600
Card Heading: 16–18px / 600
Body: 14–16px / 400
Label: 13–14px / 500
Helper Text: 12–13px / 400
```

Maintain strong readability.

---

# 77. Icons

Use:

```text
Lucide React
```

Do not mix multiple unrelated icon libraries.

Icons must have consistent:

- Stroke width
- Size
- Alignment
- Visual weight

Suggested sizes:

```text
16px — table/actions
18px — inputs/buttons
20px — navigation
24px — cards
28–32px — dashboard highlights
```

Do not use icons merely for decoration.

---

# 78. UI Components

Create reusable components:

```text
Button
IconButton
Input
Textarea
Select
Combobox
DatePicker
DateRangePicker
CurrencyInput
PercentageInput
PhoneInput
TaxRateInput
SearchInput
FilterBuilder
Tag
Badge
StatusBadge
Avatar
AvatarGroup
Card
StatCard
ChartCard
DataTable
Drawer
Modal
ConfirmDialog
CommandPalette
Tabs
Accordion
Dropdown
Popover
Tooltip
Toast
NotificationPanel
Timeline
ActivityFeed
EmptyState
LoadingSkeleton
ErrorState
FileUploader
DocumentViewer
RichTextEditor
TemplateEditor
WorkflowBuilder
PermissionMatrix
```

---

# 79. Form UX

Forms must be professional and fast.

Use:

- Clear labels
- Configurable placeholders
- Contextual helper text
- Custom errors
- Smart defaults
- Keyboard navigation
- Accessible focus states
- Sticky action footer for long forms
- Unsaved changes protection
- Save / Save & Close / Cancel where appropriate

Do not use browser native required validation.

---

# 80. Form Configuration Example

Use a centralized registry:

```ts
export const clientFields = {
  first_name: {
    name: "first_name",
    label: "First Name",
    placeholder: "Enter first name",
    errorPlaceholder: "Please enter first name",
    type: "text",
  },

  email: {
    name: "email",
    label: "Email Address",
    placeholder: "Enter email address",
    errorPlaceholder: "Please enter a valid email address",
    type: "email",
  },
};
```

The reusable form renderer should consume this configuration.

This allows future changes to labels/placeholders/errors without modifying every page.

---

# 81. Responsive Design

The application must work across:

- Desktop
- Laptop
- Tablet
- Mobile

Desktop should optimize for CPA workflows and dense data.

Mobile should prioritize:

- Quick actions
- Client communication
- Notifications
- Tasks
- Appointments
- Approvals
- Document upload
- Dashboard summary

---

# 82. Accessibility

Target WCAG 2.2 AA where practical.

Include:

- Keyboard navigation
- Focus indicators
- Semantic HTML
- ARIA where necessary
- Screen-reader labels
- Color contrast
- Accessible tables
- Accessible charts or summaries
- Error announcements
- Reduced motion support

Do not communicate status through color alone.

---

# 83. Navigation

Platform navigation:

```text
Dashboard
Tenants
Subscriptions
Plans
Modules
Feature Flags
Users
Templates
Tax Configuration
System Settings
Audit Logs
Support
```

Firm navigation:

```text
Dashboard
CRM
Clients
Leads
Entities
Engagements
Services
Filings
Taxation
Deadlines
Workflows
Tasks
Time
Calendar
Documents
Templates
Communications
Appointments
Proposals
Signatures
Accounting
Banking
Invoices
Payments
Expenses
Vendors
Reports
Team
Offices
Imports
Exports
Settings
```

Use permission/module flags to dynamically render navigation.

---

# 84. Global Search

Search:

```text
Clients
Contacts
Entities
Leads
Engagements
Filings
Tasks
Documents
Invoices
Payments
Tax Returns
```

Use a provider abstraction.

Initial implementation can use PostgreSQL full-text search.

Allow future migration to:

```text
Meilisearch
Elasticsearch
OpenSearch
```

without rewriting business logic.

---

# 85. Global Command Palette

Add a command palette.

Examples:

```text
Create Client
Create Lead
Create Filing
Create Task
Create Invoice
Create Proposal
Upload Document
Start Timer
Open Calendar
Search Client
Open Settings
```

Keyboard shortcut:

```text
Ctrl/Cmd + K
```

---

# 86. Notifications UI

Create:

- Header notification bell
- Notification drawer
- Unread count
- Grouping by date
- Mark read
- Mark all read
- Notification filters
- Deep links
- Notification preferences

Use template/event resolution from the backend.

---

# 87. Templates UI

Platform admin:

```text
Templates
├── Email
├── SMS
├── Notifications
├── Documents
├── Invoices
├── Quotations
├── Engagement Letters
├── Workflows
└── Folder Templates
```

Tenant:

```text
Settings
→ Templates
→ Platform Defaults
→ Tenant Overrides
→ Office Overrides
```

Template list must show:

```text
Name
Code
Category
Scope
Channel
Status
Version
Last Updated
Updated By
```

Actions:

```text
Preview
Edit
Duplicate
Publish
Archive
Test
Version History
Restore Version
```

---

# 88. Taxation UI

Platform:

```text
Taxation
├── Countries & currencies
├── Jurisdictions
├── Tax Authorities
├── Tax Types
├── Tax Categories
├── Tax Rules
├── Tax Rates
├── Exemptions
└── Tax Settings
```

Tenant:

```text
Taxation
├── Tax Profiles
├── Registrations
├── Obligations
├── Tax Returns
├── Tax Transactions
├── Tax Rules
└── Tax Reports
```

Tax rule editor should support:

```text
Jurisdiction
Tax Type
Tax Code
Rate
Calculation Method
Inclusive/Exclusive
Effective Date
Components
Exemption
Withholding
Reverse Charge
Status
```

---

# 89. Notification + Template + Workflow Relationship

Use event-driven architecture:

```text
Business Event
      ↓
Workflow Engine
      ↓
Conditions
      ↓
Action
      ↓
Notification Service
      ↓
Template Resolver
      ↓
Channel Adapter
      ↓
Queue
      ↓
Provider
      ↓
Delivery Log
      ↓
Audit Log
```

This makes the platform extensible.

---

# 90. Settings + Template + Notification Resolution

Do not hard-code tenant-specific behavior.

Example:

```text
Invoice Overdue
    ↓
Find Notification Preference
    ↓
Resolve Template
    ↓
Office Template?
    ↓ No
Tenant Template?
    ↓ No
Platform Template
    ↓
Render Variables
    ↓
Queue Email
    ↓
Track Delivery
```

---

# 91. Email / SMS Provider Abstraction

Use interfaces.

Example:

```text
EmailProviderInterface
SmsProviderInterface
StorageProviderInterface
PaymentProviderInterface
CalendarProviderInterface
EFileProviderInterface
```

Allow providers to be swapped without rewriting business logic.

---

# 92. Subscription / SaaS Billing

Since this is a SaaS platform, include:

```text
plans
plan_features
subscriptions
subscription_items
subscription_events
billing_cycles
usage_records
```

Support:

- Trial
- Monthly
- Annual
- Upgrade
- Downgrade
- Pause
- Cancel
- Grace period
- Failed payment
- Feature limits
- Usage limits
- Module entitlements

Tenant feature access must be resolved through a centralized entitlement service.

---

# 93. Platform Tenant Management

Platform administrator should manage:

```text
Tenants
Users
Plans
Subscriptions
Modules
Feature Flags
Templates
Tax Defaults
System Settings
Audit Logs
Support
```

Tenant profile should display:

- Firm name
- Owner
- Plan
- Status
- User count
- Client count
- Storage
- Subscription
- Enabled modules
- Created date
- Last activity

---

# 94. Tenant Provisioning

Tenant registration:

```text
Create Tenant
→ Create Owner
→ Create Default Settings
→ Assign Default Role
→ Provision Default Templates
→ Provision Default Workflow Templates
→ Provision Default Filing Types
→ Provision Chart of Accounts
→ Provision Tax Defaults
→ Create Storage Namespace
→ Create Audit Context
→ Send Welcome Notification
```

Provisioning should be idempotent.

Use a transaction for database creation and queued jobs for external operations.

---

# 95. Seeders

Create:

```text
DatabaseSeeder.php
PlatformAdministratorSeeder.php
RoleSeeder.php
PermissionSeeder.php
ModuleSeeder.php
FeatureFlagSeeder.php
SystemSettingsSeeder.php
DefaultTemplateSeeder.php
DefaultNotificationSeeder.php
DefaultWorkflowSeeder.php
DefaultFilingTypeSeeder.php
DefaultChartOfAccountsSeeder.php
DefaultTaxSeeder.php
DevelopmentSeeder.php
DemoSeeder.php
```

Seeders must be idempotent.

Use:

```php
firstOrCreate()
updateOrCreate()
```

Do not generate huge fake datasets in production.

---

# 96. Parallel Development Strategy

**Everything must be developed in parallel wherever dependencies allow.**

Do not finish the entire backend first and postpone the frontend.

Each feature should be a vertical slice:

```text
Migration
→ Model
→ Relations
→ Request
→ Policy
→ Action
→ API
→ Resource
→ Tests
→ Frontend Service
→ Hook
→ Listing
→ Create
→ Edit
→ View
→ Permissions
→ Audit
```

## Parallel Workstreams

### Track A — Platform Foundation

- Auth
- Tenant context
- RBAC
- Permissions
- Settings
- Modules
- Feature flags
- Audit
- Countries
- Currencies
- Language
- Timezone

### Track B — CRM

- Leads
- Clients
- Contacts
- Entities
- Relationships
- Services

### Track C — Practice Management

- Engagements
- Filings
- Deadlines
- Workflows
- Tasks
- Calendar
- Time tracking

### Track D — Taxation

- Tax profiles
- Jurisdictions
- Tax authorities
- Tax types
- Tax rules
- Tax rates
- Exemptions
- Tax transactions
- Tax returns
- Tax obligations

### Track E — Accounting

- Chart of accounts
- Periods
- Journal entries
- GL
- AR
- AP
- Expenses
- Banking
- Reconciliation

### Track F — Templates / Communications

- Template engine
- Email
- SMS
- Notifications
- Messaging
- Document templates

### Track G — Documents

- Storage
- Requests
- Versioning
- Secure links
- Retention
- Legal hold
- E-signatures

### Track H — Client Portal

- Portal auth
- Dashboard
- Documents
- Filing status
- Messages
- Appointments
- Proposals
- Signatures
- Invoices
- Payments

### Track I — Frontend Design System

- Layout
- Navigation
- Components
- Forms
- Tables
- Charts
- Modals
- Drawers
- Notifications
- Responsive design

### Track J — Reporting / Analytics

- Practice reports
- Tax reports
- Accounting reports
- Staff reports
- Revenue reports
- Dashboard widgets

### Track K — Integrations

- Email
- SMS
- Payments
- Storage
- Calendar
- Banking
- E-filing
- Webhooks

### Track L — QA / Security

- Unit tests
- Feature tests
- API tests
- Tenant isolation
- Permission tests
- Financial integrity
- Security tests
- UI tests
- Accessibility tests

---

# 97. Parallel Development Dependency Rule

Teams may work in parallel when interfaces/contracts are known.

For example:

```text
Backend API Contract
        ↓
Frontend implementation can proceed
        ↓
Mock API / fixture
        ↓
Real API integration
```

Do not block frontend development waiting for every backend feature.

Do not build fake final APIs that contradict the real data model.

Use shared:

- OpenAPI schema
- TypeScript types
- DTO contracts
- Response standards
- Error formats

---

# 98. Testing Strategy

## Backend

Create:

- Unit tests
- Feature tests
- API tests
- Policy tests
- Tenant isolation tests
- Eloquent relationship tests
- Soft delete tests
- Restore tests
- Tax calculation tests
- Tax versioning tests
- Template rendering tests
- Notification tests
- Workflow tests
- Financial integrity tests
- Journal balancing tests
- Locked period tests
- Idempotency tests

## Frontend

Create:

- Component tests
- Form validation tests
- Permission rendering tests
- API state tests
- Critical workflow tests
- Accessibility tests
- Responsive behavior tests

---

# 99. Critical Accounting Test

```text
Create Journal Entry
→ Validate Debit = Credit
→ Approve
→ Post
→ Generate Ledger Impact
→ Lock Period
→ Attempt Edit
→ Reject
→ Create Reversal
→ Verify Ledger
→ Verify Audit Trail
```

---

# 100. Critical Tax Test

```text
Create Tax Rule v1
→ Calculate Transaction
→ Post Transaction
→ Create Tax Transaction
→ Update Future Tax Rule
→ Create Tax Rule v2
→ Calculate New Transaction
→ Verify Old Transaction Still Uses v1
→ Verify New Transaction Uses v2
→ Generate Tax Return
→ Verify Tax Transaction Aggregation
```

---

# 101. Critical Template Test

```text
Create Platform Template
→ Publish v1
→ Tenant Overrides Template
→ Publish Tenant v1
→ Office Overrides Tenant
→ Publish Office v1
→ Trigger Notification
→ Verify Office Template Resolved
→ Remove Office Override
→ Verify Tenant Template Resolved
→ Remove Tenant Override
→ Verify Platform Template Resolved
```

---

# 102. Critical Tenant Isolation Test

```text
Tenant A creates Client A
Tenant B creates Client B

Tenant A:
→ Can access Client A
→ Cannot access Client B

Tenant B:
→ Can access Client B
→ Cannot access Client A
```

Test:

- REST endpoints
- relationships
- search
- exports
- queues
- files
- notifications
- reports
- cache
- background jobs

---

# 103. Error and Empty States

Every page must have:

```text
Loading
Empty
Error
Permission Denied
Not Found
Success
Partial Data
Offline / Network Error where applicable
```

Empty states must provide a useful action:

```text
No clients yet
[Add Client]
```

Do not show blank screens.

---

# 104. Audit + Activity UI

For major records create an activity timeline:

```text
Created
Assigned
Updated
Document Uploaded
Comment Added
Status Changed
Approved
Rejected
Invoice Generated
Payment Received
Filed
```

Display:

- User
- Event
- Timestamp
- Related record
- Description

Sensitive audit data should remain restricted.

---

# 105. API Performance

Optimize:

- Pagination
- Eager loading
- Query scopes
- Indexes
- Caching
- Queue processing
- Bulk operations
- Large exports
- Large imports
- Report queries

Do not prematurely optimize with raw SQL.

First use proper Eloquent/query-builder design.

Use profiling before introducing custom SQL.

---

# 106. Caching

Cache:

- Platform settings
- Tenant settings
- Office settings
- Permission maps
- Module entitlements
- Tax rules where safe
- Template resolution where safe
- Static reference data

Cache keys must include tenant context where tenant-specific.

Example:

```text
tenant:{tenant_id}:settings
tenant:{tenant_id}:permissions
tenant:{tenant_id}:templates:{code}
```

Invalidate cache when source configuration changes.

---

# 107. Queue Architecture

Use queues for:

```text
Emails
SMS
Notifications
Imports
Exports
PDF generation
Document processing
Virus scanning
Deadline generation
Workflow execution
Recurring invoices
Reminders
Scheduled reports
Tax return aggregation where heavy
AI processing
```

Every queued job that touches tenant data must preserve tenant context safely.

---

# 108. Idempotency

Critical operations must be idempotent.

Examples:

- Payment webhooks
- Subscription webhooks
- Recurring invoice generation
- Notification delivery
- Filing submission
- Import jobs
- Workflow execution
- External integration callbacks

Use unique event IDs / idempotency keys.

Never create duplicate financial records because a webhook was delivered twice.

---

# 109. Reports

## Practice Reports

```text
Client Growth
Lead Conversion
Filing Completion
Overdue Filings
Staff Productivity
Staff Utilization
Workflow Bottlenecks
Document Turnaround
Revenue by Service
Engagement Profitability
```

## Tax Reports

```text
Tax Liability
Tax Transactions
Tax Returns
Tax Obligations
Tax by Jurisdiction
Tax by Client
Withholding Summary
Exemption Summary
Upcoming Tax Deadlines
```

## Accounting Reports

```text
Trial Balance
General Ledger
Balance Sheet
Profit & Loss
Cash Flow
AR Aging
AP Aging
Account Activity
Revenue by Service
Expense by Category
```

Reports support:

- Filters
- Date range
- Jurisdiction
- Client
- Entity
- Service
- Staff
- Drill-down
- Export
- Print
- Scheduled delivery

---

# 110. AI Architecture

AI features are optional.

Possible capabilities:

```text
Client Activity Summary
Missing Document Detection
Overdue Work Detection
Workflow Bottleneck Analysis
Workload Balancing Suggestions
Client Communication Drafting
Document Classification
Tax Data Extraction Assistance
Invoice / Expense Categorization Suggestions
```

AI must never silently:

- Post journal entries
- Delete data
- Send payments
- Change financial records
- Submit tax filings

Use:

```text
AI Suggestion
→ User Review
→ User Approval
→ Action
```

Every AI action should be auditable.

---

# 111. Integrations Architecture

Create integration abstractions.

Potential integrations:

```text
Email Providers
SMS Providers
Payment Gateways
Google Calendar
Microsoft Calendar
Cloud Storage
Bank APIs
Accounting Import Sources
E-Filing Providers
Identity Providers
Webhooks
AI Providers
```

Store credentials securely.

Never expose provider secrets to the frontend.

---

# 112. Webhooks

Create:

```text
webhook_endpoints
webhook_events
webhook_deliveries
webhook_attempts
```

Support:

- Signing secrets
- Event types
- Retry
- Backoff
- Delivery status
- Response code
- Delivery logs

Tenant webhook payloads must never leak data from another tenant.

---

# 113. API Documentation

Generate OpenAPI documentation.

Every endpoint should document:

- Authentication
- Permission
- Parameters
- Request schema
- Validation
- Response schema
- Errors
- Pagination
- Filters
- Examples

---

# 114. Route Architecture

Next.js:

```text
app/
├── (marketing)/
├── (auth)/
├── (platform)/
│   └── platform/
├── (dashboard)/
│   └── app/
└── (portal)/
    └── portal/
```

Dashboard modules:

```text
dashboard
clients
leads
entities
engagements
services
filings
taxation
deadlines
workflows
tasks
time
calendar
documents
templates
communications
appointments
proposals
signatures
accounting
banking
invoices
payments
expenses
vendors
reports
team
offices
imports
exports
settings
```

Portal:

```text
dashboard
profile
tax-profile
filings
documents
messages
appointments
proposals
engagements
signatures
invoices
payments
notifications
```

---

# 115. Frontend API Layer

Do not call APIs directly from every component.

Use:

```text
features/{module}/api/
features/{module}/hooks/
features/{module}/schemas/
features/{module}/types/
```

Example:

```text
features/clients/
├── api/
│   └── clients-api.ts
├── hooks/
│   ├── use-clients.ts
│   ├── use-client.ts
│   ├── use-create-client.ts
│   └── use-update-client.ts
├── schemas/
│   └── client-schema.ts
├── components/
└── pages/
```

Use TanStack Query for server state.

Use Zustand only for client-side state that genuinely needs it.

---

# 116. Permissions in Frontend

Create a central permission helper:

```ts
can("clients.create")
can("clients.update")
can("templates.publish")
can("taxes.manage")
```

Hide unauthorized UI actions.

But backend remains final authority.

---

# 117. Security for Files

For every file:

- Validate extension
- Validate MIME type
- Validate size
- Scan where supported
- Store outside public web root
- Use tenant-aware path
- Use signed URLs
- Check authorization before every access
- Log sensitive downloads
- Apply retention policy

---

# 118. Data Privacy

Sensitive information may include:

- Tax identifiers
- Banking information
- Payment information
- Personal information
- Documents
- Authentication data

Apply:

- Encryption where appropriate
- Least privilege
- Access logging
- Masking
- Secure storage
- Secure deletion where legally permitted

Never display sensitive identifiers unnecessarily.

---

# 119. Professional CPA Dashboard

The main firm dashboard should immediately answer:

```text
How is the practice performing?
What deadlines are approaching?
What work is overdue?
What needs client input?
What is the team working on?
What revenue is outstanding?
How profitable are engagements?
What tax obligations are coming?
```

Top-level KPI cards:

```text
Active Clients
Open Engagements
Upcoming Deadlines
Overdue Filings
Pending Client Documents
Unpaid Invoices
MTD Revenue
Billable Utilization
```

---

# 120. Recommended Additional CPA Features

Add these as configurable modules because they materially improve a professional CPA platform:

## A. Client Risk / Compliance

- Risk rating
- Review status
- Compliance checklist
- Risk history
- Required approvals

## B. Client Organizer

Annual questionnaire:

```text
Personal Details
Income
Employment
Investments
Property
Dependents
Deductions
Business Activity
Other Tax Information
```

Allow reusable organizer templates.

## C. Recurring Compliance Calendar

Automatically create recurring obligations.

## D. Engagement Profitability

Compare:

```text
Quoted Fee
Actual Time
Staff Cost
Expenses
Revenue
Gross Margin
```

## E. SLA Management

Track:

```text
Created
First Response
Due
Completed
SLA Breach
```

## F. Client Health Score

Configurable scoring:

```text
Payment History
Document Responsiveness
Filing Compliance
Engagement Activity
Communication
Outstanding Balance
```

Do not expose internal health scoring to clients unless explicitly configured.

## G. Knowledge Base

Tenant/private knowledge:

- Procedures
- Internal guides
- Checklists
- Tax notes
- Templates
- FAQs

## H. Internal Notes

Allow rich internal notes attached to:

- Client
- Entity
- Engagement
- Filing
- Invoice
- Tax Return

Never expose internal notes through portal resources.

---

# 121. Recommended Database Modules

The database should broadly contain:

```text
Identity
users
roles
permissions
role_user
permission_role
sessions
login_attempts

Tenancy
tenants
tenant_users
offices
teams
departments
tenant_settings
office_settings
user_preferences

CRM
leads
lead_sources
lead_activities
parties
persons
organizations
clients
contacts
relationships
tags
taggables
custom_fields
custom_field_values

Practice
services
engagements
engagement_services
engagement_team
filings
filing_types
filing_status_histories
deadlines
deadline_rules
deadline_instances
tasks
task_dependencies
task_comments
time_entries
capacity_plans

Tax
tax_profiles
tax_profile_identifiers
tax_residencies
tax_registrations
tax_jurisdictions
tax_authorities
tax_types
tax_categories
tax_rates
tax_rules
tax_rule_components
tax_exemptions
tax_transactions
tax_transaction_lines
tax_periods
tax_returns
tax_return_lines
tax_obligations

Accounting
fiscal_years
accounting_periods
chart_of_accounts
journal_entries
journal_entry_lines
bank_accounts
bank_transactions
bank_reconciliations
reconciliation_lines
customers
vendors
invoices
invoice_items
credit_notes
payments
payment_allocations
refunds
write_offs
bills
bill_items
expenses
expense_categories

Documents
document_folders
documents
document_versions
document_requests
document_request_items
document_shares
retention_policies
legal_holds

Templates
template_categories
templates
template_versions
template_variables
template_assignments
template_localizations
template_render_logs

Communication
conversations
conversation_participants
messages
message_attachments
email_logs
sms_logs

Notifications
notifications
notification_templates
notification_deliveries
notification_preferences
notification_events
notification_digests

Workflow
workflow_templates
workflow_versions
workflow_stages
workflow_rules
workflow_actions
workflow_runs
workflow_run_steps

Proposals
proposals
proposal_items
proposal_versions
proposal_approvals

Signatures
signature_requests
signature_participants
signature_fields
signature_events
signature_certificates

Scheduling
appointment_types
appointments
availability_rules
calendar_connections

Platform
modules
feature_flags
plans
plan_features
subscriptions
subscription_items
subscription_events
webhook_endpoints
webhook_events

Operations
imports
import_jobs
import_errors
exports
audit_logs
system_settings
```

Do not create every table blindly if a normalized relationship can be designed better. Review relationships and constraints before migration generation.

---

# 122. Eloquent Relationship Requirements

Every model must explicitly define its meaningful relationships.

Examples:

```php
class Client extends Model
{
    public function tenant(): BelongsTo {}
    public function contacts(): HasMany {}
    public function entities(): HasMany {}
    public function engagements(): HasMany {}
    public function filings(): HasMany {}
    public function invoices(): HasMany {}
    public function documents(): MorphMany {}
    public function tags(): MorphToMany {}
    public function activities(): MorphMany {}
}
```

Use relationship methods rather than manually joining unrelated tables throughout the application.

Create reusable scopes:

```php
scopeActive()
scopeForTenant()
scopeOverdue()
scopeUpcoming()
scopePublished()
scopeForJurisdiction()
```

---

# 123. Financial Integrity

All financial mutations must use database transactions.

Flow:

```text
Request
→ Authorization
→ Validation
→ Database Transaction
→ Business Rules
→ Financial Integrity Validation
→ Post
→ Generate Ledger Impact
→ Commit
→ Audit Event
```

If anything fails, rollback the entire operation.

Never partially post a financial transaction.

---

# 124. Historical Integrity

Historical records must preserve:

- Original amounts
- Original tax
- Original exchange rate
- Original rule/version
- Original template version where applicable
- User who performed the action
- Timestamp
- Audit history

Do not reconstruct history solely from current configuration.

---

# 125. Development Quality Rules

Do not:

- Put complex business logic in controllers
- Use raw SQL without justification
- Trust frontend tenant IDs
- Trust frontend permissions
- Use floating-point money
- Delete posted financial history
- Delete completed filing history
- Hard-code tax rates
- Hard-code notification content throughout the application
- Hard-code form labels everywhere
- Duplicate validation rules across components
- Expose internal notes to clients
- Return entire Eloquent models blindly
- Create N+1 queries
- Generate fake production data
- Build fake placeholder APIs as the final architecture

Do:

- Use Eloquent relationships
- Use services/actions
- Use policies
- Use Form Requests
- Use API Resources
- Use events/jobs
- Use transactions
- Use audit logs
- Use tenant-aware caching
- Use reusable components
- Use centralized form configuration
- Use template resolution
- Use versioning
- Use idempotency
- Write tests

---

# 126. Definition of Done

A module is not complete until it contains:

1. Database migrations
2. Database constraints
3. Models
4. Eloquent relationships
5. Factories
6. Seeders where needed
7. Form Requests
8. Policies
9. Actions / Services
10. Events / Listeners where needed
11. Queue jobs where needed
12. API Controllers
13. API Resources
14. Routes
15. OpenAPI documentation
16. Unit tests
17. Feature tests
18. Tenant isolation tests
19. Permission tests
20. Audit logging
21. Frontend API service
22. TanStack Query hooks
23. Zod schema
24. Listing page
25. Create page
26. Edit page
27. View/detail page
28. Delete/archive behavior
29. Restore where applicable
30. Import where applicable
31. Export where applicable
32. Print where applicable
33. Loading state
34. Empty state
35. Error state
36. Permission state
37. Responsive UI
38. Accessibility
39. Custom validation/error UX
40. Documentation

---

# 127. Development Phases

The phases are logical milestones, but implementation should proceed in parallel by workstream.

## Phase 1 — Foundation

- Laravel API
- Next.js
- PostgreSQL
- Redis
- Authentication
- RBAC
- Permissions
- Tenant context
- Settings
- Module registry
- Feature flags
- Audit logging
- Error handling
- Form configuration
- UI design system

## Phase 2 — CRM

- Leads
- Clients
- Contacts
- Entities
- Relationships
- Services
- Onboarding

## Phase 3 — Practice Management

- Engagements
- Filings
- Tax obligations
- Deadline engine
- Workflow
- Tasks
- Time
- Capacity
- Calendar

## Phase 4 — Taxation

- Tax profiles
- Jurisdictions
- Tax authorities
- Tax types
- Tax categories
- Tax rules
- Tax rates
- Exemptions
- Tax transactions
- Tax returns
- Tax reports

## Phase 5 — Documents

- Storage
- Folders
- Requests
- Versioning
- Secure sharing
- Retention
- Legal hold
- E-signature

## Phase 6 — Accounting

- Chart of Accounts
- Fiscal periods
- Journal entries
- General Ledger
- Trial Balance
- P&L
- Balance Sheet
- Cash Flow

## Phase 7 — Billing

- Proposals
- Quotations
- Engagement billing
- Invoices
- Recurring invoices
- Payments
- AR/AP
- Expenses

## Phase 8 — Banking

- Bank accounts
- Imports
- Categorization
- Matching
- Reconciliation

## Phase 9 — Templates / Communications

- Template engine
- Email
- SMS
- Notifications
- Messaging
- Notification preferences

## Phase 10 — Reporting / Analytics / AI

- Practice analytics
- Tax reports
- Financial reports
- Staff analytics
- Scheduled reports
- AI assistant

---

# 128. Final Implementation Architecture

Use this overall pattern:

```text
Next.js UI
    ↓
Feature Component
    ↓
Form Configuration
    ↓
Zod Validation
    ↓
API Service
    ↓
TanStack Query
    ↓
Laravel API
    ↓
Middleware
    ↓
Tenant Context
    ↓
Form Request
    ↓
Policy
    ↓
Action / Service
    ↓
Eloquent Models + Relationships
    ↓
Database Transaction
    ↓
Domain Event
    ↓
Queue / Listener
    ↓
Notification / Integration
    ↓
Audit Log
```

Financial:

```text
Request
→ Authorization
→ Validation
→ Transaction
→ Financial Integrity
→ Tax Calculation
→ Accounting Posting
→ Ledger Impact
→ Commit
→ Audit
```

Template:

```text
Event
→ Template Resolver
→ Office Override
→ Tenant Override
→ Platform Default
→ Version
→ Variable Validation
→ Render
→ Channel Adapter
→ Queue
→ Delivery
→ Log
```

Tax:

```text
Transaction
→ Client / Entity
→ Tax Profile
→ Jurisdiction
→ Tax Rule Resolver
→ Effective Date
→ Tax Rate
→ Exemption
→ Calculation
→ Tax Transaction
→ Accounting Posting
→ Audit
```

---

# 129. Final Product Standard

The final product must look and behave like a **premium international enterprise CPA platform**.

It must not feel like:

- A generic CRUD application
- A template admin dashboard
- A simple invoicing app
- A basic CRM
- A static prototype

It must feel like:

```text
CPA CRM
+
Practice Management
+
Tax Compliance
+
Accounting
+
Billing
+
Document Management
+
Client Portal
+
Workflow Automation
+
Communication
+
Reporting
+
Enterprise Administration
```

The platform must support configuration rather than hard-coding wherever practical.

The most important architectural principles are:

```text
Eloquent-first
Tenant-safe
API-first
Permission-first
Audit-first
Configuration-driven
Template-driven
Event-driven
Queue-aware
Financially immutable
Tax-rule versioned
Responsive
Accessible
Testable
Scalable
```

---

# 130. Master Acceptance Checklist

Before considering the platform production-ready, verify:

## Architecture

- [ ] Single Next.js application
- [ ] Laravel API-first backend
- [ ] PostgreSQL production database
- [ ] Redis
- [ ] Queue / Horizon
- [ ] Scheduler
- [ ] Object storage
- [ ] OpenAPI

## Multi-Tenancy

- [ ] Tenant context
- [ ] Tenant isolation
- [ ] Tenant-aware files
- [ ] Tenant-aware queues
- [ ] Tenant-aware cache
- [ ] Tenant isolation tests

## Security

- [ ] MFA
- [ ] Password reset
- [ ] Rate limiting
- [ ] RBAC
- [ ] Policies
- [ ] Audit logs
- [ ] Signed URLs
- [ ] Secure file access

## CRM

- [ ] Leads
- [ ] Clients
- [ ] Contacts
- [ ] Entities
- [ ] Relationships
- [ ] Onboarding
- [ ] Engagements
- [ ] Services

## Tax

- [ ] Tax profiles
- [ ] Tax jurisdictions
- [ ] Tax authorities
- [ ] Tax types
- [ ] Tax categories
- [ ] Tax rates
- [ ] Tax rules
- [ ] Effective dating
- [ ] Rule versioning
- [ ] Exemptions
- [ ] Tax transactions
- [ ] Tax obligations
- [ ] Tax returns
- [ ] Tax reports

## Accounting

- [ ] Chart of Accounts
- [ ] Fiscal years
- [ ] Periods
- [ ] Journal entries
- [ ] Double-entry validation
- [ ] General Ledger
- [ ] Trial Balance
- [ ] P&L
- [ ] Balance Sheet
- [ ] Cash Flow
- [ ] AR
- [ ] AP
- [ ] Banking
- [ ] Reconciliation
- [ ] Multi-currency

## Practice

- [ ] Filings
- [ ] Deadline engine
- [ ] Workflows
- [ ] Tasks
- [ ] Time tracking
- [ ] Capacity
- [ ] Calendar
- [ ] SLA

## Templates

- [ ] Platform templates
- [ ] Tenant overrides
- [ ] Office overrides
- [ ] Template variables
- [ ] Versioning
- [ ] Preview
- [ ] Test send
- [ ] Email
- [ ] SMS
- [ ] Notifications
- [ ] Documents
- [ ] Invoices
- [ ] Quotations
- [ ] Engagement letters
- [ ] Workflow templates

## Notifications

- [ ] In-app
- [ ] Email
- [ ] SMS
- [ ] Preferences
- [ ] Delivery tracking
- [ ] Queue processing
- [ ] Digests

## Documents

- [ ] Folder hierarchy
- [ ] Requests
- [ ] Versioning
- [ ] Secure sharing
- [ ] Retention
- [ ] Legal hold
- [ ] Recycle bin
- [ ] E-signatures

## Frontend

- [ ] Supplied teal palette
- [ ] Inter typography
- [ ] Lucide icons
- [ ] Recharts
- [ ] Responsive
- [ ] Accessible
- [ ] Reusable forms
- [ ] Centralized field config
- [ ] Custom validation
- [ ] Error-in-placeholder UX
- [ ] Data tables
- [ ] Charts
- [ ] Command palette
- [ ] Notification center
- [ ] Loading states
- [ ] Empty states
- [ ] Error states

## Quality

- [ ] Eloquent relationships
- [ ] No unnecessary raw SQL
- [ ] No N+1
- [ ] Financial transactions atomic
- [ ] Idempotent jobs
- [ ] Audit trail
- [ ] API tests
- [ ] Tenant isolation tests
- [ ] Permission tests
- [ ] Tax tests
- [ ] Template resolution tests
- [ ] Workflow tests
- [ ] Accessibility tests

---

# 131. Non-Negotiable Final Rules

1. **Use Eloquent relationships by default.**
2. **Use raw SQL only with a documented technical/performance reason.**
3. **Never trust frontend tenant IDs.**
4. **Never trust frontend permissions.**
5. **Never use floating-point money.**
6. **Never delete posted financial history.**
7. **Never overwrite historical tax rules used by posted transactions.**
8. **Never hard-code tenant-specific templates.**
9. **Never hard-code form labels/placeholders/errors throughout components.**
10. **Never expose internal notes through client APIs.**
11. **Never put complex business logic in controllers.**
12. **Never process large imports/exports synchronously.**
13. **Never allow duplicate webhook-driven financial transactions.**
14. **Never expose raw storage paths.**
15. **Never use browser-native required validation as the application's validation system.**
16. **Always use custom validation and custom error rendering.**
17. **Always audit sensitive and financial actions.**
18. **Always preserve tenant context in queues and cache.**
19. **Always version templates and tax rules where historical consistency matters.**
20. **Build frontend, backend, database, API, tests and permissions as parallel vertical slices.**
21. **Do not generate a fake prototype.**
22. **Every module must be production-quality and end-to-end.**

---

# 132. Final Instruction to the Development Agent

Implement this platform **module by module, in parallel workstreams wherever dependencies allow**, while maintaining a single coherent architecture.

Do not simplify the system into generic CRUD.

Do not skip database relationships, permissions, audit trails, validation, settings, templates, taxation, notifications, financial integrity or tenant isolation.

When implementing any feature, ask:

```text
Who owns this data?
Who can view it?
Who can modify it?
Who can approve it?
What happens historically?
What audit event is generated?
What notification is triggered?
Which template is resolved?
Which settings apply?
Does tax apply?
Does accounting apply?
Does the client portal see it?
Can it be restored?
Can it be archived?
Can it be reversed?
Does it require a queue?
Does it need idempotency?
Does it need an Eloquent relationship?
Does it need a database constraint?
Does it need a test?
```

The resulting system must be a **real, extensible, enterprise-grade CPA CRM and accounting practice platform**, with a calm premium teal UI, centralized configuration, professional accounting controls, country-neutral taxation, powerful templates, robust notifications, secure client collaboration and a clean Laravel Eloquent architecture.



```text
Create Parallel Frontend & Backend both. Each module complete according to Backend API & Then frontend with proper integrations. Everytime clear previous build & make new build. 

Also, convert .env to .smartfox and .smartfox work like .env and keep routes proper each module based. so, easy to track. After development of API's & Frontned also create postman collection. 
````