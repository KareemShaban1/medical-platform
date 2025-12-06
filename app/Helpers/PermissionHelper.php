<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if the authenticated user has a permission
     * Can be used in Blade templates with @if(hasPermission('permission'))
     *
     * @param string $permission
     * @return bool
     */
    function hasPermission(string $permission): bool
    {
        // Detect guard
        $guard = detectGuard();

        if (!$guard || !\Illuminate\Support\Facades\Auth::guard($guard)->check()) {
            return false;
        }

        $user = \Illuminate\Support\Facades\Auth::guard($guard)->user();

        // Set team_id for team-based permissions
        if ($guard === 'clinic') {
            $clinicId = $user->clinic_id ?? null;
            if ($clinicId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($clinicId);
            } else {
                return false;
            }
        } elseif ($guard === 'supplier') {
            $supplierId = $user->supplier_id ?? null;
            if ($supplierId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($supplierId);
            } else {
                return false;
            }
        } elseif ($guard === 'admin') {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(\App\Models\Admin::TeamId);
            }
        }

        return $user->can($permission);
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Check if the authenticated user has any of the given permissions
     *
     * @param string ...$permissions
     * @return bool
     */
    function hasAnyPermission(string ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if (hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('hasAllPermissions')) {
    /**
     * Check if the authenticated user has all of the given permissions
     *
     * @param string ...$permissions
     * @return bool
     */
    function hasAllPermissions(string ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if the authenticated user has a role
     * Can be used in Blade templates with @if(hasRole('role'))
     *
     * @param string $role
     * @return bool
     */
    function hasRole(string $role): bool
    {
        $guard = detectGuard();

        if (!$guard || !\Illuminate\Support\Facades\Auth::guard($guard)->check()) {
            return false;
        }

        $user = \Illuminate\Support\Facades\Auth::guard($guard)->user();

        // Set team_id for team-based permissions
        if ($guard === 'clinic') {
            $clinicId = $user->clinic_id ?? null;
            if ($clinicId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($clinicId);
            } else {
                return false;
            }
        } elseif ($guard === 'supplier') {
            $supplierId = $user->supplier_id ?? null;
            if ($supplierId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($supplierId);
            } else {
                return false;
            }
        } elseif ($guard === 'admin') {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(\App\Models\Admin::TeamId);
            }
        }

        return $user->hasRole($role);
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Check if the authenticated user has any of the given roles
     *
     * @param string ...$roles
     * @return bool
     */
    function hasAnyRole(string ...$roles): bool
    {
        $guard = detectGuard();

        if (!$guard || !\Illuminate\Support\Facades\Auth::guard($guard)->check()) {
            return false;
        }

        $user = \Illuminate\Support\Facades\Auth::guard($guard)->user();

        // Set team_id for team-based permissions
        if ($guard === 'clinic') {
            $clinicId = $user->clinic_id ?? null;
            if ($clinicId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($clinicId);
            } else {
                return false;
            }
        } elseif ($guard === 'supplier') {
            $supplierId = $user->supplier_id ?? null;
            if ($supplierId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($supplierId);
            } else {
                return false;
            }
        } elseif ($guard === 'admin') {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(\App\Models\Admin::TeamId);
            }
        }

        return $user->hasAnyRole($roles);
    }
}

if (!function_exists('hasAllRoles')) {
    /**
     * Check if the authenticated user has all of the given roles
     *
     * @param string ...$roles
     * @return bool
     */
    function hasAllRoles(string ...$roles): bool
    {
        $guard = detectGuard();

        if (!$guard || !\Illuminate\Support\Facades\Auth::guard($guard)->check()) {
            return false;
        }

        $user = \Illuminate\Support\Facades\Auth::guard($guard)->user();

        // Set team_id for team-based permissions
        if ($guard === 'clinic') {
            $clinicId = $user->clinic_id ?? null;
            if ($clinicId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($clinicId);
            } else {
                return false;
            }
        } elseif ($guard === 'supplier') {
            $supplierId = $user->supplier_id ?? null;
            if ($supplierId && function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($supplierId);
            } else {
                return false;
            }
        } elseif ($guard === 'admin') {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(\App\Models\Admin::TeamId);
            }
        }

        return $user->hasAllRoles($roles);
    }
}

if (!function_exists('detectGuard')) {
    /**
     * Detect the guard based on current request
     *
     * @return string|null
     */
    function detectGuard(): ?string
    {
        $path = request()->path();
        $prefix = request()->route()?->getPrefix() ?? '';

        if (str_contains($prefix, '/admin') || str_contains($path, '/admin')) {
            return 'admin';
        } elseif (str_contains($prefix, '/clinic') || str_contains($path, '/clinic')) {
            return 'clinic';
        } elseif (str_contains($prefix, '/supplier') || str_contains($path, '/supplier')) {
            return 'supplier';
        } elseif (str_contains($prefix, '/user') || str_contains($path, '/user')) {
            return 'patient';
        }

        // Try to detect from authenticated guards
        if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
            return 'admin';
        } elseif (\Illuminate\Support\Facades\Auth::guard('clinic')->check()) {
            return 'clinic';
        } elseif (\Illuminate\Support\Facades\Auth::guard('supplier')->check()) {
            return 'supplier';
        } elseif (\Illuminate\Support\Facades\Auth::guard('patient')->check()) {
            return 'patient';
        }

        return null;
    }
}

