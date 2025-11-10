# Paymob Environment Variables Template

Add these lines to your `.env` file (around line 67-71 or at the end):

```env
# Paymob Payment Gateway Configuration
PAYMENT_GATEWAY_PAYMOB_ENABLED=true
PAYMOB_API_KEY=ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SnBjM01pT2lKb2RIUndjem92TDNkM2R5NTNjeTVuYVhSb2RXSXVZMjl0TDJsa1lpSXNJbVZ0WVdsc0lqb2lNVFkzTWpFME1pNHdJbjAuLlJhMWZzUWlEWmY2S0c3Y0J2Y3J3S2J3WU5Fb3B3Tl9rY3V6clR5V2t0Tm9sY3ZRc0F3Y3J3S2J3WU5Fb3B3Tl9rY3V6clR5V2t0Tm9sY3ZRc0F3
PAYMOB_INTEGRATION_ID=1234567
PAYMOB_IFRAME_ID=1234567
PAYMOB_HMAC_SECRET=ABCD1234EFGH5678IJKL9012MNOP3456QRST7890UVWX1234YZAB5678CDEF
PAYMOB_CURRENCY=EGP
PAYMOB_REQUIRE_3D_SECURE=true
```

## Where to Get Each Value:

1. **PAYMOB_API_KEY**: 
   - Paymob Dashboard → Settings → API Keys
   - This is your authentication token

2. **PAYMOB_INTEGRATION_ID**: 
   - Paymob Dashboard → Settings → Integrations
   - Select your payment integration (Card, Wallet, etc.)
   - Copy the Integration ID

3. **PAYMOB_IFRAME_ID**: 
   - Usually same as Integration ID
   - Check integration settings if different

4. **PAYMOB_HMAC_SECRET**: 
   - Paymob Dashboard → Settings → Webhooks
   - Look for "HMAC Secret" or "Webhook Secret"
   - Used for webhook verification

5. **PAYMOB_CURRENCY**: 
   - Set to your currency code (EGP, USD, EUR, etc.)
   - Default: EGP

6. **PAYMOB_REQUIRE_3D_SECURE**: 
   - Set to `true` to require 3D Secure authentication (recommended for security)
   - Set to `false` to allow non-3D Secure payments (less secure, not recommended)
   - Default: `true`
   - **Note**: Even if set to `false`, Paymob dashboard integration settings may still require 3D Secure
   - **Warning**: Disabling 3D Secure increases fraud risk and may violate some payment regulations

## After Adding to .env:

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Test the integration in Paymob sandbox/test mode first

## Important: Return URL Configuration

### For Local Development:
Paymob **cannot** access `localhost` URLs. You need to use a public URL:

1. **Use ngrok** (recommended):
   ```bash
   ngrok http 8000
   ```
   Then set Paymob return URL to: `https://your-ngrok-url.ngrok.io/payment/return/paymob`

2. See `guides/PAYMOB_LOCAL_DEVELOPMENT.md` for detailed setup instructions

### For Production:
Set Paymob return URL in dashboard to:
```
https://yourdomain.com/payment/return/paymob
```

**Important:**
- Return URL must be publicly accessible (not localhost)
- Must use HTTPS in production
- Configure in Paymob Dashboard → Settings → Integrations → Return URL

## Important:
- Replace the example values above with your actual Paymob credentials
- Never share or commit your `.env` file
- Use test credentials first before going live
- Return URL must be publicly accessible (use ngrok for local development)

