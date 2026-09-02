<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Standardised JSON response wrapper for all API endpoints.
 *
 * Success:     { success: true,  message: "...", data: {...}, meta: {...} }
 * Validation:  { success: false, message: "...", errors: {...} }
 * Error:       { success: false, message: "...", code: "...", errors: {} }
 */
final class ApiResponse
{
    /**
     * 200/201 success response.
     *
     * @param  mixed  $data
     */
    public static function success(
        mixed $data = null,
        string $message = 'Operation completed successfully.',
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        // If data is a paginator, extract pagination meta automatically
        if ($data instanceof AbstractPaginator) {
            $payload['data'] = $data->items();
            $payload['meta'] = array_merge([
                'current_page' => $data->currentPage(),
                'last_page'    => method_exists($data, 'lastPage') ? $data->lastPage() : null,
                'per_page'     => $data->perPage(),
                'total'        => method_exists($data, 'total') ? $data->total() : null,
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ], $meta);
        } else {
            $payload['data'] = $data;
            if (! empty($meta)) {
                $payload['meta'] = $meta;
            }
        }

        return response()->json($payload, $statusCode);
    }

    /**
     * 201 created response.
     *
     * @param  mixed  $data
     */
    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * 204 no content response (for deletes).
     */
    public static function noContent(string $message = 'Resource deleted successfully.'): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message], 200);
    }

    /**
     * Business logic / application error (non-validation).
     */
    public static function error(
        string $message = 'An error occurred.',
        string $code = 'ERROR',
        int $statusCode = 400,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code'    => $code,
            'errors'  => $errors,
        ], $statusCode);
    }

    /**
     * 403 Forbidden.
     */
    public static function forbidden(string $message = 'You do not have permission to perform this action.'): JsonResponse
    {
        return self::error($message, 'FORBIDDEN', 403);
    }

    /**
     * 404 Not Found.
     */
    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return self::error($message, 'NOT_FOUND', 404);
    }

    /**
     * 423 Locked — used for locked accounting periods, immutable records, etc.
     */
    public static function locked(string $message = 'This resource is locked and cannot be modified.'): JsonResponse
    {
        return self::error($message, 'LOCKED', 423);
    }

    /**
     * 422 Validation error (mirrors Laravel's default validation response shape).
     */
    public static function validationError(array $errors, string $message = 'Validation failed.'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * 500 Server error (safe — never expose stack traces in production).
     */
    public static function serverError(string $message = 'An unexpected error occurred. Please try again.'): JsonResponse
    {
        return self::error($message, 'SERVER_ERROR', 500);
    }
}
