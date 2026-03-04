<?php

use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\StudentMiddleWare;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\TeacherMiddleWare;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware){
               //Register The Middleware
        $middleware->alias([
            'admin' => AdminMiddleWare::class,
            'teachers' => TeacherMiddleWare::class,
            'students' => StudentMiddleWare::class,
            'super.admin' => SuperAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
