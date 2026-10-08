<?php

use App\Http\Middleware\EnsureCompanyAccess;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'company.access' => EnsureCompanyAccess::class,
            'permission' => EnsurePermission::class,
        ]);
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
        
            if (! $user) {
                return '/login';
            }
        
            return match ($user->role->value) {
                'super_admin' => '/admin/dashboard',
                'manager' => '/app/dashboard',
                'consultant' => '/app/dashboard',
                'patient' => '/patient/dashboard',
                default => '/dashboard',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
