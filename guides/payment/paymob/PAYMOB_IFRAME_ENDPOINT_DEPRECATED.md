# Paymob Iframe Endpoint Deprecated - Error Fix

## Error Message
```
"IFrame matching query does not exist."
```

## Problem

Paymob has **completely deprecated and removed** the iframe endpoint. Even redirecting to the iframe URL no longer works. The endpoint `/api/acceptance/iframes/{iframeId}` returns an error.

## Current Status

❌ **Not Working**: `https://accept.paymob.com/api/acceptance/iframes/{iframeId}?payment_token={token}`
❌ **Not Working**: `https://accept.paymob.com/api/acceptance/iframes/{integrationId}?payment_token={token}`

## Solution Required

You **MUST** contact Paymob support to get the new redirect URL format for card payments. The iframe endpoint is completely removed.

## Steps to Fix

### Step 1: Contact Paymob Support

1. **Log in to Paymob Dashboard**
2. **Contact Support** and ask:
   - "What is the new redirect URL format for card payments?"
   - "The iframe endpoint is deprecated. What endpoint should we use for hosted payment page redirects?"
   - "How do we redirect users to the payment page for card payments without using the iframe endpoint?"

### Step 2: Check Paymob Dashboard

1. **Navigate to**: Settings → Integrations → Your Card Integration
2. **Look for**:
   - Payment Page URL
   - Redirect URL
   - Hosted Payment Page URL
   - Any new redirect URL format

### Step 3: Check Paymob API Documentation

1. **Visit**: [Paymob API Documentation](https://accept.paymob.com/docs/)
2. **Look for**:
   - Hosted Payment Page (HPP) integration
   - Payment redirect URLs
   - Card payment redirect methods

### Step 4: Update Code

Once you have the new redirect URL format from Paymob, update:

**File**: `app/PaymentGateways/Gateways/PaymobGateway.php`

**Method**: `getCardPaymentRedirectUrl()` (around line 380)

Replace with the new URL format provided by Paymob.

## Possible New URL Formats

Based on common payment gateway patterns, Paymob might use one of these formats:

1. **Payment Page Endpoint**:
   ```
   https://accept.paymob.com/api/acceptance/payment_page/{paymentKey}?integration_id={integrationId}
   ```

2. **Payment Keys Endpoint**:
   ```
   https://accept.paymob.com/api/acceptance/payment_keys/{paymentKey}?integration_id={integrationId}
   ```

3. **Payments Endpoint** (with redirect parameter):
   ```
   https://accept.paymob.com/api/acceptance/payments/redirect?payment_token={paymentKey}&integration_id={integrationId}
   ```

4. **New HPP Endpoint**:
   ```
   https://accept.paymob.com/api/acceptance/hpp/{paymentKey}?integration_id={integrationId}
   ```

**Note**: These are examples. You must get the actual format from Paymob.

## Temporary Workaround

If you need a temporary solution while waiting for Paymob's response:

1. **Use Wallet Payments**: Wallet payments still work (they use a different endpoint)
2. **Use COD**: Cash on Delivery as an alternative
3. **Contact Paymob Immediately**: This is a critical issue that needs Paymob's official guidance

## Code Update Required

Once you have the correct redirect URL format from Paymob, update this method:

```php
private function getCardPaymentRedirectUrl(string $paymentKey, int $integrationId): ?string
{
    // Replace with the new URL format from Paymob
    // Example (update with actual format):
    return "https://accept.paymob.com/api/acceptance/[NEW_ENDPOINT]?payment_token={$paymentKey}&integration_id={$integrationId}";
}
```

## Testing

After updating with the new URL format:

1. **Test in Sandbox**: Use Paymob's test environment first
2. **Test Card Payment**: Create a test order with card payment
3. **Verify Redirect**: Ensure redirect to Paymob payment page works
4. **Test Payment Flow**: Complete a test payment
5. **Verify Callback**: Ensure callback handling works correctly

## Important Notes

- ⚠️ **This is a breaking change** from Paymob - the iframe endpoint is completely removed
- ⚠️ **You must contact Paymob** - there's no workaround without their new URL format
- ⚠️ **Card payments are currently broken** - until you get the new redirect URL format
- ✅ **Wallet payments still work** - they use a different endpoint

## Contact Information

- **Paymob Support**: Contact through your Paymob dashboard
- **Paymob Documentation**: https://accept.paymob.com/docs/
- **Paymob Dashboard**: Check integration settings for new URL formats

## Summary

1. ❌ Iframe endpoint is completely deprecated and removed
2. ✅ Contact Paymob support for new redirect URL format
3. ✅ Update code with new URL format once received
4. ✅ Test thoroughly before deploying to production






