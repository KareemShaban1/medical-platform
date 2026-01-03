# Supplier Payout Feature - Implementation Plan

## Overview

Create a payout system for suppliers similar to the affiliate payout system. Suppliers can request payouts for their orders, and admins can review, approve, and mark payouts as paid with proof images.

## Features

1. **Supplier Payout Settings** (stored in config)

    - Minimum payout amount: Configurable via ENV (default: 100)
    - Payout cooldown period: Configurable via ENV (default: 2 weeks)
    - Payment processing time note: 2-5 business days

2. **Supplier Payout Profile**

    - Stores payment method preferences (bank, wallet, etc.)
    - Payment details (account number, etc.)

3. **Supplier Payout Request**

    - Linked to specific orders (many-to-many)
    - Amount calculated from eligible orders
    - Status: pending, approved, paid, rejected
    - Admin can add notes and proof images
    - Cooldown enforcement (one request per X weeks)

4. **Admin Features**

    - View all payout requests
    - Review linked orders
    - Mark as paid with proof images
    - Reject with reason

5. **Notifications**
    - Notify admin when payout requested
    - Notify supplier when payout status changes

## Database Tables

### 1. supplier_payout_profiles

-   id
-   supplier_id (foreign key)
-   payout_method (string)
-   payout_details (text)
-   notes (text, nullable)
-   timestamps

### 2. supplier_payout_requests

-   id
-   supplier_id (foreign key)
-   amount (decimal)
-   payout_method (string)
-   payout_details (text)
-   supplier_note (text, nullable)
-   admin_note (text, nullable)
-   status (enum: pending, approved, paid, rejected)
-   paid_by_admin_id (foreign key, nullable)
-   paid_at (timestamp, nullable)
-   timestamps

### 3. supplier_payout_request_orders (pivot)

-   id
-   supplier_payout_request_id
-   order_supplier_id
-   amount (decimal) - portion from this order
-   timestamps

## ENV Variables

```
SUPPLIER_MIN_PAYOUT_AMOUNT=100
SUPPLIER_PAYOUT_COOLDOWN_WEEKS=2
```

## Files to Create

### Models

-   app/Models/SupplierPayoutProfile.php
-   app/Models/SupplierPayoutRequest.php

### Migrations

-   create_supplier_payout_profiles_table
-   create_supplier_payout_requests_table

### Controllers

-   app/Http/Controllers/Backend/Dashboards/Supplier/PayoutController.php
-   app/Http/Controllers/Backend/Dashboards/Admin/SupplierPayoutController.php

### Views

-   Supplier Dashboard: payouts/index.blade.php
-   Admin Dashboard: supplier-payouts/index.blade.php, show.blade.php

### Routes

-   supplier.php: payout routes
-   admin.php: supplier payout routes

### Notifications

-   app/Notifications/Admin/SupplierPayoutRequestedNotification.php
-   app/Notifications/Supplier/PayoutStatusChangedNotification.php
