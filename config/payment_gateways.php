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
        'api_key' => env('PAYMOB_API_KEY'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID'), // Card integration ID (iframe)
        'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'), // Wallet integration ID
        'iframe_id' => env('PAYMOB_IFRAME_ID', env('PAYMOB_INTEGRATION_ID')),
        'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
        'currency' => env('PAYMOB_CURRENCY', 'EGP'),
        'return_url' => env('PAYMOB_RETURN_URL', null), // Configure in Paymob dashboard instead
        'require_3d_secure' => env('PAYMOB_REQUIRE_3D_SECURE', true), // Set to false to allow non-3D Secure payments (less secure)
    ],
];
