<?php

use App\Enums\TokenAbility;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_AWS_ELB);
        $middleware->alias([
            // 'abilities' => CheckAbilities::class,
            // 'ability' => CheckForAnyAbility::class,
            'access' => CheckForAnyAbility::class . ':' . TokenAbility::ACCESS_API->value,
            'refresh' => CheckForAnyAbility::class . ':' . TokenAbility::REFRESH_ACCESS_TOKEN->value,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
