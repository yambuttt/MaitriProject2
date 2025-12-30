<?php

use App\Http\Middleware\AdminOnly;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->registered(function (Application $app) {
        // SESUAIKAN path di bawah ini
        $app->usePublicPath(
            realpath(__DIR__ . '/../../public_html')
            // kalau docroot-nya misalnya public_html/maitriproject.me,
            // pakai: realpath(__DIR__ . '/../../public_html/maitriproject.me')
        );
    })
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminOnly::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'paydisini/callback',
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\AffiliateRefMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


//     <?php

// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;
// use App\Http\Middleware\AdminOnly; 
// return Application::configure(basePath: dirname(__DIR__))
//     ->registered(function (Application $app) {
//         // SESUAIKAN path di bawah ini
//         $app->usePublicPath(
//             realpath(__DIR__ . '/../../public_html')
//             // kalau docroot-nya misalnya public_html/maitriproject.me,
//             // pakai: realpath(__DIR__ . '/../../public_html/maitriproject.me')
//         );
//     })
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         api: __DIR__.'/../routes/api.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->alias([
//             'admin' => AdminOnly::class,
//         ]);
//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })
//     ->create();
