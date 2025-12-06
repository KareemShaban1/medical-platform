# Debug Permissions Issue

## Quick Fix Steps

### Step 1: Check Your User's Permissions
Run this command to see what's wrong:
```bash
php artisan permissions:check-user your-email@example.com --guard=clinic
```

This will show you:
- User's roles
- User's permissions
- Whether the permission exists
- Whether the role has the permission

### Step 2: Fix the User's Role Assignment
Run this command to re-assign the role with correct team_id:
```bash
# Fix specific user
php artisan permissions:fix-user-roles --guard=clinic --email=your-email@example.com

# Fix all clinic users
php artisan permissions:fix-user-roles --guard=clinic
```

### Step 3: Verify It Works
After fixing, refresh your browser. The dashboard link should appear.

## Common Issues

### Issue 1: Role Not Found
**Error**: `'clinic-admin' role not found for clinic ID X`

**Solution**: Run the seeder first:
```bash
php artisan db:seed --class=RoleAndPermissionSeeder
```

### Issue 2: Permission Not Assigned to Role
**Error**: User has role but can't access permission

**Solution**: The role might not have the permission. Check in the seeder that `clinic-admin` role has `'view dashboard'` permission.

### Issue 3: Wrong Team ID
**Error**: Role exists but user can't access it

**Solution**: The role was created for a different clinic. Run the fix command to re-assign with correct team_id.

## Manual Fix (Tinker)

If the commands don't work, use tinker:

```php
// In php artisan tinker
$user = \App\Models\ClinicUser::where('email', 'your-email@example.com')->first();
$user->clinic_id; // Check this exists

// Find the role
setPermissionsTeamId($user->clinic_id);
$role = \App\Models\Role::where('name', 'clinic-admin')
    ->where('guard_name', 'clinic')
    ->where('team_id', $user->clinic_id)
    ->first();

// Check if role exists
if (!$role) {
    echo "Role not found! Run seeder first.\n";
} else {
    // Re-assign role
    $user->syncRoles([$role]);
    
    // Verify
    setPermissionsTeamId($user->clinic_id);
    echo "Has role: " . ($user->hasRole('clinic-admin') ? 'YES' : 'NO') . "\n";
    echo "Can view dashboard: " . ($user->can('view dashboard') ? 'YES' : 'NO') . "\n";
}

// Clear cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

## Temporary Workaround

I've added a temporary workaround in the sidebar that checks both:
1. Permission: `view dashboard`
2. Role: `clinic-admin`

So the dashboard link will show if the user has EITHER the permission OR the role. This should make it appear immediately while we fix the underlying issue.

## After Fixing

1. Clear browser cache
2. Refresh the page
3. The dashboard link should appear
4. Run the check command again to verify everything is working










