<?php

/**
 * Pagination & listing defaults for the entire application.
 *
 * Use in controllers:
 *   $perPage = request()->integer('per_page', config('pagination.default_per_page'));
 *   $perPage = min($perPage, config('pagination.max_per_page'));
 *
 * Or via the PaginationHelper:
 *   PaginationHelper::perPage()
 *   PaginationHelper::sortBy('name', ['name', 'email', 'created_at'])
 */
return [

    // ── Default rows per page ─────────────────────────────────────────────────
    'default_per_page' => 25,

    // ── Maximum rows per page a client can request ────────────────────────────
    'max_per_page' => 200,

    // ── Allowed per-page options (sent to frontend for the dropdown) ──────────
    'per_page_options' => [10, 25, 50, 100],

    // ── Default sort direction ────────────────────────────────────────────────
    'default_sort_direction' => 'desc',   // 'asc' | 'desc'

    // ── Simple pagination threshold (use simplePaginate above this limit) ─────
    'simple_paginate_threshold' => 5000,

    // ── Per-module defaults ───────────────────────────────────────────────────
    'modules' => [
        'clients'      => ['per_page' => 25, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
        'leads'        => ['per_page' => 25, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
        'filings'      => ['per_page' => 50, 'sort_by' => 'deadline_at', 'sort_dir' => 'asc'],
        'tasks'        => ['per_page' => 25, 'sort_by' => 'due_date',   'sort_dir' => 'asc'],
        'invoices'     => ['per_page' => 25, 'sort_by' => 'issued_at',  'sort_dir' => 'desc'],
        'blog_posts'   => ['per_page' => 9,  'sort_by' => 'published_at','sort_dir' => 'desc'],
        'audit_logs'   => ['per_page' => 50, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
        'tenants'      => ['per_page' => 25, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
        'users'        => ['per_page' => 25, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
        'notifications'=> ['per_page' => 25, 'sort_by' => 'created_at', 'sort_dir' => 'desc'],
    ],

];
