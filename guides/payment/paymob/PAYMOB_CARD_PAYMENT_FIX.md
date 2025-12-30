# Paymob Card Payment - Current Issue & Resolution Steps

## Current Problem

Card payments are failing with the error:
```
"Unable to generate payment URL. The iframe endpoint has been deprecated by Paymob. 
Please contact Paymob support for the new redirect URL format, or use an alternative payment method."
```

## Root Cause

1. ✅ **Iframe endpoint deprecated**: `/api/acceptance/iframes/{id}` returns "IFrame matching query does not exist"
2. ✅ **Payments/pay endpoint requires card details**: `/api/acceptance/payments/pay` requires POST with card details upfront (not suitable for hosted payment pages)

## What to Check First

### Step 1: Check Laravel Logs

Check `storage/logs/laravel.log` for detailed error information:

```bash
tail -f storage/logs/laravel.log | grep -i paymob
```

Look for:
- **Payment key creation response**: Check if it includes `redirect_url` or `payment_page_url`
- **API error response**: See what Paymob is actually returning
- **Alternative URL formats**: The logs now include potential alternative formats

### Step 2: Check Payment Key Response

The code now logs the full payment key creation response. Check if Paymob returns a redirect URL in the payment key response.

## Required Action: Contact Paymob Support

You **MUST** contact Paymob support to get the new redirect URL format. Here's what to ask:

### Questions to Ask Paymob:

1. **"The iframe endpoint `/api/acceptance/iframes/{id}` is completely deprecated and returns 'IFrame matching query does not exist'. What is the new redirect URL format for card payments?"**

2. **"How do we redirect users to the hosted payment page for card payments where users enter their card details?"**

3. **"Does the payment key creation response (`/acceptance/payment_keys`) include a redirect URL or payment page URL?"**

4. **"What is the correct endpoint/URL format to redirect users to the payment page for card payments?"**

### Information to Provide Paymob:

- Your Integration ID: `{YOUR_INTEGRATION_ID}`
- Your API Key: `{YOUR_API_KEY}` (first few characters only for security)
- Error message: "IFrame matching query does not exist"
- Current URL format you're trying: `https://accept.paymob.com/api/acceptance/iframes/{integrationId}?payment_token={token}`

## Possible Solutions from Paymob

Paymob might provide one of these:

### Option 1: New Redirect URL Format
```
https://accept.paymob.com/api/acceptance/payment_page?payment_token={token}&integration_id={id}
```

### Option 2: Payment Key Includes Redirect URL
The payment key creation response might include a `redirect_url` or `payment_page_url` field.

### Option 3: Different Endpoint
```
https://accept.paymob.com/api/acceptance/payments/redirect?payment_token={token}&integration_id={id}
```

### Option 4: Integration-Specific URL
Paymob might provide a URL format specific to your integration.

## Once You Get the Answer from Paymob

### Update the Code

**File**: `app/PaymentGateways/Gateways/PaymobGateway.php`

**Method**: `getCardPaymentRedirectUrl()` (around line 358)

Replace the method with the correct URL format:

```php
private function getCardPaymentRedirectUrl(string $paymentKey, int $integrationId): ?string
{
    // Replace with the URL format provided by Paymob
    return "https://accept.paymob.com/api/acceptance/[NEW_ENDPOINT]?payment_token={$paymentKey}&integration_id={$integrationId}";
}
```

Or if the payment key response includes the redirect URL, update `createPaymentKey()` to return it.

## Temporary Workaround

While waiting for Paymob's response:

1. **Use Wallet Payments**: Wallet payments still work (they use a different endpoint)
2. **Use COD**: Cash on Delivery as an alternative
3. **Disable Card Payments**: Temporarily disable Paymob card payments until you get the new URL format

## Testing After Update

Once you update with the new URL format:

1. **Test in Sandbox**: Use Paymob's test environment first
2. **Test Card Payment**: Create a test order with card payment
3. **Verify Redirect**: Ensure redirect to Paymob payment page works
4. **Test Payment Flow**: Complete a test payment
5. **Verify Callback**: Ensure callback handling works correctly

## Logs to Check

After updating, check these logs to verify it's working:

```bash
# Check for successful redirect URL generation
grep "Paymob card payment redirect URL generated" storage/logs/laravel.log

# Check for any errors
grep "Paymob.*error" storage/logs/laravel.log -i
```

## Summary

- ❌ **Iframe endpoint**: Completely deprecated
- ❌ **Payments/pay endpoint**: Requires card details upfront
- ✅ **Solution**: Contact Paymob support for new redirect URL format
- ✅ **Code ready**: Once you have the format, update `getCardPaymentRedirectUrl()` method
- ✅ **Logging**: Code now logs detailed information to help debug

## Next Steps

1. ✅ Check Laravel logs for detailed error information
2. ✅ Contact Paymob support with the questions above
3. ✅ Update code with the new URL format once received
4. ✅ Test thoroughly before deploying to production






