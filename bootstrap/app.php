<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\checkForForecast;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\RedirectIfRegistered;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'=>AdminMiddleware::class,
            'forecast' => checkForForecast::class,
            'permission' => CheckPermission::class,
            'registered'=>RedirectIfRegistered::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
