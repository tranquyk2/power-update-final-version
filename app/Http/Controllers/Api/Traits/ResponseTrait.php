<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function respond(bool $success, array $data = [], int $status = 200, $error_code = null, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'error_code' => $error_code,
            'data' => $data
        ], $status);
    }

    public function respondBadRequest(string $message = 'Bad Requests'): JsonResponse
    {
        return $this->respond(false, [], 400, 0, $message);
    }

    public function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respond(false, [], 401, 0, $message);
    }

    public function respondNotFound(string $message = 'Not found'): JsonResponse
    {
        return $this->respond(false, [], 404, 0, $message);
    }

    public function respondForbidden(string $message = 'Internal Server Error'): JsonResponse
    {
        return $this->respond(false, [], 403, 0, $message);
    }

    public function respondInternalServerError(string $message = 'Internal Server Error'): JsonResponse
    {
        return $this->respond(false, [], 500, 0, $message);
    }
}
