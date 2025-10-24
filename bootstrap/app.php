<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\CheckSpmbResult;
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
            // Tambahkan alias 'auth' yang hilang
            'auth' => \App\Http\Middleware\Authenticate::class,
            
            // Tambahkan alias middleware Anda yang sudah ada
            'is_super_admin' => \App\Http\Middleware\IsSuperAdmin::class,
            'is_admin_sekolah' => \App\Http\Middleware\IsAdminSekolah::class,
        ]);
        $middleware->web(append: [
            // 🔥 Tambahkan middleware Anda ke grup 'web'
            CheckSpmbResult::class, 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
