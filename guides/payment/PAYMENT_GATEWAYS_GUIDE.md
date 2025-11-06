# Payment Gateways Integration Guide

## Overview

This guide explains how to add new payment gateways to the Medical Platform. The payment gateway system is built with clean architecture principles, making it easy to extend and maintain.

## Architecture

The payment gateway system follows a clean architecture pattern with the following components:

### 1. **Interface** (`PaymentGatewayInterface`)
- Defines the contract that all payment gateways must implement
- Located at: `app/PaymentGateways/Contracts/PaymentGatewayInterface.php`

### 2. **Base Class** (`BasePaymentGateway`)
- Provides common functionality for all gateways
- Handles configuration management
- Located at: `app/PaymentGateways/BasePaymentGateway.php`

### 3. **Manager** (`PaymentGatewayManager`)
- Factory pattern implementation
- Manages gateway instances
- Returns available gateways
- Located at: `app/PaymentGateways/PaymentGatewayManager.php`

### 4. **Response Object** (`PaymentResponse`)
- Standardized response format for all gateways
- Located at: `app/PaymentGateways/Contracts/PaymentResponse.php`

### 5. **Enum** (`PaymentGateway`)
- Defines all available payment gateways
- Located at: `app/Enums/PaymentGateway.php`

## Current Gateways

### 1. Cash on Delivery (COD)
- **Class**: `App\PaymentGateways\Gateways\CodGateway`
- **Name**: `cod`
- **Status**: Always enabled

### 2. Paymob
- **Class**: `App\PaymentGateways\Gateways\PaymobGateway`
- **Name**: `paymob`
- **Status**: Enabled via configuration

## How to Add a New Payment Gateway

### Step 1: Create the Gateway Class

Create a new gateway class in `app/PaymentGateways/Gateways/` directory. The class should extend `BasePaymentGateway` and implement `PaymentGatewayInterface`.

**Example: `StripeGateway.php`**

```php
<?php

namespace App\PaymentGateways\Gateways;

use App\PaymentGateways\BasePaymentGateway;
use App\PaymentGateways\Contracts\PaymentResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeGateway extends BasePaymentGateway
{
    public function getName(): string
    {
        return 'stripe';
    }

    public function getDisplayName(): string
    {
        return 'Stripe';
    }

    public function processPayment(array $data): PaymentResponse
    {
        try {
            $this->validateConfig(['api_key', 'secret_key']);

            $amount = $data['amount'] ?? 0;
            $orderId = $data['order_id'] ?? null;

            // Implement your payment processing logic here
            // Example: Create payment intent with Stripe API
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getConfigValue('secret_key'),
            ])->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => (int)($amount * 100), // Convert to cents
                'currency' => $data['currency'] ?? 'usd',
                'metadata' => [
                    'order_id' => $orderId,
                ],
            ]);

            if ($response->successful()) {
                $paymentIntent = $response->json();
                
                return PaymentResponse::success(
                    message: 'Payment intent created successfully',
                    redirectUrl: $paymentIntent['next_action']['redirect_to_url']['url'] ?? null,
                    transactionId: $paymentIntent['id'],
                    data: $paymentIntent,
                    gateway: $this->getName()
                );
            }

            return PaymentResponse::failure(
                'Failed to create payment intent',
                gateway: $this->getName()
            );
        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return PaymentResponse::failure(
                'Payment processing failed: ' . $e->getMessage(),
                gateway: $this->getName()
            );
        }
    }

    public function verifyPayment(array $data): PaymentResponse
    {
        try {
            $this->validateConfig(['webhook_secret']);

            // Verify webhook signature
            $signature = $data['signature'] ?? '';
            $payload = $data['payload'] ?? '';
            
            // Implement signature verification logic
            
            $event = json_decode($payload, true);
            
            if ($event['type'] === 'payment_intent.succeeded') {
                return PaymentResponse::success(
                    message: 'Payment verified successfully',
                    transactionId: $event['data']['object']['id'],
                    data: $event,
                    gateway: $this->getName()
                );
            }

            return PaymentResponse::failure(
                'Payment verification failed',
                gateway: $this->getName()
            );
        } catch (\Exception $e) {
            Log::error('Stripe verification error: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return PaymentResponse::failure(
                'Payment verification failed: ' . $e->getMessage(),
                gateway: $this->getName()
            );
        }
    }

    public function isEnabled(): bool
    {
        return $this->getConfigValue('enabled', false);
    }
}
```

### Step 2: Add Gateway to Enum

Add your gateway to the `PaymentGateway` enum in `app/Enums/PaymentGateway.php`:

```php
enum PaymentGateway: string
{
    case COD = 'cod';
    case PAYMOB = 'paymob';
    case STRIPE = 'stripe'; // Add this

    public function label(): string
    {
        return match($this) {
            self::COD => 'Cash on Delivery',
            self::PAYMOB => 'Paymob',
            self::STRIPE => 'Stripe', // Add this
        };
    }

    // ... rest of the enum
}
```

### Step 3: Register Gateway in Manager

Update `PaymentGatewayManager` in `app/PaymentGateways/PaymentGatewayManager.php`:

