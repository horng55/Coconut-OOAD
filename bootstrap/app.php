<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminHandleInertiaRequests;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckUserSubscription;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\UserHandleInertiaRequests;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            StartSession::class,
            AddQueuedCookiesToResponse::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            CorsMiddleware::class,
        ]);

        $middleware->appendToGroup('auth', [
            Authenticate::class,
        ]);

        $middleware->appendToGroup('admin', [
            AdminMiddleware::class,
            AdminHandleInertiaRequests::class,
        ]);

        $middleware->appendToGroup('user', [
            UserMiddleware::class,
            UserHandleInertiaRequests::class,
            CheckUserSubscription::class,
        ]);

    })->withExceptions(function (Exceptions $exceptions) {
    })->create();

