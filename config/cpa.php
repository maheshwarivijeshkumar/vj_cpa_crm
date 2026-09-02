<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CPA CRM Application Settings
    |--------------------------------------------------------------------------
    */

    // Free trial length in days for new tenants
    'trial_days' => (int) env('TRIAL_DAYS', 14),

    // Whether new firm self-registration is open
    'registration_open' => (bool) env('REGISTRATION_OPEN', true),

    // Platform branding
    'platform_name'    => env('APP_NAME', 'VJ CPA CRM'),
    'support_email'    => env('SUPPORT_EMAIL', 'support@cpacrm.com'),
    'support_url'      => env('SUPPORT_URL', 'https://cpacrm.com/support'),

    // Auth settings
    'max_login_attempts' => (int) env('MAX_LOGIN_ATTEMPTS', 5),
    'login_lockout_minutes' => (int) env('LOGIN_LOCKOUT_MINUTES', 15),

    // SEO defaults
    'seo' => [
        'title_suffix'       => env('SEO_TITLE_SUFFIX', ' — VJ CPA CRM'),
        'default_title'      => env('SEO_DEFAULT_TITLE', 'VJ CPA CRM — Enterprise CPA Practice Management'),
        'default_description'=> env('SEO_DEFAULT_DESCRIPTION', 'The modern practice management platform for CPA firms. Manage clients, filings, workflows, documents, accounting and more.'),
        'default_image'      => env('SEO_DEFAULT_IMAGE', '/images/og-default.png'),
        'twitter_handle'     => env('SEO_TWITTER_HANDLE', '@cpacrm'),
    ],

];
