# Paymob Iframe Integration Migration Guide

## Overview

Paymob has deprecated/removed the iframe integration method. This guide explains how to migrate your codebase from the deprecated iframe approach to the new redirect-based payment flow.

## What Changed?

### Previous Implementation (Deprecated)
- **Card Payments**: Used iframe URLs like `https://accept.paymob.com/api/acceptance/iframes/{iframeId}?payment_token={paymentKey}`
- **Wallet Payments**: Already using redirect URLs (no changes needed)

### New Implementation (Required)
- **Card Payments**: Must use redirect URLs similar to wallet payments
- **Wallet Payments**: No changes needed (already correct)

## Current Codebase Analysis

### Files Affected

1. **`app/PaymentGateways/Gateways/PaymobGateway.php`** (Line 144-146)
   - Currently uses iframe URL for card payments
   - Needs to be updated to use redirect URL

2. **`config/payment_gateways.php`** (Line 23)
   - Contains `iframe_id` configuration
   - Can be kept for backward compatibility but may not be needed

3. **Frontend Files** (No changes needed)
   - `resources/views/frontend/pages/checkout/index.blade.php` - Already handles redirect URLs correctly
   - `resources/views/backend/dashboards/clinic/pages/requests/show.blade.php` - Already handles redirect URLs correctly

## Migration Steps

### Step 1: Update PaymobGateway.php

Replace the iframe-based card payment flow with a redirect-based flow similar to wallet payments.

**Current Code (Lines 143-165):**
```php
} else {
    // Card - Use iframe
    $iframeId = $this->getConfigValue('iframe_id', $this->getConfigValue('integration_id'));
    $redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";

    Log::info('Paymob iframe URL generated', [
        'iframe_id' => $iframeId,
        'integration_id' => $integrationId,
        'redirect_url' => $redirectUrl,
        'note' => 'If 3D Secure not appearing, check Paymob dashboard integration settings for 3D Secure configuration',
    ]);

    return PaymentResponse::success(
        message: 'Payment URL generated successfully',
        redirectUrl: $redirectUrl,
        transactionId: (string)$paymobOrder['id'],
        data: [
            'paymob_order_id' => $paymobOrder['id'],
            'payment_key' => $paymentKey,
        ],
        gateway: $this->getName()
    );
}
```

