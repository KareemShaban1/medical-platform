# Fix Role Assignment for Clinic Users

## Issue
The Dashboard link doesn't appear even though the user has the `clinic-admin` role assigned.

## Root Cause
The Blade directive `@hasPermission` wasn't setting the `team_id` before checking permissions for clinic users. Since clinic permissions are team-based, they need the correct `team_id` context.

## Solution Applied
✅ Updated `AppServiceProvider.php` to set `team_id` in all Blade directives before checking permissions/roles.

## How to Fix Existing Users

### Option 1: Re-assign Role (Recommended)
If you just assigned the role, you may need to re-assign it with the correct team context:

```php
// In tinker or a seeder
$user = \App\Models\ClinicUser::find($userId);
setPermissionsTeamId($user->clinic_id);
$user->assignRole('clinic-admin');
```

### Option 2: Clear Cache and Re-check
```php
// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Then refresh the page
```

### Option 3: Verify Role Assignment
Check if the role is correctly assigned:

```php
$user = \App\Models\ClinicUser::find($userId);
setPermissionsTeamId($user->clinic_id);

// Check role
dd($user->hasRole('clinic-admin')); // Should return true

// Check permission
dd($user->can('view dashboard')); // Should return true
```

## Testing

1. Log in as a clinic user
2. The Dashboard link should now appear if the user has `clinic-admin` role
3. Verify other menu items appear based on permissions

## Important Notes

- Always set `team_id` before checking permissions for clinic/supplier users
- The Blade directive now handles this automatically
- If you manually assign roles in code, always call `setPermissionsTeamId()` first











