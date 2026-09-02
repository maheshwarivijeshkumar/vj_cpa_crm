<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Fields that should never appear in logs.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public function register(): void
    {
        // ── Report ────────────────────────────────────────────────────────────
        $this->reportable(function (Throwable $e): void {
            // Always log non-HTTP exceptions with context
            if (! $e instanceof HttpException) {
                logger()->error($e->getMessage(), [
                    'exception' => get_class($e),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                    'url'       => request()?->fullUrl(),
                    'user_id'   => auth()->id(),
                    'tenant_id' => auth()->user()?->tenant_id,
                ]);
            }
        });

        // ── Render ────────────────────────────────────────────────────────────

        // Model not found → 404
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            $model = class_basename($e->getModel());
            if ($request->expectsJson()) {
                return ApiResponse::notFound("{$model} not found.");
            }
            return $this->errorView($request, 404, "{$model} not found.");
        });

        // CSRF token mismatch → 419
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Your session has expired. Please refresh and try again.', 'CSRF_EXPIRED', 419);
            }
            return $this->errorView($request, 419, 'Your session has expired. Please refresh the page and try again.');
        });

        // Validation → 422
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::validationError($e->errors(), 'The given data was invalid.');
            }
            // Let Laravel handle web validation redirects natively
        });

        // Authentication → 401
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Unauthenticated. Please log in to continue.', 'UNAUTHENTICATED', 401);
            }
            return redirect()->guest(route('login'));
        });

        // Authorization → 403
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::forbidden($e->getMessage() ?: 'You do not have permission to perform this action.');
            }
            return $this->errorView($request, 403, $e->getMessage() ?: 'You do not have permission to access this page.');
        });

        // Not found → 404
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return ApiResponse::notFound('The requested resource was not found.');
            }
            return $this->errorView($request, 404, 'The page you are looking for does not exist.');
        });

        // Generic HTTP exceptions (429, 500, 503, etc.)
        $this->renderable(function (HttpException $e, Request $request) {
            $code    = $e->getStatusCode();
            $message = $e->getMessage() ?: self::defaultMessage($code);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'code'    => "HTTP_{$code}",
                    'errors'  => [],
                ], $code);
            }

            return $this->errorView($request, $code, $message);
        });

        // Catch-all server errors — hide internals in production
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                $message = config('app.debug')
                    ? $e->getMessage()
                    : 'An unexpected error occurred. Please try again later.';

                return ApiResponse::serverError($message);
            }

            // In debug mode, let Whoops render it
            if (config('app.debug')) {
                return null; // pass through to default handler
            }

            return $this->errorView($request, 500, 'An unexpected error occurred. Our team has been notified.');
        });
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function errorView(Request $request, int $code, string $message): Response|RedirectResponse
    {
        // Inertia requests need a full-page response with the error layout
        if ($request->header('X-Inertia')) {
            return response(
                view('app', ['page' => json_encode([
                    'component' => "Errors/{$code}",
                    'props'     => ['status' => $code, 'message' => $message],
                    'url'       => $request->getRequestUri(),
                    'version'   => null,
                ])]),
                $code
            );
        }

        // Regular web request — serve blade error view if it exists
        $viewName = "errors.{$code}";
        if (view()->exists($viewName)) {
            return response(view($viewName, compact('code', 'message')), $code);
        }

        // Fallback generic error view
        return response(view('errors.generic', compact('code', 'message')), $code);
    }

    private static function defaultMessage(int $code): string
    {
        return match ($code) {
            400 => 'Bad request.',
            401 => 'Unauthenticated.',
            403 => 'Forbidden.',
            404 => 'Not found.',
            408 => 'Request timeout.',
            419 => 'Session expired.',
            422 => 'Validation failed.',
            423 => 'Resource is locked.',
            429 => 'Too many requests. Please slow down.',
            500 => 'Internal server error.',
            502 => 'Bad gateway.',
            503 => 'Service temporarily unavailable.',
            default => 'An error occurred.',
        };
    }
}
