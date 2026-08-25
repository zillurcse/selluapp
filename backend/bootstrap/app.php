<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Must prepend before HandleCors so vendor domains are patched into the config
        $middleware->prepend(\App\Http\Middleware\DynamicCorsOrigin::class);

        // Treat every API request as JSON so auth/validation failures return
        // JSON (401/422) instead of a 302 redirect the Nuxt proxy turns into 502.
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'package.feature' => \App\Http\Middleware\CheckPackageFeature::class,
            'package.limit' => \App\Http\Middleware\CheckPackageLimit::class,
            'super-admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API routes must always answer with JSON (401/403/422/500), never a
        // browser 302 redirect. A redirect from an /api/* route cannot be
        // relayed by the Nuxt proxy and surfaces to the client as 502 Bad Gateway.
        $exceptions->shouldRenderJsonWhen(function ($request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