**New Code (Option 1 - Using Paymob's Payment API):**
```php
} else {
    // Card - Use redirect URL (iframe deprecated)
    // Option 1: Use Paymob's payment API to get redirect URL
    $cardResult = $this->initiateCardPayment($paymentKey);
    
    if (!$cardResult || empty($cardResult['redirect_url'])) {
        // Fallback: Use direct redirect URL format
        // Note: This format may vary based on Paymob's current API
        $redirectUrl = "https://accept.paymob.com/api/acceptance/payments/pay?payment_token={$paymentKey}";
    } else {
        $redirectUrl = $cardResult['redirect_url'];
    }

    Log::info('Paymob card payment redirect URL generated', [
        'integration_id' => $integrationId,
        'redirect_url' => $redirectUrl,
        'note' => 'Iframe integration deprecated. Using redirect URL instead.',
    ]);

    return PaymentResponse::success(
        message: 'Payment URL generated successfully',
        redirectUrl: $redirectUrl,
        transactionId: (string)$paymobOrder['id'],
        data: [
            'paymob_order_id' => $paymobOrder['id'],
            'payment_key' => $paymentKey,
        ],
        gateway: $this->getName()
    );
}
```

**New Code (Option 2 - Direct Redirect URL):**
```php
} else {
    // Card - Use redirect URL (iframe deprecated)
    // Direct redirect to Paymob payment page
    $redirectUrl = "https://accept.paymob.com/api/acceptance/payments/pay?payment_token={$paymentKey}";

    Log::info('Paymob card payment redirect URL generated', [
        'integration_id' => $integrationId,
        'redirect_url' => $redirectUrl,
        'note' => 'Iframe integration deprecated. Using redirect URL instead.',
    ]);

    return PaymentResponse::success(
        message: 'Payment URL generated successfully',
        redirectUrl: $redirectUrl,
        transactionId: (string)$paymobOrder['id'],
        data: [
            'paymob_order_id' => $paymobOrder['id'],
            'payment_key' => $paymentKey,
        ],
        gateway: $this->getName()
    );
}
```

### Step 2: Add Card Payment Method (If Using Option 1)

If you choose Option 1, add this method to `PaymobGateway.php`:

```php
/**
 * Initiate card payment and return redirect URL
 * Similar to wallet payment but for card payments
 */
private function initiateCardPayment(string $paymentKey): ?array
{
    try {
        $payload = [
            'source' => [
                'identifier' => 'CARD',
                'subtype' => 'CARD',
                'type' => 'CARD',
            ],
            'payment_token' => $paymentKey,
        ];

        $response = Http::post(self::API_URL . '/acceptance/payments/pay', $payload);

        $json = $response->json();
        // Paymob may return redirection_url or redirect_url
        $redirectUrl = $json['redirection_url']
            ?? $json['redirect_url']
            ?? ($json['data']['redirection_url'] ?? ($json['data']['redirect_url'] ?? null));

        if ($redirectUrl) {
            return ['redirect_url' => $redirectUrl];
        }

        Log::error('Paymob card payment init failed', [
            'status' => $response->status(),
            'response' => $response->body(),
        ]);
        return null;
    } catch (\Exception $e) {
        Log::error('Paymob card payment init error: ' . $e->getMessage());
        return null;
    }
}
```

### Step 3: Update Configuration (Optional)

The `iframe_id` configuration can be kept for backward compatibility but is no longer required. You can update the comment in `config/payment_gateways.php`:

```php
'paymob' => [
    'enabled' => env('PAYMENT_GATEWAY_PAYMOB_ENABLED', true),
    'api_key' => env('PAYMOB_API_KEY'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'), // Card integration ID
    'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'), // Wallet integration ID
    'iframe_id' => env('PAYMOB_IFRAME_ID', env('PAYMOB_INTEGRATION_ID')), // Deprecated - kept for backward compatibility
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
    'currency' => env('PAYMOB_CURRENCY', 'EGP'),
    'return_url' => env('PAYMOB_RETURN_URL', null), // Configure in Paymob dashboard instead
    'require_3d_secure' => env('PAYMOB_REQUIRE_3D_SECURE', true),
    'fee_fixed' => env('PAYMOB_FEE_FIXED', 3),
    'fee_percent' => env('PAYMOB_FEE_PERCENT', 5),
],
```

## Recommended Approach

Based on the existing wallet payment implementation, **Option 2 (Direct Redirect URL)** is recommended because:

1. **Simplicity**: Minimal code changes
2. **Consistency**: Similar to how wallet payments work
3. **Reliability**: Direct URL redirect is more reliable than API calls
4. **Paymob Standard**: This is the standard approach Paymob recommends

The redirect URL format may need to be verified with Paymob's current API documentation. Common formats include:
- `https://accept.paymob.com/api/acceptance/payments/pay?payment_token={paymentKey}`
- `https://accept.paymob.com/api/acceptance/payment_keys?payment_token={paymentKey}`

## Testing Steps

### 1. Test in Sandbox/Test Mode

1. **Update the code** with the new redirect URL approach
2. **Clear config cache**:
   ```bash
   php artisan config:clear
   ```
3. **Test card payment flow**:
   - Create a test order
   - Select Paymob as payment gateway
   - Select card payment method
   - Verify redirect to Paymob payment page
   - Complete test payment
   - Verify callback/webhook handling

### 2. Verify Redirect URL Format

If the direct redirect URL doesn't work, check Paymob's API documentation for the correct format. You may need to:

1. **Check Paymob Dashboard**:
   - Log in to Paymob dashboard
   - Go to Integration settings
   - Check for redirect URL format or payment page URL

2. **Contact Paymob Support**:
   - Ask for the correct redirect URL format for card payments
   - Verify if any additional parameters are needed

### 3. Test Payment Callbacks

Ensure that payment callbacks and webhooks still work correctly:

1. **Test Success Callback**:
   - Complete a successful payment
   - Verify redirect back to your site
   - Check order status is updated

2. **Test Failure Callback**:
   - Cancel or fail a payment
   - Verify redirect back to your site
   - Check error handling

3. **Test Webhook**:
   - Verify webhook is received
   - Check HMAC verification works
   - Verify order status updates

## Paymob Dashboard Configuration

### Update Return URLs

1. **Log in to Paymob Dashboard**
2. **Navigate to**: Settings → Integrations → Your Card Integration
3. **Configure Return URLs**:
   - **Success URL**: `https://yourdomain.com/payment/callback/paymob?success=true`
   - **Failure URL**: `https://yourdomain.com/payment/callback/paymob?success=false`
   - **Cancel URL**: `https://yourdomain.com/payment/callback/paymob?success=false`

### Verify Webhook Configuration

1. **Navigate to**: Settings → Webhooks
2. **Verify Webhook URL**: `https://yourdomain.com/payment/callback/paymob`
3. **Ensure HMAC Secret** matches your `.env` file

## Environment Variables

No changes needed to environment variables. The existing configuration will work:

```env
PAYMENT_GATEWAY_PAYMOB_ENABLED=true
PAYMOB_API_KEY=your_api_key
PAYMOB_INTEGRATION_ID=your_integration_id
PAYMOB_IFRAME_ID=your_iframe_id  # Deprecated but kept for compatibility
PAYMOB_WALLET_INTEGRATION_ID=your_wallet_integration_id
PAYMOB_HMAC_SECRET=your_hmac_secret
PAYMOB_CURRENCY=EGP
PAYMOB_REQUIRE_3D_SECURE=true
```

## Troubleshooting

### Issue: Redirect URL Not Working

**Solution**: Verify the redirect URL format with Paymob's current API documentation. The URL format may have changed.

### Issue: Payment Page Not Loading

**Possible Causes**:
1. Incorrect payment token
2. Expired payment token
3. Invalid integration ID

**Solution**: 
- Check payment token generation
- Verify integration ID in Paymob dashboard
- Check API response logs

### Issue: Callback Not Working

**Solution**:
- Verify return URLs in Paymob dashboard
- Check webhook configuration
- Verify HMAC secret matches
- Check server logs for callback requests

## Additional Resources

- [Paymob API Documentation](https://accept.paymob.com/docs/)
- [Paymob Developer Portal](https://www.paymob.com/en/developers)
- [Paymob Support](https://www.paymob.com/en/contact)

## Migration Checklist

- [ ] Update `PaymobGateway.php` to use redirect URL instead of iframe
- [ ] Test card payment flow in sandbox/test mode
- [ ] Verify redirect URL format with Paymob documentation
- [ ] Test payment callbacks (success and failure)
- [ ] Test webhook handling
- [ ] Update Paymob dashboard return URLs if needed
- [ ] Test in production with small test transaction
- [ ] Monitor logs for any errors
- [ ] Update documentation/comments in code

## Notes

- **Frontend Code**: No changes needed - the existing redirect handling will work with the new approach
- **Wallet Payments**: No changes needed - already using redirect URLs
- **Backward Compatibility**: The `iframe_id` config can be kept but is no longer used
- **3D Secure**: Still works the same way - controlled by Paymob dashboard settings

## Support

If you encounter issues during migration:

1. Check Paymob's official documentation for the latest API changes
2. Contact Paymob support for clarification on redirect URL format
3. Review server logs for detailed error messages
4. Test with Paymob's sandbox/test environment first

