# Quick Reference: Password & Status Management

## 🔑 How to Change a User's Password

### From Doctor Profile Details Page:

1. Navigate to **Users Management** → **Doctor Profiles**
2. Click **"Show"** on any doctor profile
3. Look for the **"Authentication Account"** card (green header)
4. Click the **"Change Password"** button in the card header
5. In the modal:
   - Enter new password (minimum 8 characters)
   - Confirm the password
   - Click **"Change Password"**
6. Success message will appear and modal will close automatically

### Technical Details:
- **Endpoint:** `POST /admin/users-management/change-password`
- **Parameters:**
  - `user_id`: ID of the clinic user or supplier user
  - `user_type`: Either `clinic_user` or `supplier_user`
  - `new_password`: The new password (min 8 chars)
  - `confirm_password`: Password confirmation (must match)
- **Response:** JSON with success/error message

---

## 🔄 How to Enable/Disable User Accounts

### From Doctor Profile Details Page:

1. Navigate to **Users Management** → **Doctor Profiles**
2. Click **"Show"** on any doctor profile
3. Look for the **"Authentication Account"** card
4. Find the **"Account Status"** row
5. Click the **toggle button** (Activate/Deactivate)
6. Confirm the action in the dialog
7. Status badge and button will update in real-time

### Visual Indicators:
- **Active Account:** Green badge + "Deactivate" button
- **Inactive Account:** Gray badge + "Activate" button

### Technical Details:
- **Endpoint:** `POST /admin/users-management/toggle-status`
- **Parameters:**
  - `user_id`: ID of the user
  - `user_type`: Either `clinic_user` or `supplier_user`
  - `status`: Boolean (1 for active, 0 for inactive)
- **Response:** JSON with success/error message

---

## 🎯 Where These Features Are Available

### Currently Implemented:
✅ **Doctor Profile Details** (`doctor-profile-details.blade.php`)
- Password change for associated clinic users
- Account status toggle for clinic users

### Can Be Extended To:
- Clinic User Details page
- Supplier User Details page
- Any page displaying user authentication accounts

---

## 🔧 How to Add to Other Pages

### Step 1: Add Change Password Button
```html
<button type="button" class="btn btn-sm btn-light" 
        data-toggle="modal" 
        data-target="#changePasswordModal">
    <i class="fas fa-lock"></i> {{ __('Change Password') }}
</button>
```

### Step 2: Add Status Toggle Button
```html
<button type="button" class="btn btn-sm btn-outline-primary toggle-account-status" 
        data-user-id="{{ $user->id }}" 
        data-user-type="clinic_user"
        data-current-status="{{ $user->is_active ? 1 : 0 }}">
    <i class="fas fa-toggle-on"></i> 
    {{ $user->is_active ? __('Deactivate') : __('Activate') }}
</button>
```

### Step 3: Include Modal and JavaScript
Copy the entire password change modal and script section from `doctor-profile-details.blade.php`

---

## ⚠️ Important Notes

### Password Requirements:
- Minimum 8 characters
- Must match confirmation
- No special character requirements (can be added)
- Stored as hashed value using `Hash::make()`

### Security Features:
- CSRF protection on all requests
- Server-side validation
- User type verification
- User existence check before operations

### User Experience:
- No page reload required
- Real-time feedback
- Success/error messages
- Auto-close on success
- Confirmation dialog for status changes

---

## 🐛 Troubleshooting

### Password Change Not Working:
1. Check browser console for JavaScript errors
2. Verify CSRF token is present: `{{ csrf_token() }}`
3. Check network tab for 500/422 errors
4. Verify user_id and user_type are correct
5. Check Laravel logs: `storage/logs/laravel.log`

### Status Toggle Not Working:
1. Verify jQuery is loaded
2. Check if toastr library is included
3. Confirm route exists: `php artisan route:list | grep toggle-status`
4. Verify user has `is_active` column
5. Check database connection

### Common Errors:
- **"User not found"**: Invalid user_id or user_type
- **"Passwords do not match"**: Client-side validation failed
- **"Token mismatch"**: CSRF token expired, refresh page
- **"Unauthorized"**: Not logged in as admin

---

## 📊 Database Changes

### Tables Affected:

**clinic_users:**
- `password` column updated on password change
- `is_active` column toggled for status

**supplier_users:**
- `password` column updated on password change
- `is_active` column toggled for status

### No Migration Required:
All columns already exist in the database schema.

---

## 🎨 Customization Options

### Change Modal Colors:
Edit the modal header class:
```html
<!-- Current: Green -->
<div class="modal-header bg-success text-white">

<!-- Alternative: Blue -->
<div class="modal-header bg-primary text-white">
```

### Change Button Styles:
```html
<!-- Outlined button -->
<button class="btn btn-outline-success">

<!-- Solid button -->
<button class="btn btn-success">

<!-- With gradient -->
<button class="btn btn-gradient-success">
```

### Add Password Strength Meter:
Include a library like `zxcvbn` or create custom validation.

---

## 📱 Mobile Responsiveness

Both features are fully responsive:
- Modal works on all screen sizes
- Buttons scale appropriately
- Touch-friendly interface
- No horizontal scrolling required

---

## 🔐 Best Practices

### When Changing Passwords:
1. Always log the action
2. Notify user via email
3. Force logout if user is currently logged in
4. Track password history (optional)

### When Toggling Status:
1. Log who made the change
2. Consider adding a reason field
3. Notify user via email
4. Check for active sessions

### Security Recommendations:
1. Add password strength requirements
2. Implement rate limiting
3. Add audit trail
4. Enable 2FA for admins
5. Set password expiry policies

---

## ✨ Tips & Tricks

### Quick Keyboard Shortcuts:
- `Escape`: Close modal
- `Enter`: Submit form (when focused)
- `Tab`: Navigate between fields

### jQuery Selectors Used:
- `.toggle-account-status`: Status toggle buttons
- `#changePasswordModal`: Password modal
- `#changePasswordForm`: Password form
- `#togglePassword`: Visibility toggle

### AJAX Endpoints:
```javascript
// Change password
$.post('{{ route("admin.users-management.change-password") }}', data);

// Toggle status  
$.post('{{ route("admin.users-management.toggle-status") }}', data);
```

---

**Last Updated:** 2024
**Feature Version:** 1.0.0