```php
public function gateway(string|PaymentGateway $gateway): PaymentGatewayInterface
{
    $gatewayName = $gateway instanceof PaymentGateway ? $gateway->value : $gateway;

    return match ($gatewayName) {
        'cod' => new CodGateway($this->getGatewayConfig('cod')),
        'paymob' => new PaymobGateway($this->getGatewayConfig('paymob')),
        'stripe' => new StripeGateway($this->getGatewayConfig('stripe')), // Add this
        default => throw new \Exception("Payment gateway '{$gatewayName}' not found"),
    };
}
```

### Step 4: Add Configuration

Add your gateway configuration to `config/payment_gateways.php`:

```php
return [
    // ... existing gateways

    'stripe' => [
        'enabled' => env('PAYMENT_GATEWAY_STRIPE_ENABLED', false),
        'api_key' => env('STRIPE_API_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'usd'),
    ],
];
```

### Step 5: Add Environment Variables

Add the required environment variables to your `.env` file:

```env
PAYMENT_GATEWAY_STRIPE_ENABLED=true
STRIPE_API_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_CURRENCY=usd
```

### Step 6: Configure Webhook URL

In your payment gateway dashboard (e.g., Stripe Dashboard), configure the webhook URL:

```
https://yourdomain.com/payment/callback/stripe
```

The webhook will be handled automatically by the `paymentCallback` method in `CheckoutController`.

## Payment Gateway Interface Methods

### `getName(): string`
Returns the unique identifier for the gateway (e.g., 'stripe', 'paypal').

### `getDisplayName(): string`
Returns the human-readable name for the gateway (e.g., 'Stripe', 'PayPal').

### `processPayment(array $data): PaymentResponse`
Processes a payment request. The `$data` array contains:
- `amount`: The payment amount
- `order_id`: The order ID
- `order_number`: The order number
- `currency`: The currency code
- `customer`: Customer information (name, email, phone, etc.)

Returns a `PaymentResponse` object with:
- `success`: Boolean indicating success/failure
- `message`: Human-readable message
- `redirectUrl`: URL to redirect user for payment (if applicable)
- `transactionId`: Transaction ID from the gateway
- `data`: Additional gateway-specific data

### `verifyPayment(array $data): PaymentResponse`
Verifies a payment callback/webhook. The `$data` array contains the raw callback data from the gateway.

Returns a `PaymentResponse` object indicating verification success/failure.

### `isEnabled(): bool`
Returns whether the gateway is enabled and can be used.

### `getConfig(): array`
Returns the gateway configuration array.

## PaymentResponse Helper Methods

### Static Methods

```php
// Create success response
PaymentResponse::success(
    message: 'Payment successful',
    redirectUrl: 'https://payment-url.com',
    transactionId: 'txn_123',
    data: ['additional' => 'data'],
    gateway: 'stripe'
);

// Create failure response
PaymentResponse::failure(
    message: 'Payment failed',
    transactionId: 'txn_123',
    data: ['error' => 'details'],
    gateway: 'stripe'
);
```

### Instance Methods

```php
$response->toArray(); // Convert to array
```

## Order Integration

When an order is placed, the system:

1. Creates the order with `payment_gateway` field set to the selected gateway
2. Calls `processPayment()` on the selected gateway
3. Stores `transaction_id` if provided by the gateway
4. Redirects user to payment URL if online payment is required
5. Updates order status based on payment result

## Webhook Handling

Payment gateway webhooks are automatically handled via the route:
```
POST /payment/callback/{gateway}
```

The `CheckoutController::paymentCallback()` method:
1. Verifies the webhook signature
2. Updates the order payment status
3. Returns appropriate response

## Testing

### Testing Payment Processing

1. Use test/sandbox credentials from your payment gateway
2. Place a test order with the new gateway
3. Verify the payment flow works correctly
4. Check that orders are created with correct `payment_gateway` and `transaction_id`

### Testing Webhooks

1. Use your gateway's webhook testing tools
2. Send test webhooks to your callback URL
3. Verify orders are updated correctly
4. Check logs for any errors

## Best Practices

1. **Error Handling**: Always wrap API calls in try-catch blocks and log errors
2. **Configuration Validation**: Use `validateConfig()` to ensure required config is present
3. **Logging**: Log all payment operations for debugging
4. **Security**: Verify webhook signatures to prevent unauthorized access
5. **Testing**: Test thoroughly in sandbox/test mode before production
6. **Documentation**: Document any gateway-specific requirements or quirks

## Example: Complete Paymob Integration

Refer to `app/PaymentGateways/Gateways/PaymobGateway.php` for a complete implementation example.

## Troubleshooting

### Gateway Not Appearing
- Check that `isEnabled()` returns `true`
- Verify configuration is set in `config/payment_gateways.php`
- Check environment variables are set correctly

### Payment Processing Fails
- Check logs in `storage/logs/laravel.log`
- Verify API credentials are correct
- Ensure gateway is properly configured in the gateway dashboard

### Webhook Not Working
- Verify webhook URL is correctly configured in gateway dashboard
- Check that webhook route is accessible (no auth required)
- Verify signature verification logic is correct

## Support

For issues or questions about adding payment gateways, refer to:
- Payment gateway official documentation
- Laravel HTTP client documentation: https://laravel.com/docs/http-client
- Application logs: `storage/logs/laravel.log`

