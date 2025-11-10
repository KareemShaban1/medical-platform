# Paymob Test Card Credentials

This guide provides test card credentials for testing Paymob payment integration in sandbox/test mode.

## ⚠️ Important Notes

- **These cards work ONLY in test/sandbox mode**
- **Never use these in production**
- **Test cards will fail in production** (as detected by our system)
- Make sure your Paymob account is in **test/sandbox mode** when using these

## ✅ Successful Payment Test Cards

### Card 1: Standard Visa (Recommended)
- **Card Number**: `4987654321098769`
- **Expiry Date**: Any future date (e.g., `12/25`)
- **CVV**: Any 3 digits (e.g., `123`)
- **Result**: Payment will be approved

### Card 2: MasterCard
- **Card Number**: `5123456789012346`
- **Expiry Date**: Any future date (e.g., `12/25`)
- **CVV**: Any 3 digits (e.g., `123`)
- **Result**: Payment will be approved

### Card 3: Visa (Alternative)
- **Card Number**: `4987654321098769`
- **Expiry Date**: `05/31`
- **CVV**: `100`
- **Result**: Payment will be approved

## ❌ Failed Payment Test Cards

### Card 4: Insufficient Funds
- **Card Number**: `5123456789012346`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **Result**: Payment declined - Insufficient funds (Response Code: 51)

### Card 5: Invalid Card Number
- **Card Number**: `1111111111111111` or `0000000000000000`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **Result**: Payment declined - Invalid card number (Response Code: 14)
- **Note**: Our system detects this as a test card

### Card 6: Expired Card
- **Card Number**: `4987654321098769`
- **Expiry Date**: Past date (e.g., `01/20`)
- **CVV**: Any 3 digits
- **Result**: Payment declined - Expired card (Response Code: 54)

### Card 7: Declined Card
- **Card Number**: `5123456789012346`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **Result**: Payment declined - Do not honor (Response Code: 05)

## 🔐 3D Secure Test Cards

### ⚠️ Important: 3D Secure is Required

**Most Paymob integrations require 3D Secure authentication**. If you see `is_3d_secure: false` in your logs, it means:
- ❌ 3D Secure authentication was **not completed**
- ❌ User may have **cancelled** the authentication popup
- ❌ Card may not support 3D Secure
- ❌ Browser may have **blocked** the 3D Secure popup
- ❌ Bank security restrictions

### Card 8: 3D Secure Required (Success)
- **Card Number**: `4987654321098769`
- **Expiry Date**: Any future date (e.g., `12/25`)
- **CVV**: Any 3 digits (e.g., `123`)
- **3D Secure Password**: 
  - In **test mode**: Usually `1234` or `0000` (check Paymob test page)
  - Or use the OTP/password shown on the 3D Secure page
- **Steps to Success**:
  1. Enter card details in Paymob iframe
  2. **Wait for 3D Secure popup/page** to appear
  3. **DO NOT close the popup** - this is critical!
  4. Enter the 3D Secure password/OTP (usually `1234` in test mode)
  5. Click "Submit" or "Authenticate"
  6. Wait for redirect back to your application
- **Result**: ✅ Payment approved (`is_3d_secure: true`, `payment_status: PAID`)

### Card 9: 3D Secure Failed (Cancelled by User)
- **Card Number**: `4987654321098769`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **Action**: **Close the 3D Secure popup/page without completing authentication**
- **Result**: ❌ Payment failed (`is_3d_secure: false`, `payment_status: UNPAID`)
- **Error Message**: "Payment failed. 3D Secure authentication was not completed..."
- **This matches your current issue!**

### Card 10: 3D Secure Failed (Wrong Password)
- **Card Number**: `5123456789012346`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **3D Secure Password**: Wrong password (e.g., `9999`)
- **Result**: ❌ 3D Secure authentication failed

### Common 3D Secure Issues & Solutions

1. **3D Secure popup blocked by browser**:
   - ✅ **Solution**: Allow popups for Paymob domain (`accept.paymob.com`)
   - Check browser popup blocker settings
   - Try different browser

