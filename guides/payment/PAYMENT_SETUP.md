# Payment Gateway Setup

## Quick Start

### 1. Run Migration

```bash
php artisan migrate
```

This will add `payment_gateway` and `transaction_id` columns to the `orders` table.

### 2. Configure Paymob (Optional)

Add these environment variables to your `.env` file:

```env
PAYMENT_GATEWAY_PAYMOB_ENABLED=true
PAYMOB_API_KEY=your_api_key_here
PAYMOB_INTEGRATION_ID=your_integration_id_here
PAYMOB_IFRAME_ID=your_iframe_id_here
PAYMOB_HMAC_SECRET=your_hmac_secret_here
PAYMOB_CURRENCY=EGP
```

### 3. Configure Paymob Webhook

In your Paymob dashboard, set the webhook URL to:
```
https://yourdomain.com/payment/callback/paymob
```

### 4. Test the Integration

1. Add items to cart
2. Go to checkout
3. Select payment gateway (COD or Paymob)
4. Place order
5. For Paymob, you'll be redirected to Paymob payment page
6. Complete payment
7. Webhook will update order status automatically

## Default Gateways

- **COD (Cash on Delivery)**: Always enabled, no configuration needed
- **Paymob**: Requires configuration (see above)

## Adding New Gateways

See `guides/PAYMENT_GATEWAYS_GUIDE.md` for detailed instructions.

## Troubleshooting

### Gateway Not Showing
- Check that gateway is enabled in `config/payment_gateways.php`
- Verify environment variables are set
- Check `isEnabled()` method returns `true`

### Payment Processing Fails
- Check application logs: `storage/logs/laravel.log`
- Verify API credentials
- Test gateway in sandbox/test mode first

### Webhook Not Working
- Ensure webhook URL is accessible (no authentication required)
- Verify webhook signature verification
- Check logs for webhook errors

