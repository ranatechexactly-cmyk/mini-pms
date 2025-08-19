<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed $result
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse($result, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $result
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $error,
            'data' => !empty($errorMessages) ? $errorMessages : null
        ];

        return response()->json($response, $code);
    }
}
