<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $guard = $this->detectGuard();

        if (!$guard || !Auth::guard($guard)->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Unauthorized'),
                ], 401);
            }
            abort(401, 'Unauthorized');
        }

        $user = Auth::guard($guard)->user();

        if (!$user->can($permission)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('You do not have permission to perform this action.'),
                    'permission' => $permission
                ], 403);
            }

            abort(403, __('You do not have permission to perform this action.'));
        }

        return $next($request);
    }

    /**
     * Detect the guard based on route prefix
     */
    protected function detectGuard(): ?string
    {
        $route = request()->route();
        $prefix = $route->getPrefix() ?? '';
        $path = request()->path();

        if (str_contains($prefix, '/admin') || str_contains($path, '/admin')) {
            return 'admin';
        } elseif (str_contains($prefix, '/clinic') || str_contains($path, '/clinic')) {
            return 'clinic';
        } elseif (str_contains($prefix, '/supplier') || str_contains($path, '/supplier')) {
            return 'supplier';
        } elseif (str_contains($prefix, '/user') || str_contains($path, '/user')) {
            return 'patient';
        }

        return null;
    }
}











