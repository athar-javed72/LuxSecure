<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'two_factor_code',
        'code',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if (! $this->shouldReturnJson($request, $e) && ! $request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'code' => 'validation_error',
                    'errors' => $e->errors(),
                ], 422);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'code' => 'unauthenticated',
                ], 401);
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $message = $e instanceof HttpExceptionInterface && $e->getMessage() !== ''
                ? $e->getMessage()
                : (config('app.debug') ? $e->getMessage() : 'Server error.');

            return response()->json([
                'success' => false,
                'message' => $message,
                'code' => match ($status) {
                    403 => 'forbidden',
                    404 => 'not_found',
                    429 => 'too_many_requests',
                    default => 'http_error',
                },
            ], $status);
        });
    }
}
