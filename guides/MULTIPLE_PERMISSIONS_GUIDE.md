# Multiple Permissions Guide

## Why Helper Functions?

**The helper functions serve a different purpose than Blade directives:**

1. **Blade Directives** (`@hasPermission`, `@hasRole`):
   - Used for wrapping HTML blocks: `@hasPermission('view users')...@endhasPermission`
   - Clean syntax for simple conditionals
   - Cannot be used in `@if` statements directly

2. **Helper Functions** (`hasPermission()`, `hasRole()`):
   - Used in `@if` statements for complex logic: `@if(hasPermission('view users') || hasPermission('view doctor profiles'))`
   - Can be used in PHP code (controllers, repositories)
   - Can be combined with other conditions

## Usage Examples

### Option 1: Using Helper Functions in @if (For Multiple Permissions)

```blade
{{-- Show menu if user has ANY of these permissions --}}
@if(hasPermission('view users') || hasPermission('view doctor profiles'))
<li class="side-nav-item">
    <a href="#">HR Management</a>
</li>
@endif

{{-- Show menu if user has ALL of these permissions --}}
@if(hasPermission('view users') && hasPermission('create user'))
<li class="side-nav-item">
    <a href="#">User Management</a>
</li>
@endif

{{-- Complex conditions --}}
@if(hasPermission('view users') && (hasPermission('create user') || hasRole('admin')))
<li class="side-nav-item">
    <a href="#">Advanced User Management</a>
</li>
@endif
```

### Option 2: Using Blade Directives (For Single Permission)

```blade
{{-- Simple single permission check --}}
@hasPermission('view users')
<li class="side-nav-item">
    <a href="#">Users</a>
</li>
@endhasPermission
```

### Option 3: Using New Multiple Permission Directives

```blade
{{-- Show if user has ANY of these permissions --}}
@hasAnyPermission('view users', 'view doctor profiles')
<li class="side-nav-item">
    <a href="#">HR Management</a>
</li>
@endhasAnyPermission

{{-- Show if user has ALL of these permissions --}}
@hasAllPermissions('view users', 'create user')
<li class="side-nav-item">
    <a href="#">User Management</a>
</li>
@endhasAllPermissions
```

## Recommended Approach for Your Sidebar

For the HR Management section, use **Option 3** (cleanest):

```blade
@hasAnyPermission('view users', 'view doctor profiles')
<!-- HR Management -->
<li class="side-nav-item">
    <a data-bs-toggle="collapse" href="#sidebarUsers" class="side-nav-link">
        <i class="uil-users-alt"></i>
        <span>HR Management</span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarUsers">
        <ul class="side-nav-second-level">
            @hasPermission('view users')
            <li><a href="{{ route('clinic.users.index') }}">Employees</a></li>
            @endhasPermission
            
            @hasPermission('view doctor profiles')
            <li><a href="{{ route('clinic.doctor-profiles.index') }}">Doctor Profiles</a></li>
            @endhasPermission
        </ul>
    </div>
</li>
@endhasAnyPermission
```

## Available Functions & Directives

### Helper Functions (Use in @if statements)
- `hasPermission($permission)` - Check single permission
- `hasAnyPermission(...$permissions)` - Check if has ANY permission
- `hasAllPermissions(...$permissions)` - Check if has ALL permissions
- `hasRole($role)` - Check single role
- `hasAnyRole(...$roles)` - Check if has ANY role
- `hasAllRoles(...$roles)` - Check if has ALL roles

### Blade Directives (Use for wrapping blocks)
- `@hasPermission('permission')` - Check single permission
- `@hasAnyPermission('perm1', 'perm2')` - Check if has ANY permission
- `@hasAllPermissions('perm1', 'perm2')` - Check if has ALL permissions
- `@hasRole('role')` - Check single role
- `@hasAnyRole('role1', 'role2')` - Check if has ANY role
- `@hasAllRoles('role1', 'role2')` - Check if has ALL roles

## Benefits of This Approach

✅ **No Code Duplication**: Blade directives use helper functions
✅ **Flexible**: Can use in @if statements OR as directives
✅ **Reusable**: Helper functions can be used in PHP code too
✅ **Maintainable**: Single source of truth for permission logic










