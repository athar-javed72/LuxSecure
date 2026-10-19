<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureLegalAcceptance;
use App\Http\Middleware\EnsurePanelAccess;
use App\Http\Middleware\EnsureSanctumAbility;
use App\Http\Middleware\EnsureTwoFactorForPanel;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetCountryContext;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustHosts;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateSignature;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrustHosts::class,
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
            SecurityHeaders::class,
        ]);

        $middleware->web(replace: [
            EncryptCookies::class => App\Http\Middleware\EncryptCookies::class,
            PreventRequestForgery::class => VerifyCsrfToken::class,
        ]);

        $middleware->web(append: [
            SetCountryContext::class,
            EnsureLegalAcceptance::class,
        ]);

        $middleware->api(append: [
            EnsureLegalAcceptance::class,
        ]);

        $middleware->throttleApi('api');

        $middleware->preventRequestForgery(except: [
            'webhooks/payments/*',
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'signed' => ValidateSignature::class,
            'panel' => EnsurePanelAccess::class,
            'active' => EnsureUserIsActive::class,
            'panel.2fa' => EnsureTwoFactorForPanel::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'ability' => EnsureSanctumAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->expectsJson() && ! $request->is('api/*')) {
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
    })
    ->withSchedule(function (Schedule $schedule) {
        $tz = config('app.timezone', 'Asia/Karachi');

        $schedule->command('maskanoor:backup')->dailyAt('02:00')->timezone($tz);
        $schedule->command('maskanoor:notify-rent-due')->dailyAt('08:00')->timezone($tz);
        $schedule->command('maskanoor:expire-booking-holds')->everyFiveMinutes()->timezone($tz);
    })->create();
