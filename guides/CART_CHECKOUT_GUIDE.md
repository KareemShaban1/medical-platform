# Cart and Checkout System Guide

## Overview
This guide explains the cart and checkout system implemented for clinic users in the Medical Platform.

## Database Structure

### Tables Created
1. **carts** - Stores cart information for clinic users
2. **cart_items** - Individual items in the cart
3. **checkouts** - Checkout session tracking
4. **orders** - Final orders after checkout
5. **order_items** - Items in completed orders

## Features Implemented

### 1. Cart Dropdown in Navigation
- Shows cart icon with item count badge
- Dropdown displays cart items with images, quantities, and prices
- "View Cart" button to go to full cart page
- Auto-loads cart data for authenticated clinic users

### 2. Cart Page (`/cart`)
- Full cart view with all items
- Quantity controls (increase/decrease)
- Remove item functionality
- Clear cart button
- Order summary with subtotal, shipping, tax, discount, and total
- "Proceed to Checkout" button

### 3. Checkout Page (`/checkout`)
- Review order items
- Payment method selection:
  - Cash on Delivery (COD)
  - Online Payment
- Order summary
- Place order functionality

### 4. Order Success Page
- Order confirmation with order number
- Order details and status
- List of ordered items
- Order summary
- Links to continue shopping or go home

## Authentication & Authorization
- **Guard**: `auth:clinic` - Only authenticated clinic users can access cart/checkout
- **User Context**: Uses `clinic_user_id` and `clinic_id` from authenticated user

## API Endpoints

### Cart Routes
```
GET    /cart                    - View cart page
GET    /cart/data               - Get cart data (AJAX)
POST   /cart/add                - Add product to cart
POST   /cart/update/{itemId}    - Update cart item quantity
DELETE /cart/remove/{itemId}    - Remove item from cart
POST   /cart/clear              - Clear entire cart
```

### Checkout Routes
```
GET    /checkout                      - View checkout page
POST   /checkout/place-order          - Place order
GET    /checkout/success/{order}      - Order success page
```

## Usage Examples

### Adding "Add to Cart" Button to Product Pages

#### Method 1: Using data attributes (auto-binding)
```html
<button 
    data-add-to-cart
    data-product-id="{{ $product->id }}"
    data-supplier-id="{{ $product->supplier_id }}"
    data-quantity="1"
    class="btn btn-primary">
    Add to Cart
</button>
```

#### Method 2: Manual JavaScript call
```html
<button onclick="addToCart({{ $product->id }}, {{ $product->supplier_id }}, 1)" class="btn btn-primary">
    Add to Cart
</button>
```

### AJAX Request Example
```javascript
fetch('/cart/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        product_id: 123,
        supplier_id: 45,
        quantity: 2
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Added to cart:', data.cart_count);
    }
});
```

## Order Flow

1. **Browse Products** → User views products
2. **Add to Cart** → Products added to cart (creates/updates cart and cart_items)
3. **View Cart** → User reviews cart at `/cart`
4. **Proceed to Checkout** → Navigate to `/checkout`
5. **Select Payment Method** → Choose COD or Online
6. **Place Order** → Creates:
   - `checkout` record
   - `order` record with unique order number
   - `order_items` from cart items
   - Links checkout to order
   - Clears cart
7. **Order Success** → Confirmation page with order details

## Models and Relationships

### Cart Model
```php
- belongsTo: ClinicUser, Clinic
- hasMany: CartItem
- Method: calculateTotals() - Recalculates cart totals
- Attribute: total_items - Total quantity of items
```

### CartItem Model
```php
- belongsTo: Cart, Product, Supplier
- Method: calculateTotal() - Calculates item total with tax
```

### Checkout Model
```php
- belongsTo: Cart, ClinicUser, Clinic, Order
```

### Order Model
```php
- belongsTo: ClinicUser, Clinic
- hasMany: OrderItem
```

## Configuration

### Tax Rate
Default: 14% (configured in Cart and CartItem models)
To change, update the tax calculation in:
- `app/Models/Cart.php` line 67
- `app/Models/CartItem.php` line 62

### Shipping
Default: $0 (free shipping)
To implement shipping calculation, modify:
- `app/Models/Cart.php` line 66

### Order Number Format
Format: `ORD-YYYYMMDD-XXXX-RRRR`
- YYYYMMDD: Date
- XXXX: Padded user ID
- RRRR: Random 4-digit number

## Running Migrations

```bash
php artisan migrate
```

This will create the following tables:
- carts
- cart_items
- checkouts
- orders (if not already exists)
- order_items (if not already exists)

## Testing the System

1. **Login as Clinic User**
   ```
   Navigate to /clinic/login
   ```

2. **Add Products to Cart**
   - Go to products page
   - Click "Add to Cart" (if button exists)
   - Or use browser console: `addToCart(productId, supplierId, 1)`

3. **View Cart**
   - Click cart icon in navigation
   - Or navigate to `/cart`

4. **Checkout**
   - Click "Proceed to Checkout"
   - Select payment method
   - Click "Place Order"

5. **View Order**
   - Redirected to success page automatically
   - Order details displayed

## Notes

- Cart is persistent per clinic user
- Cart items are cleared after successful order placement
- Tax is calculated at 14% of subtotal
- Shipping is currently set to $0
- Payment processing for online payments needs to be implemented separately
- The system uses Flowbite for dropdown functionality
- Toast notifications are shown for success/error messages

## Future Enhancements

1. **Payment Gateway Integration** - Implement online payment processing
2. **Shipping Calculator** - Dynamic shipping based on location/weight
3. **Coupon System** - Apply discount coupons
4. **Address Management** - Add shipping/billing addresses
5. **Order Tracking** - Track order status updates
6. **Email Notifications** - Send order confirmation emails
7. **Inventory Management** - Deduct stock on order placement
8. **Order History** - View past orders in user dashboard

## Troubleshooting

### Cart dropdown not showing items
- Check if user is authenticated with `auth:clinic` guard
- Verify route `/cart/data` is accessible
- Check browser console for JavaScript errors

### "Add to Cart" not working
- Ensure CSRF token meta tag exists in head:
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```
- Check if `cart.js` is loaded
- Verify user is authenticated

### Order not creating
- Check database migrations are run
- Verify all required relationships exist (Product, Supplier, etc.)
- Check Laravel logs for errors: `storage/logs/laravel.log`

## Support

For issues or questions, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console for JavaScript errors
3. Network tab for failed API requests
