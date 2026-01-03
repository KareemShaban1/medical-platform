# Rental Space Company Feature - Implementation Complete ✅

## Overview

This feature allows the admin to flag certain clinics as "Rental Space Companies" (Real Estate Companies). These flagged clinics:

1. Will NOT be shown on the frontend clinics page
2. Will have a restricted role that only allows access to the Rental Spaces module

## Implementation Summary

### ✅ Phase 1: Database Migration

-   Added `is_rental_space_company` boolean column to `clinics` table (default: false)
-   File: `database/migrations/2026_01_03_124345_add_is_rental_space_company_to_clinics_table.php`

### ✅ Phase 2: Model Updates

-   Updated `Clinic` model to include the new field in `$fillable` and `$casts`
-   Added `scopeNotRentalSpaceCompany($query)` to filter out rental space companies
-   Added `scopeRentalSpaceCompany($query)` to get only rental space companies
-   File: `app/Models/Clinic.php`

### ✅ Phase 3: Role/Permission Configuration

-   Added `rental-space-manager` role in the ClinicRolePermissionSeeder
-   This role has permissions only for:
    -   Dashboard (view dashboard, view clinic info, update clinic info)
    -   Rental Spaces (all rental space permissions)
    -   Subscriptions (view only)
    -   Notifications (basic notification permissions)
-   File: `database/seeders/Guards/ClinicRolePermissionSeeder.php`

### ✅ Phase 4: Admin Dashboard Updates

-   Added toggle checkbox in admin clinic list to enable/disable the rental space company flag
-   Added `toggleRentalSpaceCompany` method to admin ClinicController
-   Added route `admin.clinics.toggle-rental-space-company`
-   Added JavaScript handler with confirmation dialog
-   Files:
    -   `app/Http/Controllers/Backend/Dashboards/Admin/ClinicController.php`
    -   `app/Repository/Admin/ClinicRepository.php`
    -   `routes/admin.php`
    -   `resources/views/backend/dashboards/admin/pages/clinics/index.blade.php`
    -   `resources/views/backend/dashboards/admin/pages/clinics/scripts.blade.php`

### ✅ Phase 5: Frontend Updates

-   Updated all frontend clinic queries to exclude rental space companies
-   Updated `Frontend\ClinicController` - index, filter, show, related clinics, nearby clinics
-   Updated `Frontend\HomeController` - home page clinics listing
-   Files:
    -   `app/Http/Controllers/Frontend/ClinicController.php`
    -   `app/Http/Controllers/Frontend/HomeController.php`

### ✅ Phase 6: Role Assignment Logic

-   When admin enables the flag, all clinic users are synced to `rental-space-manager` role
-   When admin disables the flag, all clinic users are restored to `clinic-admin` role
-   Implemented in `toggleRentalSpaceCompany` method

### ✅ Phase 7: Sidebar Visibility

-   The sidebar already uses `@hasPermission` directives
-   Users with `rental-space-manager` role will automatically only see:
    -   Dashboard
    -   Clinic Info
    -   Rental Spaces (under Clinic Management)
    -   My Subscription
    -   Notifications
-   No changes needed - permission-based filtering handles this automatically

### ✅ Phase 8: Translations

-   Added Arabic translations for all new strings
-   File: `resources/lang/ar.json`

## How to Use

1. **In Admin Dashboard:**

    - Go to Clinics list
    - Find the "Real Estate" column with a toggle switch
    - Toggle it ON to mark a clinic as a rental space company
    - A confirmation dialog will appear explaining the effects

2. **Effects when enabled:**

    - The clinic will be hidden from the public clinics page
    - All clinic users will be assigned the `rental-space-manager` role
    - Users will only see Dashboard, Clinic Info, Rental Spaces, Subscriptions, and Notifications in their sidebar

3. **Effects when disabled:**
    - The clinic will appear on the public clinics page again
    - All clinic users will be restored to the `clinic-admin` role with full access

## Files Modified/Created

### Created:

-   `database/migrations/2026_01_03_124345_add_is_rental_space_company_to_clinics_table.php`

### Modified:

-   `app/Models/Clinic.php`
-   `app/Http/Controllers/Backend/Dashboards/Admin/ClinicController.php`
-   `app/Repository/Admin/ClinicRepository.php`
-   `app/Http/Controllers/Frontend/ClinicController.php`
-   `app/Http/Controllers/Frontend/HomeController.php`
-   `database/seeders/Guards/ClinicRolePermissionSeeder.php`
-   `routes/admin.php`
-   `resources/views/backend/dashboards/admin/pages/clinics/index.blade.php`
-   `resources/views/backend/dashboards/admin/pages/clinics/scripts.blade.php`
-   `resources/lang/ar.json`
