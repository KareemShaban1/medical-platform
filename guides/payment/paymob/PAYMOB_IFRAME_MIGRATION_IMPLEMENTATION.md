# Paymob Iframe Migration - Quick Implementation Guide

## Quick Reference: Code Changes Required

This document provides the exact code changes needed to migrate from Paymob's deprecated iframe integration.

## File: `app/PaymentGateways/Gateways/PaymobGateway.php`

### Change 1: Replace Card Payment Flow (Lines 143-165)

**Remove this code:**
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

**Replace with this code:**
```php
} else {
    // Card - Use redirect URL (iframe deprecated by Paymob)
    // Even though iframes are deprecated, the iframe URL still works as a redirect URL
    // The URL format: /api/acceptance/iframes/{iframeId}?payment_token={token}
    // We use integration_id as iframe_id (they're usually the same)
    $iframeId = $this->getConfigValue('iframe_id', $integrationId);
    $redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";

    Log::info('Paymob card payment redirect URL generated', [
        'integration_id' => $integrationId,
        'iframe_id' => $iframeId,
        'redirect_url' => $redirectUrl,
        'note' => 'Iframe embedding deprecated, but URL still works for redirects.',
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

**Important Note:** 
- Paymob deprecated **iframe embedding** (you can't use `<iframe>` tag in HTML)
- However, **redirecting** to the iframe URL still works (using `window.location.href`)
- The URL path still contains "/iframes/" but this is Paymob's Hosted Payment Page (HPP) format for redirects
- The `/acceptance/payments/pay` endpoint requires card details (POST with source), so it's not suitable for hosted payment pages where users enter card details
- **Current Solution**: Use the iframe URL format for redirects (not embedding)
- **Future Update**: If Paymob provides a newer redirect URL format without "iframes" in the path, update the code accordingly

### Change 2: Update Comment (Line 122)

**Change this:**
```php
// Step 4: Depending on method, either use iframe (card) or wallet flow
```

**To this:**
```php
// Step 4: Depending on method, either use redirect URL (card) or wallet flow
```

## No Additional Method Required

The solution uses the existing iframe URL format as a redirect URL. No additional API method is needed.

```php
**Note:** No additional method is needed. The solution uses the existing iframe URL format as a redirect URL.
```

## Testing the Changes

### 1. Test Locally First

```bash
# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Test payment flow
```

### 2. Verify Redirect URL

1. Create a test order
2. Select Paymob card payment
3. Check if redirect URL is generated correctly
4. Verify redirect to Paymob payment page

### 3. Test Payment Flow

1. Complete a test payment
2. Verify callback handling
3. Check order status update
4. Verify webhook (if configured)

## Solution: Use Iframe URL as Redirect

**Important:** 
- Paymob deprecated **iframe embedding** (you can't use `<iframe>` tag in HTML)
- However, the iframe URL itself still works for **redirects** (window.location.href)
- The `/acceptance/payments/pay` endpoint requires card details (POST with source), so it's not suitable for hosted payment pages where users enter their card details

### Solution: Redirect to Iframe URL

**How it works:**
- Use the same iframe URL format: `https://accept.paymob.com/api/acceptance/iframes/{iframeId}?payment_token={token}`
- Instead of embedding it in an `<iframe>` tag, redirect the user to this URL
- Paymob will show the payment page, user enters card details, and gets redirected back

**Pros:**
- Simple solution - minimal code changes
- Uses existing URL format that Paymob still supports
- No additional API calls needed
- Works for hosted payment pages where users enter card details

**Note:** This is different from wallet payments which use the `/acceptance/payments/pay` API endpoint because wallet payments require the phone number upfront.

## Verifying the Correct Redirect URL Format

If Option A doesn't work, you need to verify the correct format:

1. **Check Paymob Dashboard**:
   - Integration settings
   - Payment page URL
   - API documentation

2. **Check Paymob API Response**:
   - Look at the payment key creation response
   - Check if it includes a redirect URL

3. **Contact Paymob Support**:
   - Ask for the correct redirect URL format
   - Verify if any additional parameters are needed

## Troubleshooting

### Error: "Method 'GET' not allowed"

**Cause:** Trying to use `/acceptance/payments/pay` endpoint with GET request

**Solution:** Don't use that endpoint for card payments. Use the iframe URL format as a redirect instead:
```php
$redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";
```

### Error: "source field is required" or "identifier is required"

**Cause:** Trying to use `/acceptance/payments/pay` endpoint which requires card details upfront

**Solution:** This endpoint is for processing payments with card details you already have. For hosted payment pages (where users enter card details), use the iframe URL redirect approach instead.

### Iframe URL Doesn't Work

If the iframe URL redirect doesn't work:

1. **Verify Integration ID**: Check that your `PAYMOB_INTEGRATION_ID` and `PAYMOB_IFRAME_ID` are correct in your `.env` file
2. **Check Paymob Dashboard**: Verify your integration is active and configured correctly
3. **Contact Paymob Support**: Ask for the correct redirect URL format for card payments without iframe embedding
4. **Check Payment Key**: Ensure the payment key is valid and not expired

## Rollback Plan

If the new implementation doesn't work:

1. **Keep the old code commented** for reference
2. **Test in sandbox first** before production
3. **Have a backup payment method** (COD) available
4. **Monitor logs** for errors

## Post-Migration Checklist

- [ ] Code updated in `PaymobGateway.php`
- [ ] Tested in sandbox/test environment
- [ ] Verified redirect URL format
- [ ] Tested payment success flow
- [ ] Tested payment failure flow
- [ ] Verified callback handling
- [ ] Verified webhook handling
- [ ] Updated Paymob dashboard return URLs (if needed)
- [ ] Tested in production with small transaction
- [ ] Monitored logs for errors
- [ ] Updated team documentation

## Need Help?

If you encounter issues:

1. Check `storage/logs/laravel.log` for detailed errors
2. Verify Paymob API credentials
3. Check Paymob dashboard integration settings
4. Review Paymob's official API documentation
5. Contact Paymob support for URL format confirmation

