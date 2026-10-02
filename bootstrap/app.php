<?php

use App\Http\Middleware\IdentifySite;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // sitemap.xml y robots.txt se registran aparte, sin el grupo web:
            // un rastreo no necesita sesion ni CSRF. Ver routes/seo.php, que es
            // quien declara el middleware que necesita.
            Route::group([], __DIR__.'/../routes/seo.php');
        },
    )
    ->withCommands([
        __DIR__.'/../app/Sites/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            IdentifySite::class,
        ]);

        $middleware->alias([
            'site' => IdentifySite::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
