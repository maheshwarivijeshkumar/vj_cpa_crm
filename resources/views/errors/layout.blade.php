<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Error' }} — {{ config('app.name', 'VJ CPA CRM') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cpa-dark:        #055E5A;
            --cpa-medium-dark: #1D9792;
            --cpa-medium:      #48BCB9;
            --cpa-light:       #C5E8E5;
            --cpa-very-light:  #E6F5F4;
            --cpa-bg:          #F4FAFA;
            --cpa-white:       #FEFDFD;
            --cpa-border:      #D4ECEA;
            --cpa-text:        #0D2B2A;
            --cpa-muted:       #6B9294;
            --cpa-danger:      #DC2626;
            --cpa-warning:     #D97706;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--cpa-bg);
            color: var(--cpa-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: var(--cpa-dark);
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
        }
        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
        }
        .header-logo-mark {
            width: 36px;
            height: 36px;
            background: var(--cpa-medium);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .header-logo-name {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -0.2px;
        }

        /* Main content */
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }
        .error-card {
            background: var(--cpa-white);
            border: 1px solid var(--cpa-border);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(2, 62, 60, 0.07);
        }

        .error-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 30px;
        }
        .error-icon-info    { background: var(--cpa-very-light); color: var(--cpa-dark); }
        .error-icon-warning { background: #FEF3C7; color: var(--cpa-warning); }
        .error-icon-danger  { background: #FEE2E2; color: var(--cpa-danger); }
        .error-icon-server  { background: #F3F4F6; color: #374151; }

        .error-code {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--cpa-medium-dark);
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--cpa-text);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .error-message {
            font-size: 15px;
            color: var(--cpa-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* Actions */
        .error-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
        }
        .btn-primary {
            background: var(--cpa-medium-dark);
            color: #fff;
        }
        .btn-primary:hover { background: var(--cpa-dark); }
        .btn-secondary {
            background: var(--cpa-very-light);
            color: var(--cpa-dark);
            border: 1px solid var(--cpa-border);
        }
        .btn-secondary:hover { background: var(--cpa-light); }

        /* Divider */
        .divider {
            height: 1px;
            background: var(--cpa-border);
            margin: 2rem 0;
        }
        .help-text {
            font-size: 13px;
            color: var(--cpa-muted);
        }
        .help-text a {
            color: var(--cpa-medium-dark);
            text-decoration: none;
        }
        .help-text a:hover { text-decoration: underline; }

        /* Footer */
        .footer {
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--cpa-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--cpa-muted);
        }

        @media (max-width: 640px) {
            .error-card { padding: 2rem 1.5rem; }
            .error-title { font-size: 20px; }
            .footer { flex-direction: column; gap: 0.5rem; text-align: center; }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <a href="{{ url('/') }}" class="header-logo">
            <div class="header-logo-mark">CPA</div>
            <span class="header-logo-name">{{ config('app.name', 'VJ CPA CRM') }}</span>
        </a>
    </header>

    <main class="main">
        @yield('content')
    </main>

    <footer class="footer">
        <span>© {{ date('Y') }} {{ config('app.name', 'VJ CPA CRM') }}. All rights reserved.</span>
        <span>
            <a href="mailto:support@cpacrm.com" style="color: var(--cpa-medium-dark); text-decoration: none;">
                support@cpacrm.com
            </a>
        </span>
    </footer>
</body>
</html>
