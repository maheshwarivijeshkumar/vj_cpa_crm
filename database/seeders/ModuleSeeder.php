<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // code, name, is_core, is_enabled, dependencies, sort_order
            ['crm',           'CRM',                          true,  true,  [],                                    10],
            ['clients',       'Client Management',            true,  true,  ['crm'],                               20],
            ['contacts',      'Contacts',                     true,  true,  ['clients'],                           30],
            ['entities',      'Entities',                     true,  true,  ['clients'],                           40],
            ['services',      'Service Catalog',              true,  true,  [],                                    50],
            ['engagements',   'Engagement Management',        true,  true,  ['clients'],                           60],
            ['filings',       'Filing Engine',                true,  true,  ['clients', 'entities'],               70],
            ['taxation',      'Taxation Engine',              true,  true,  ['clients', 'entities', 'filings'],    80],
            ['deadlines',     'Deadline Engine',              true,  true,  ['filings'],                           90],
            ['workflows',     'Workflow Engine',              true,  true,  [],                                    100],
            ['tasks',         'Task Management',              true,  true,  [],                                    110],
            ['time',          'Time Tracking',                false, true,  ['engagements'],                       120],
            ['capacity',      'Capacity Planning',            false, true,  ['time'],                              130],
            ['calendar',      'Scheduling & Calendar',        false, true,  [],                                    140],
            ['documents',     'Document Management',          true,  true,  ['clients'],                           150],
            ['esignatures',   'E-Signatures',                 false, true,  ['documents'],                         160],
            ['templates',     'Template Engine',              true,  true,  [],                                    170],
            ['communications','Communications',               true,  true,  ['clients'],                           180],
            ['notifications', 'Notifications',                true,  true,  ['templates'],                         190],
            ['proposals',     'Proposals & Quotations',       false, true,  ['clients', 'services'],               200],
            ['accounting',    'Accounting Engine',            true,  true,  ['clients'],                           210],
            ['banking',       'Banking & Reconciliation',     false, true,  ['accounting'],                        220],
            ['invoicing',     'Invoicing',                    true,  true,  ['clients', 'services', 'accounting'], 230],
            ['payments',      'Payments',                     true,  true,  ['invoicing'],                         240],
            ['expenses',      'Expenses',                     false, true,  ['accounting'],                        250],
            ['reports',       'Reports & Analytics',          true,  true,  [],                                    260],
            ['portal',        'Client Portal',                false, true,  ['clients', 'documents', 'communications'], 270],
            ['imports',       'Import Engine',                true,  true,  [],                                    280],
            ['exports',       'Export Engine',                true,  true,  [],                                    290],
            ['ai',            'AI Assistant',                 false, false, [],                                    300],
            ['subscriptions', 'Subscription Billing',        true,  true,  [],                                    310],
            ['webhooks',      'Webhooks',                     false, true,  [],                                    320],
        ];

        $now = now();

        foreach ($modules as [$code, $name, $isCore, $isEnabled, $dependencies, $sortOrder]) {
            DB::table('modules')->updateOrInsert(
                ['code' => $code],
                [
                    'name'         => $name,
                    'is_core'      => $isCore,
                    'is_enabled'   => $isEnabled,
                    'dependencies' => json_encode($dependencies),
                    'sort_order'   => $sortOrder,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]
            );
        }
    }
}
