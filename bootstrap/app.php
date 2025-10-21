<?php

use App\Enums\TokenAbility;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web/index.php',
            __DIR__ . '/../routes/web/panel.php',
            __DIR__ . '/../routes/web/social.php',
            __DIR__ . '/../routes/web/health.php',
            __DIR__ . '/../routes/web/email.php',
        ],
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/api/v1.php',
            __DIR__ . '/../routes/api/v2.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_AWS_ELB);
        $middleware->validateCsrfTokens(except: [
            'chargilypay/webhook',
        ]);
        $middleware->alias([
            // 'abilities' => CheckAbilities::class,
            // 'ability' => CheckForAnyAbility::class,
            'access' => CheckForAnyAbility::class . ':' . TokenAbility::ACCESS_API->value,
            'refresh' => CheckForAnyAbility::class . ':' . TokenAbility::REFRESH_ACCESS_TOKEN->value,
            'email-verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
