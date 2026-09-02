<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permission definitions ─────────────────────────────────────────────
        // Format: 'module.action'
        // Standard actions: viewAny, view, create, update, delete, restore, forceDelete
        // Extra module-specific actions listed where needed

        $permissions = [
            // Platform
            'platform.manage',
            'platform.view_audit_logs',
            'platform.manage_tenants',
            'platform.manage_plans',
            'platform.manage_modules',
            'platform.manage_settings',
            'platform.impersonate',

            // Users & Access
            'users.viewAny', 'users.view', 'users.create', 'users.update',
            'users.delete', 'users.restore', 'users.invite', 'users.impersonate',
            'users.manage_roles', 'users.manage_permissions',

            // Roles
            'roles.viewAny', 'roles.view', 'roles.create', 'roles.update', 'roles.delete',

            // Offices
            'offices.viewAny', 'offices.view', 'offices.create', 'offices.update', 'offices.delete',

            // Settings
            'settings.view', 'settings.update',

            // CRM — Leads
            'leads.viewAny', 'leads.view', 'leads.create', 'leads.update',
            'leads.delete', 'leads.restore', 'leads.convert', 'leads.import', 'leads.export',

            // CRM — Clients
            'clients.viewAny', 'clients.view', 'clients.create', 'clients.update',
            'clients.delete', 'clients.restore', 'clients.import', 'clients.export',
            'clients.view_internal_notes', 'clients.manage_tags',

            // Contacts
            'contacts.viewAny', 'contacts.view', 'contacts.create', 'contacts.update',
            'contacts.delete', 'contacts.restore',

            // Entities
            'entities.viewAny', 'entities.view', 'entities.create', 'entities.update',
            'entities.delete', 'entities.restore',

            // Services
            'services.viewAny', 'services.view', 'services.create', 'services.update',
            'services.delete', 'services.restore',

            // Engagements
            'engagements.viewAny', 'engagements.view', 'engagements.create', 'engagements.update',
            'engagements.delete', 'engagements.restore', 'engagements.manage_team',
            'engagements.sign', 'engagements.export',

            // Filings
            'filings.viewAny', 'filings.view', 'filings.create', 'filings.update',
            'filings.delete', 'filings.restore', 'filings.submit', 'filings.approve',
            'filings.import', 'filings.export',

            // Taxation
            'taxation.viewAny', 'taxation.view', 'taxation.create', 'taxation.update',
            'taxation.delete', 'taxation.manage_rules', 'taxation.manage_rates',
            'taxation.manage_profiles', 'taxation.generate_returns',

            // Deadlines
            'deadlines.viewAny', 'deadlines.view', 'deadlines.create', 'deadlines.update',
            'deadlines.delete', 'deadlines.extend', 'deadlines.reassign',

            // Workflows
            'workflows.viewAny', 'workflows.view', 'workflows.create', 'workflows.update',
            'workflows.delete', 'workflows.publish', 'workflows.execute',

            // Tasks
            'tasks.viewAny', 'tasks.view', 'tasks.create', 'tasks.update',
            'tasks.delete', 'tasks.restore', 'tasks.complete', 'tasks.reassign',
            'tasks.view_all_users',

            // Time Tracking
            'time.viewAny', 'time.view', 'time.create', 'time.update',
            'time.delete', 'time.approve', 'time.lock', 'time.view_all_users',

            // Calendar
            'calendar.viewAny', 'calendar.view', 'calendar.create', 'calendar.update',
            'calendar.delete', 'calendar.manage_types', 'calendar.view_all_users',

            // Capacity
            'capacity.viewAny', 'capacity.view', 'capacity.manage',

            // Documents
            'documents.viewAny', 'documents.view', 'documents.create', 'documents.update',
            'documents.delete', 'documents.restore', 'documents.download',
            'documents.share', 'documents.request', 'documents.manage_retention',

            // E-Signatures
            'esignatures.viewAny', 'esignatures.view', 'esignatures.create',
            'esignatures.send', 'esignatures.void', 'esignatures.download_certificate',

            // Templates
            'templates.viewAny', 'templates.view', 'templates.create', 'templates.update',
            'templates.delete', 'templates.publish', 'templates.manage_platform',

            // Communications
            'communications.viewAny', 'communications.view', 'communications.create',
            'communications.send', 'communications.delete',

            // Notifications
            'notifications.viewAny', 'notifications.view', 'notifications.mark_read',
            'notifications.manage_settings',

            // Proposals
            'proposals.viewAny', 'proposals.view', 'proposals.create', 'proposals.update',
            'proposals.delete', 'proposals.send', 'proposals.accept', 'proposals.decline',

            // Accounting
            'accounting.viewAny', 'accounting.view', 'accounting.create', 'accounting.update',
            'accounting.delete', 'accounting.post', 'accounting.reverse',
            'accounting.manage_coa', 'accounting.manage_periods', 'accounting.lock_period',
            'accounting.view_reports',

            // Banking
            'banking.viewAny', 'banking.view', 'banking.create', 'banking.update',
            'banking.delete', 'banking.reconcile', 'banking.import',

            // Invoicing
            'invoicing.viewAny', 'invoicing.view', 'invoicing.create', 'invoicing.update',
            'invoicing.delete', 'invoicing.send', 'invoicing.void', 'invoicing.export',
            'invoicing.manage_recurring',

            // Payments
            'payments.viewAny', 'payments.view', 'payments.create', 'payments.update',
            'payments.delete', 'payments.refund', 'payments.write_off', 'payments.export',

            // Expenses
            'expenses.viewAny', 'expenses.view', 'expenses.create', 'expenses.update',
            'expenses.delete', 'expenses.approve', 'expenses.reject',
            'expenses.view_all_users', 'expenses.export',

            // Reports
            'reports.viewAny', 'reports.view', 'reports.export', 'reports.schedule',
            'reports.view_financial', 'reports.view_practice', 'reports.view_staff',

            // Imports
            'imports.create', 'imports.view', 'imports.delete',

            // Exports
            'exports.create', 'exports.view', 'exports.download', 'exports.delete',

            // Client Portal
            'portal.viewAny', 'portal.view', 'portal.manage',

            // Webhooks
            'webhooks.viewAny', 'webhooks.view', 'webhooks.create',
            'webhooks.update', 'webhooks.delete', 'webhooks.view_logs',

            // AI
            'ai.use', 'ai.manage',

            // Subscriptions
            'subscriptions.view', 'subscriptions.manage',

            // Audit Logs
            'audit_logs.viewAny', 'audit_logs.view', 'audit_logs.export',
        ];

        $now = now();

        foreach ($permissions as $name) {
            [$module, $action] = array_pad(explode('.', $name, 2), 2, '');

            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'uuid'        => (string) Str::uuid(),
                    'name'        => $name,
                    'guard'       => 'web',
                    'module'      => $module,
                    'action'      => $action,
                    'description' => ucfirst(str_replace(['.', '_'], [' — ', ' '], $name)),
                    'is_system'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );
        }
    }
}
