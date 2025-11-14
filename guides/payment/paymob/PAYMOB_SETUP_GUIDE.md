# Paymob Setup Guide

## How to Get Paymob Credentials

### Step 1: Sign Up for Paymob Account

1. Go to [https://www.paymob.com/](https://www.paymob.com/)
2. Click on "Sign Up" or "Get Started"
3. Fill in your business information
4. Complete the registration process
5. Verify your email and complete KYC (Know Your Customer) verification

### Step 2: Access Paymob Dashboard

1. Log in to your Paymob account
2. Navigate to the **Dashboard** or **Settings** section

### Step 3: Get Your Credentials

#### 1. API Key (`PAYMOB_API_KEY`)
- Location: **Settings** → **API Keys** or **Developer Settings**
- Look for "API Key" or "Authentication Token"
- This is your main authentication key for Paymob API

#### 2. Integration ID (`PAYMOB_INTEGRATION_ID`)
- Location: **Settings** → **Integrations** or **Payment Methods**
- Create a new integration or select an existing one
- The Integration ID is shown in the integration details
- This identifies which payment method you're using (Card, Wallet, etc.)

#### 3. Iframe ID (`PAYMOB_IFRAME_ID`)
- Usually the same as `PAYMOB_INTEGRATION_ID`
- Location: **Settings** → **Integrations** → Select your integration
- Look for "Iframe ID" or check the integration details
- If not available, use the same value as `PAYMOB_INTEGRATION_ID`

#### 4. HMAC Secret (`PAYMOB_HMAC_SECRET`)
- Location: **Settings** → **Webhooks** or **Security**
- Look for "HMAC Secret" or "Webhook Secret"
- This is used to verify webhook signatures from Paymob
- If not visible, you may need to generate it or contact Paymob support

#### 5. Currency (`PAYMOB_CURRENCY`)
- Default: `EGP` (Egyptian Pound)
- Set this to match your currency (e.g., `USD`, `EUR`, `SAR`)
- Location: Usually in account settings or integration settings

### Step 4: Configure Webhook URL

In your Paymob dashboard:
1. Go to **Settings** → **Webhooks**
2. Add webhook URL: `https://yourdomain.com/payment/callback/paymob`
3. Select events: Payment Success, Payment Failed
4. Save the webhook configuration

### Step 5: Add Credentials to .env File

Add these lines to your `.env` file:

```env
PAYMENT_GATEWAY_PAYMOB_ENABLED=true
PAYMOB_API_KEY=your_api_key_here
PAYMOB_INTEGRATION_ID=your_integration_id_here
PAYMOB_IFRAME_ID=your_iframe_id_here
PAYMOB_HMAC_SECRET=your_hmac_secret_here
PAYMOB_CURRENCY=EGP
```

### Step 6: Test Your Integration

1. Use Paymob's test/sandbox environment first
2. Test with small amounts
3. Verify webhook is receiving callbacks
4. Check logs in `storage/logs/laravel.log`

## Important Notes

- **Test Mode**: Paymob provides test credentials for development
- **Production**: Request production credentials after testing
- **Security**: Never commit `.env` file to version control
- **HMAC**: Required for webhook verification - keep it secure

## Support

- Paymob Documentation: [https://docs.paymob.com/](https://docs.paymob.com/)
- Paymob Support: Contact through dashboard or email
- API Documentation: Available in Paymob dashboard under Developer section

## Troubleshooting

**Can't find HMAC Secret?**
- Check webhook settings
- Contact Paymob support
- It might be generated after first webhook setup

**Integration ID not working?**
- Ensure integration is activated
- Check if payment method is enabled
- Verify you're using the correct integration for your region

**Webhook not receiving callbacks?**
- Verify URL is publicly accessible
- Check webhook is enabled in Paymob dashboard
- Ensure HTTPS is used (not HTTP)

