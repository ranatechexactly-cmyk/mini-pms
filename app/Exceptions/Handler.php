<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Force JSON response for API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->renderApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Render API exception as JSON response
     */
    private function renderApiException(Request $request, Throwable $e): JsonResponse
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        
        if ($statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        $response = [
            'status' => 'error',
            'message' => $this->getExceptionMessage($e, $statusCode),
            'data' => null,
            'meta' => [
                'version' => 'v1',
                'timestamp' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID', uniqid())
            ]
        ];

        // Add debug info in development
        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get appropriate error message based on exception type
     */
    private function getExceptionMessage(Throwable $e, int $statusCode): string
    {
        switch ($statusCode) {
            case 401:
                return 'Unauthorized';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 422:
                return 'Validation Error';
            case 429:
                return 'Too Many Requests';
            case 500:
                return config('app.debug') ? $e->getMessage() : 'Internal Server Error';
            default:
                return $e->getMessage() ?: 'An error occurred';
        }
    }
}
