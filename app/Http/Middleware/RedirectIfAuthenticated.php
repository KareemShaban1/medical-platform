<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if accessing login routes
        $isLoginRoute = $request->is('login') 
            || $request->is('admin/login') 
            || $request->is('clinic/login') 
            || $request->is('supplier/login') 
            || $request->is('affiliate/login');

        if ($isLoginRoute) {
            // Check if user is already logged in on any guard
            if (Auth::guard('admin')->check()) {
                return redirect('/admin/dashboard');
            }
            if (Auth::guard('clinic')->check()) {
                $clinicUser = Auth::guard('clinic')->user();
                if ($clinicUser->has_clinic) {
                    return redirect('/clinic/dashboard');
                } else {
                    return redirect('/');
                }
            }
            if (Auth::guard('supplier')->check()) {
                return redirect('/supplier/dashboard');
            }
            if (Auth::guard('affiliate')->check()) {
                return redirect('/affiliate/dashboard');
            }
            if (Auth::guard('patient')->check()) {
                return redirect('/user');
            }
        }

        return $next($request);
    }
}




