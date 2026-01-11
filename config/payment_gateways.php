<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for all payment gateways.
    | Each gateway can be enabled/disabled and configured individually.
    |
    */

    'cod' => [
        'enabled' => env('PAYMENT_GATEWAY_COD_ENABLED', true),
    ],

    'paymob' => [
        'enabled' => env('PAYMENT_GATEWAY_PAYMOB_ENABLED', true),

        // New Unified Checkout credentials
        'secret_key' => env('PAYMOB_SECRET_KEY'),
        'public_key' => env('PAYMOB_PUBLIC_KEY'),

        // Legacy credentials (still used for wallet payments, can also be used as fallback)
        'api_key' => env('PAYMOB_API_KEY'), // Falls back to this if secret_key not set

        // Integration IDs (get from Paymob Dashboard -> Developers -> Payment Integrations)
        'integration_id' => env('PAYMOB_INTEGRATION_ID'), // Card integration ID
        'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'), // Wallet integration ID

        // HMAC for webhook verification
        'hmac_secret' => env('PAYMOB_HMAC_SECRET'),

        // Callback URLs for Unified Checkout
        'callback_url' => env('PAYMOB_RETURN_URL'), // Webhook notification URL
        'redirect_url' => env('PAYMOB_RETURN_URL'), // Where to redirect after payment

        // Other settings
        'currency' => env('PAYMOB_CURRENCY', 'EGP'),
        'require_3d_secure' => env('PAYMOB_REQUIRE_3D_SECURE', true),
        'fee_fixed' => env('PAYMOB_FEE_FIXED', 3),
        'fee_percent' => env('PAYMOB_FEE_PERCENT', 5),

        // Deprecated - keeping for backward compatibility (not used with Unified Checkout)
        'iframe_id' => env('PAYMOB_IFRAME_ID', env('PAYMOB_INTEGRATION_ID')),
        'return_url' => env('PAYMOB_RETURN_URL', null),
    ],
];
