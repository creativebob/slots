<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\SlotUnavailable;
use App\Exceptions\HoldConflict;
use Illuminate\Contracts\Cache\LockTimeoutException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'idempotent' => \Infinitypaul\Idempotency\Middleware\EnsureIdempotency::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (SlotUnavailable $e, $request) {
            return response()->json(['message' => 'The slot is unvailable'], 404);
        });

        $exceptions->render(function (HoldConflict $e, $request) {
            return response()->json(['message' => 'Hold conflict'], 409);
        });

        $exceptions->render(function (LockTimeoutException $e, $request) {
            return response()->json(['message' => 'Resource is busy'], 423);
        });

    })->create();
