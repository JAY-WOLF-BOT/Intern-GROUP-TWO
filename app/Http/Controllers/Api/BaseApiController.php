<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Base API Controller
 *
 * Provides consistent response formatting for all API endpoints.
 * Ensures uniform JSON structure across the entire API.
 */
class BaseApiController extends Controller
{
    /**
     * Format a successful response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data = null, $message = 'Request successful', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Format an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function errorResponse($message = 'Request failed', $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Format a paginated response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function paginatedResponse($data, $message = 'Data retrieved successfully', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Format a validation error response.
     *
     * @param array $errors
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], 422);
    }

    /**
     * Format an unauthorized response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse($message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ], 401);
    }

    /**
     * Format a forbidden response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse($message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ], 403);
    }

    /**
     * Format a not found response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse($message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
        ], 404);
    }
}
