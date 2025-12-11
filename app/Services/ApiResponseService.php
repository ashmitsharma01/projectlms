<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseService
{
    public static function response(int $statusCode, string $message, $data = null, bool $isError = false): JsonResponse
    {
        $response = [
            'status' => $isError ? 'error' : 'success',
            'message' => $message,
            'code' => $statusCode, // Include the status code in the response
        ];

        if ($data) {
            $response[$isError ? 'errors' : 'data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public static function success(string $message, $data = null): JsonResponse
    {
        return self::response(200, $message, $data);
    }

    public static function error(int $statusCode, string $message, $data = null): JsonResponse
    {
        return self::response($statusCode, $message, $data, true);
    }

    public static function validationError($errors): JsonResponse
    {
        return self::error(422, 'Validation Error', $errors);
    }
}
