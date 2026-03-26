<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

/**
 * API Helper Functions
 *
 * Utility functions for API operations.
 * Use these to maintain consistency across API responses.
 */
class ApiHelper
{
    /**
     * Format an API response.
     *
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return array
     */
    public static function formatResponse($success = true, $message = '', $data = null, $statusCode = 200): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
            'status_code' => $statusCode,
        ];
    }

    /**
     * Format pagination metadata.
     *
     * @param Paginator $paginator
     * @return array
     */
    public static function formatPagination($paginator): array
    {
        return [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Safe cache retrieval with fallback.
     *
     * @param string $key
     * @param callable $callback
     * @param int $minutes
     * @return mixed
     */
    public static function rememberInCache($key, $callback, $minutes = 60)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Clear cache by pattern.
     *
     * @param string $pattern
     * @return void
     */
    public static function clearCachePattern($pattern): void
    {
        Cache::flush(); // Note: Use a more sophisticated cache tagging system in production
    }

    /**
     * Format API error details for logging.
     *
     * @param \Exception $exception
     * @return array
     */
    public static function formatErrorLog($exception): array
    {
        return [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Validate and sanitize phone number for Ghana.
     *
     * @param string $phone
     * @return string|null
     */
    public static function sanitizeGhanaPhoneNumber($phone): ?string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Check for Ghana number formats
        if (preg_match('/^\+233\d{9}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^0\d{9}$/', $phone)) {
            return '+233' . substr($phone, 1);
        }

        // Invalid phone number
        return null;
    }

    /**
     * Generate a unique transaction ID.
     *
     * @return string
     */
    public static function generateTransactionId(): string
    {
        return uniqid('TXN_', true) . '_' . time();
    }

    /**
     * Generate a unique reference code.
     *
     * @param string $prefix
     * @return string
     */
    public static function generateReferenceCode($prefix = 'REF'): string
    {
        return $prefix . '_' . strtoupper(bin2hex(random_bytes(4))) . '_' . time();
    }

    /**
     * Calculate pagination offset.
     *
     * @param int $page
     * @param int $perPage
     * @return int
     */
    public static function calculateOffset($page = 1, $perPage = 15): int
    {
        return ($page - 1) * $perPage;
    }

    /**
     * Format currency amount (Ghana Cedis).
     *
     * @param float $amount
     * @param bool $includeSymbol
     * @return string
     */
    public static function formatCurrency($amount, $includeSymbol = true): string
    {
        $formatted = number_format($amount, 2, '.', ',');
        return $includeSymbol ? 'GHS ' . $formatted : $formatted;
    }

    /**
     * Parse budget range from query.
     *
     * @param string|null $budgetMin
     * @param string|null $budgetMax
     * @return array
     */
    public static function parseBudgetRange($budgetMin = null, $budgetMax = null): array
    {
        return [
            'min' => is_numeric($budgetMin) ? (float) $budgetMin : 0,
            'max' => is_numeric($budgetMax) ? (float) $budgetMax : PHP_INT_MAX,
        ];
    }

    /**
     * Check if a value is in a valid set.
     *
     * @param mixed $value
     * @param array $validValues
     * @return bool
     */
    public static function isValidValue($value, $validValues): bool
    {
        return in_array($value, $validValues, true);
    }

    /**
     * Format a resource for API output.
     *
     * @param mixed $data
     * @param array|null $only
     * @param array|null $except
     * @return array
     */
    public static function formatResourceForApi($data, $only = null, $except = null): array
    {
        if (method_exists($data, 'toArray')) {
            $array = $data->toArray();
        } else {
            // @phpstan-ignore-next-line
            $array = (array) $data;
        }

        if ($only) {
            return array_only($array, $only);
        }

        if ($except) {
            return array_except($array, $except);
        }

        return $array;
    }

    /**
     * Get HTTP status message.
     *
     * @param int $code
     * @return string
     */
    public static function getHttpStatusMessage($code): string
    {
        return match ($code) {
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
            default => 'Unknown',
        };
    }

    /**
     * Build a filter query string.
     *
     * @param array $filters
     * @return string
     */
    public static function buildFilterQueryString($filters): string
    {
        $query = [];
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        return implode('&', $query);
    }

    /**
     * Validate JSON structure.
     *
     * @param array $data
     * @param array $required
     * @return bool
     */
    public static function hasRequiredFields($data, $required): bool
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Get client IP address.
     *
     * @return string
     */
    public static function getClientIpAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }
        return $ip;
    }

    /**
     * Log API activity.
     *
     * @param string $action
     * @param array $data
     * @return void
     */
    public static function logApiActivity($action, $data = []): void
    {
        \Log::info("API Activity: {$action}", array_merge([
            'ip' => self::getClientIpAddress(),
            'timestamp' => now()->toIso8601String(),
        ], $data));
    }
}
