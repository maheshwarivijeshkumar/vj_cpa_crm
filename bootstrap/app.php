<?php

use App\Http\Middleware\EnsureTenantContext;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Load .smartfox environment file (replaces .env)
|--------------------------------------------------------------------------
*/
(static function (): void {
    $smartfox = dirname(__DIR__) . '/.smartfox';
    if (! file_exists($smartfox)) {
        return;
    }

    $lines = file($smartfox, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        // Expand ${VAR} references inline
        $value = (string) preg_replace_callback(
            '/\$\{([^}]+)\}/',
            static fn (array $m): string => (string) (getenv($m[1]) ?: $_ENV[$m[1]] ?? $_SERVER[$m[1]] ?? ''),
            $value,
        );

        if (! array_key_exists($key, $_SERVER) && ! array_key_exists($key, $_ENV)) {
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
})();

/*
|--------------------------------------------------------------------------
| Create Application
|--------------------------------------------------------------------------
*/
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'tenant' => EnsureTenantContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handler is auto-bound via App\Exceptions\Handler::register().
        // The Handler class uses Laravel's reportable/renderable API — no manual
        // singleton() call needed here.

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();
