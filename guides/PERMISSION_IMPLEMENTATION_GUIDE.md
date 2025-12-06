# Permission Implementation Guide

This guide shows how to apply permission checks throughout the application.

## Blade Directives Available

### @hasPermission('permission name')
Checks if the authenticated user has a specific permission.

```blade
@hasPermission('view users')
    <a href="{{ route('admin.users.index') }}">Users</a>
@endhasPermission
```

### @hasRole('role name')
Checks if the authenticated user has a specific role.

```blade
@hasRole('admin')
    <div>Admin Content</div>
@endhasRole
```

### @hasAnyRole('role1', 'role2')
Checks if the user has any of the specified roles.

```blade
@hasAnyRole('admin', 'moderator')
    <div>Admin or Moderator Content</div>
@endhasAnyRole
```

## Sidebar Menu Implementation

Wrap menu items with permission checks:

```blade
@hasPermission('view users')
<li class="side-nav-item">
    <a href="{{ route('admin.users.index') }}" class="side-nav-link">
        <i class="uil-users"></i>
        <span>Users</span>
    </a>
</li>
@endhasPermission
```

For collapsible menus, check permission on the parent:

```blade
@hasPermission('view users')
<li class="side-nav-item">
    <a data-bs-toggle="collapse" href="#sidebarUsers" class="side-nav-link">
        <i class="uil-users"></i>
        <span>Users</span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarUsers">
        <ul class="side-nav-second-level">
            <li><a href="{{ route('admin.users.index') }}">All Users</a></li>
            @hasPermission('create user')
            <li><a href="{{ route('admin.users.create') }}">Create User</a></li>
            @endhasPermission
        </ul>
    </div>
</li>
@endhasPermission
```

## Button Permission Checks

### Create Button
```blade
@hasPermission('create user')
<a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <i class="uil-plus"></i> Create User
</a>
@endhasPermission
```

### Edit Button
```blade
@hasPermission('update user')
<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
    <i class="uil-edit"></i> Edit
</a>
@endhasPermission
```

### Delete Button
```blade
@hasPermission('delete user')
<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
    <i class="uil-trash"></i> Delete
</button>
@endhasPermission
```

### In DataTables Actions Column
```blade
@hasPermission('update user')
<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
    <i class="uil-edit"></i>
</a>
@endhasPermission

@hasPermission('delete user')
<button type="button" class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
    <i class="uil-trash"></i>
</button>
@endhasPermission
```

## Controller Implementation

### Using Middleware in Routes
```php
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:view users')
    ->name('users.index');

Route::post('/users', [UserController::class, 'store'])
    ->middleware('permission:create user')
    ->name('users.store');
```

### Using authorize() in Controllers
```php
public function index()
{
    $this->authorize('viewAny', User::class);
    // ... rest of method
}

public function store(Request $request)
{
    $this->authorize('create', User::class);
    // ... rest of method
}

public function update(Request $request, User $user)
{
    $this->authorize('update', $user);
    // ... rest of method
}
```

## Permission Naming Convention

Format: `{action} {resource}`

- Actions: `view`, `create`, `update`, `delete`, `restore`, `force-delete`, `approve`, `reject`, `toggle-status`
- Resources: Use singular form (`user`, `role`, `patient`, `appointment`)

Examples:
- `view users`
- `create user`
- `update user`
- `delete user`
- `approve doctor profile`

## Guard Detection

The system automatically detects the guard based on the route prefix:
- `/admin/*` → `admin` guard
- `/clinic/*` → `clinic` guard
- `/supplier/*` → `supplier` guard
- `/user/*` → `patient` guard

## Team-Based Permissions

For clinic and supplier guards, permissions are automatically scoped by team_id:
- Clinic users can only see permissions for their clinic (team_id = clinic_id)
- Supplier users can only see permissions for their supplier (team_id = supplier_id)

## Complete Example: Users Index View

```blade
@extends('backend.dashboards.admin.layouts.app')

@section('title', 'Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Users</h4>
                @hasPermission('create user')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="uil-plus"></i> Create User
                </a>
                @endhasPermission
            </div>
            <div class="card-body">
                <table id="users-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @hasPermission('update user')
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    <i class="uil-edit"></i> Edit
                                </a>
                                @endhasPermission
                                
                                @hasPermission('delete user')
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
                                    <i class="uil-trash"></i> Delete
                                </button>
                                @endhasPermission
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Testing Permissions

1. Assign roles to test users
2. Test with different permission levels
3. Verify menu items hide/show correctly
4. Verify buttons hide/show correctly
5. Test route access with middleware
6. Verify team isolation (clinic A can't see clinic B's data)











