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

### Card 8: 3D Secure Required
- **Card Number**: `4987654321098769`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **3D Secure Password**: `1234` (or as shown in Paymob test page)
- **Result**: Requires 3D Secure authentication

### Card 9: 3D Secure Failed
- **Card Number**: `5123456789012346`
- **Expiry Date**: Any future date
- **CVV**: Any 3 digits
- **3D Secure Password**: Wrong password (e.g., `9999`)
- **Result**: 3D Secure authentication failed

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

### Scenario 3: 3D Secure Flow
```
Card: 4987654321098769
3D Secure: Complete authentication
Expected: Payment approved after 3D Secure
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

**Most Common Test Card (Success)**:
```
Card: 4987654321098769
Expiry: 12/25
CVV: 123
```

**Test Card (Decline)**:
```
Card: 1111111111111111
Expiry: 12/25
CVV: 123
Expected: Invalid card number error
```

