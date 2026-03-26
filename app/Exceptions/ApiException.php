<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * API Exception Handler
 *
 * Custom exceptions for API responses.
 * Makes error handling consistent and predictable.
 */

class ApiException extends Exception
{
    protected $statusCode = 400;
    protected $errors = null;

    public function __construct($message = 'API Error', $statusCode = 400, $errors = null, $code = 0, Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function toJson(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->message,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($this->errors) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $this->statusCode);
    }
}

class ValidationException extends ApiException
{
    public function __construct($message = 'Validation failed', $errors = null)
    {
        parent::__construct($message, 422, $errors);
    }
}

class UnauthorizedException extends ApiException
{
    public function __construct($message = 'Unauthorized')
    {
        parent::__construct($message, 401);
    }
}

class ForbiddenException extends ApiException
{
    public function __construct($message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}

class ResourceNotFoundException extends ApiException
{
    public function __construct($resource = 'Resource', $id = null)
    {
        $message = $id
            ? "{$resource} with ID {$id} not found"
            : "{$resource} not found";
        parent::__construct($message, 404);
    }
}

class RateLimitException extends ApiException
{
    public function __construct($message = 'Too many requests. Please try again later.')
    {
        parent::__construct($message, 429);
    }
}

class ServerException extends ApiException
{
    public function __construct($message = 'Internal server error')
    {
        parent::__construct($message, 500);
    }
}
