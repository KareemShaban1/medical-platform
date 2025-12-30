<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'check.clinic.approval'   => \App\Http\Middleware\CheckClinicApproval::class,
            'check.supplier.approval' => \App\Http\Middleware\CheckSupplierApproval::class,
            'check.subscription'      => \App\Http\Middleware\CheckSubscription::class,
            'permission'              => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated redirects based on route prefix
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Redirect to appropriate login page based on route prefix
            if ($request->is('admin/*')) {
                return redirect()->guest('/admin/login');
            } elseif ($request->is('clinic/*')) {
                return redirect()->guest('/clinic/login');
            } elseif ($request->is('supplier/*')) {
                return redirect()->guest('/supplier/login');
            } elseif ($request->is('affiliate/*')) {
                return redirect()->guest('/affiliate/login');
            } elseif ($request->is('user/*') || $request->is('user')) {
                return redirect()->guest('/login');
            }

            // Default redirect to home login
            return redirect()->guest('/login');
        });
    })->create();
