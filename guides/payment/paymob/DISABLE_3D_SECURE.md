# How to Disable 3D Secure (Not Recommended)

## ⚠️ Important Security Warning

**Disabling 3D Secure is NOT recommended** because:
- ❌ **Increased fraud risk** - Payments without 3D Secure are more vulnerable to fraud
- ❌ **Regulatory compliance** - Some regions (like Europe with PSD2) require 3D Secure
- ❌ **Chargeback liability** - You may be liable for fraudulent transactions
- ❌ **Bank requirements** - Some banks require 3D Secure for online payments

**Only disable 3D Secure if:**
- ✅ You have explicit approval from Paymob
- ✅ You understand the security risks
- ✅ Your business model requires it (e.g., low-value transactions)
- ✅ You have proper fraud detection in place

## 🔧 How to Disable 3D Secure

### Step 1: Update Your `.env` File

Add or update this line in your `.env` file:

```env
PAYMOB_REQUIRE_3D_SECURE=false
```

### Step 2: Clear Configuration Cache

```bash
php artisan config:clear
```

### Step 3: Configure Paymob Dashboard

**Important**: Even if you set `PAYMOB_REQUIRE_3D_SECURE=false` in your code, you **must also** configure Paymob dashboard:

1. Log in to Paymob Dashboard: https://accept.paymob.com/
2. Go to **Settings** → **Integrations**
3. Select your Card Integration (matching your `PAYMOB_INTEGRATION_ID`)
4. Look for **"3D Secure"** or **"3DS"** settings
5. Enable **"Allow non-3D Secure"** (or disable "Require 3D Secure")
6. Save the changes

**Note**: If Paymob dashboard doesn't allow disabling 3D Secure, you may need to:
- Contact Paymob support to enable this option
- Use a different integration that allows non-3D Secure payments
- Create a new integration with non-3D Secure enabled

### Step 4: Test the Configuration

1. Make a test payment
2. Check logs - should show `is_3d_secure: false` without treating it as an error
3. Payment should succeed even without 3D Secure authentication

## 📋 What Changes in the Code

When `PAYMOB_REQUIRE_3D_SECURE=false`:

1. **Error Analysis**: Missing 3D Secure will **not** be treated as a payment failure
2. **Logs**: Will show `3D Secure not completed (optional, is_3d_secure: false)` instead of an error
3. **Payment Processing**: Payments can succeed without 3D Secure authentication

## 🔍 How to Verify It's Working

### Check Configuration

```bash
php artisan tinker
```

```php
config('payment_gateways.paymob.require_3d_secure')
// Should return: false
```

### Check Logs

After a payment attempt, check logs for:

```
[INFO] Payment successful
is_3d_secure: false
indicators: ["3D Secure not completed (optional, is_3d_secure: false)"]
```

**Note**: The indicator will show "optional" instead of treating it as an error.

## 🆘 Troubleshooting

### Issue: Payment still fails even with `PAYMOB_REQUIRE_3D_SECURE=false`

**Possible Causes:**
1. **Paymob dashboard still requires 3D Secure**
   - Solution: Check Paymob dashboard integration settings
   - Enable "Allow non-3D Secure" in dashboard

2. **Configuration cache not cleared**
   - Solution: Run `php artisan config:clear`

3. **Bank requires 3D Secure**
   - Solution: Some banks/cards always require 3D Secure
   - Try a different card or contact the bank

4. **Integration type doesn't support non-3D Secure**
   - Solution: Contact Paymob support to verify your integration type
   - May need to use a different integration ID

### Issue: 3D Secure still appears

**Possible Causes:**
1. **Paymob dashboard settings override code settings**
   - Solution: Paymob dashboard settings take precedence
   - Update dashboard to allow non-3D Secure

2. **Card/bank requires 3D Secure**
   - Solution: Some cards always require 3D Secure regardless of settings
   - This is a bank/card issuer requirement, not a Paymob setting

## 📞 Contact Paymob Support

If you need to disable 3D Secure but can't find the option in your dashboard:

1. **Email**: support@paymob.com
2. **Subject**: "Request to enable non-3D Secure payments"
3. **Include**:
   - Your Integration ID
   - Your merchant account details
   - Reason for disabling 3D Secure
   - Business justification

## 🔄 Re-enabling 3D Secure

If you want to re-enable 3D Secure later:

1. Update `.env`:
   ```env
   PAYMOB_REQUIRE_3D_SECURE=true
   ```

2. Clear config cache:
   ```bash
   php artisan config:clear
   ```

3. Update Paymob dashboard:
   - Disable "Allow non-3D Secure"
   - Enable "Require 3D Secure"

## 📚 Related Documentation

- [3D Secure Troubleshooting](./3D_SECURE_TROUBLESHOOTING.md)
- [Paymob Test Credentials](./PAYMOB_TEST_CREDENTIALS.md)
- [Paymob Setup Guide](./PAYMOB_SETUP_GUIDE.md)