2. **3D Secure page not appearing** (Your Current Issue):
   - ✅ **Solution**: This is a Paymob dashboard configuration issue
   - **Step 1**: Go to Paymob Dashboard → Settings → Integrations
   - **Step 2**: Select your Card Integration (the one with Integration ID you're using)
   - **Step 3**: Check "3D Secure" settings:
     - Ensure "Require 3D Secure" is **enabled**
     - Or "Allow non-3D Secure" should be **disabled**
   - **Step 4**: Save settings and test again
   - **Step 5**: If still not appearing, contact Paymob support
   - **Note**: Some integrations may have 3D Secure disabled by default
   - **Alternative**: Use a different integration ID that has 3D Secure enabled

3. **User closes 3D Secure popup** (Your current issue):
   - ✅ **Solution**: **Complete the 3D Secure authentication**
   - Don't close the popup/page
   - Enter password and submit

4. **3D Secure timeout**:
   - ✅ **Solution**: Complete authentication within 5-10 minutes
   - Don't leave the page open too long

## 📱 Wallet Payment Test (Mobile Wallet)

For wallet payments (Vodafone Cash, Orange Money, etc.):
- **Phone Number**: Use test phone numbers provided by Paymob
- **OTP**: Use test OTP from Paymob dashboard
- **Note**: Wallet testing requires specific test credentials from Paymob

## 🧪 Common Test Scenarios

### Scenario 1: Successful Payment
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
Expected: Payment approved, order/offer marked as paid
```

### Scenario 2: Test Card Detection
```
Card: 1111 (last 4 digits)
Expected: System detects as test card, shows appropriate error
```

### Scenario 3: 3D Secure Flow (Success)
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
3D Secure: Enter password (usually 1234 or 0000 in test mode)
Expected: Payment approved after 3D Secure (is_3d_secure: true)
```

### Scenario 3b: 3D Secure Flow (Failure)
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
3D Secure: Close popup or cancel authentication
Expected: Payment failed - 3D Secure not completed (is_3d_secure: false)
Error: "Payment failed. 3D Secure authentication was not completed..."
```

### Scenario 4: Payment Decline
```
Card: 5123456789012346 (with insufficient funds scenario)
Expected: Payment declined, error logged with response code
```

## 📊 Response Codes Reference

When testing, you'll see these response codes in logs:

| Code | Meaning | Test Card Behavior |
|------|---------|-------------------|
| `00` or `APPROVED` | Payment approved | Use successful test cards |
| `05` | Do not honor | Card declined by bank |
| `14` | Invalid card number | Use invalid card numbers |
| `51` | Insufficient funds | Simulated by Paymob |
| `54` | Expired card | Use past expiry date |
| `57` | Transaction not permitted | Card restrictions |
| `61` | Exceeds withdrawal limit | Amount too high |
| `62` | Restricted card | Card blocked |
| `96` | System malfunction | Gateway error |

## 🔍 How to Verify Test Mode

1. **Check Paymob Dashboard**:
   - Go to Paymob Dashboard
   - Check if you're in "Test Mode" or "Sandbox"
   - Test transactions will appear in test transaction list

2. **Check Transaction Details**:
   - In test mode, transactions have `is_live: false`
   - Test cards show specific patterns (e.g., PAN: 1111)

3. **Check Logs**:
   - Our system logs: `"is_live": false` for test transactions
   - Test cards are detected and logged

## 🚀 Quick Test Checklist

- [ ] Paymob account is in test/sandbox mode
- [ ] Using test card numbers (not real cards)
- [ ] Return URL is configured correctly
- [ ] Check logs for transaction details
- [ ] Verify payment status updates correctly
- [ ] Test both success and failure scenarios

## 📝 Notes

1. **Test cards may vary** by Paymob account configuration
2. **Contact Paymob support** if test cards don't work
3. **Always check logs** for detailed error information
4. **Test in sandbox first** before going to production

## 🔗 Resources

- [Paymob Developer Documentation](https://developers.paymob.com)
- [Paymob Test Credentials Guide](https://developers.paymob.com/guides/test-credentials-1)
- Paymob Support: support@paymob.com

## ⚡ Quick Reference

**Most Common Test Card (Success with 3D Secure)**:
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
3D Secure Password: 1234 (or as shown)
Expected: ✅ Payment approved (is_3d_secure: true)
```

**Test Card (Decline)**:
```
Card: 1111111111111111
Expiry: 12/25
CVV: 123
Expected: ❌ Invalid card number error
```

**3D Secure Not Completed (Your Current Issue)**:
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
3D Secure: Not completed (popup closed/cancelled)
Expected: ❌ Payment failed - 3D Secure not completed
Error Message: "Payment failed. 3D Secure authentication was not completed..."
Log Shows: is_3d_secure: false, payment_status: UNPAID
```

## 🔧 Troubleshooting 3D Secure Issues

### Issue: Payment fails with "3D Secure not completed"

**Possible Causes:**
1. ✅ **User cancelled 3D Secure** - Most common
   - Solution: Complete the 3D Secure authentication
   - Don't close the popup/page

2. ✅ **Browser blocked popup**
   - Solution: Allow popups for Paymob domain
   - Check browser settings

3. ✅ **Card doesn't support 3D Secure**
   - Solution: Use a card that supports 3D Secure
   - Or configure Paymob to allow non-3D Secure cards

4. ✅ **3D Secure timeout**
   - Solution: Complete authentication quickly
   - Usually 5-10 minute timeout

5. ✅ **Paymob integration requires 3D Secure**
   - Solution: Check Paymob dashboard settings
   - May need to enable "Allow non-3D Secure" if available

### How to Test 3D Secure Successfully:

1. **Use test card**: `4987654321098769`
2. **Enter card details** in Paymob iframe
3. **Wait for 3D Secure page** to appear (don't close!)
4. **Enter 3D Secure password**: Usually `1234` or `0000` in test mode
5. **Click "Authenticate"** or "Submit"
6. **Wait for redirect** back to your application
7. **Check logs**: Should show `is_3d_secure: true`

