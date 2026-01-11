<?php

namespace App\PaymentGateways\Gateways;

use App\PaymentGateways\BasePaymentGateway;
use App\PaymentGateways\Contracts\PaymentResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobGateway extends BasePaymentGateway
{
    // Legacy API URL (still used for wallet payments and some operations)
    private const LEGACY_API_URL = 'https://accept.paymob.com/api';

    // New Unified Checkout API URL
    private const INTENTION_API_URL = 'https://accept.paymob.com/v1/intention/';

    // get the name of the gateway
    public function getName(): string
    {
        return 'paymob';
    }

    // get the display name of the gateway
    public function getDisplayName(): string
    {
        return 'Paymob';
    }

    // process the payment using the new Unified Checkout (Intention API)
    public function processPayment(array $data): PaymentResponse
    {
        try {
            $this->validateConfig(['integration_id', 'hmac_secret']);

            $amount = $data['amount'] ?? 0;
            $orderId = $data['order_id'] ?? null;
            $orderNumber = $data['order_number'] ?? null;
            $customerInfo = $data['customer'] ?? [];
            $method = $data['method'] ?? 'card'; // 'card' | 'wallet'
            $walletPhone = $data['wallet_phone'] ?? null;
            $currency = $data['currency'] ?? 'EGP';

            // Ensure all required fields have valid values (not empty strings)
            $firstName = !empty($customerInfo['first_name']) ? $customerInfo['first_name'] : 'Customer';
            $lastName = !empty($customerInfo['last_name']) ? $customerInfo['last_name'] : 'User';
            $email = !empty($customerInfo['email']) ? $customerInfo['email'] : 'customer@example.com';
            $phone = !empty($customerInfo['phone']) ? $customerInfo['phone'] : '01000000000';

            // Choose integration id based on method (card vs wallet)
            $integrationId = $method === 'wallet'
                ? ($this->getConfigValue('wallet_integration_id') ?? $this->getConfigValue('integration_id'))
                : $this->getConfigValue('integration_id');

            // For wallet payments, use the LEGACY flow with OLD api_key (JWT token)
            if ($method === 'wallet') {
                // Wallet uses the OLD api_key (the JWT-like token starting with ZXlK...)
                $legacyApiKey = $this->getConfigValue('api_key');
                if (empty($legacyApiKey)) {
                    return PaymentResponse::failure(
                        'Missing required configuration: api_key (required for wallet payments)',
                        gateway: $this->getName()
                    );
                }
                return $this->processWalletPayment($data, $legacyApiKey, $integrationId);
            }

            // For card payments, use the NEW Unified Checkout with secret_key (egy_sk_...)
            $secretKey = $this->getConfigValue('secret_key');
            if (empty($secretKey)) {
                return PaymentResponse::failure(
                    'Missing required configuration: secret_key (required for card payments with Unified Checkout)',
                    gateway: $this->getName()
                );
            }

            return $this->processCardPaymentWithIntention($data, $secretKey, $integrationId);
        } catch (\Exception $e) {
            Log::error('Paymob payment error: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return PaymentResponse::failure(
                'Payment processing failed: ' . $e->getMessage(),
                gateway: $this->getName()
            );
        }
    }

    /**
     * Process card payment using the new Unified Checkout (Intention API)
     * This replaces the deprecated iFrame approach
     */
    private function processCardPaymentWithIntention(array $data, string $secretKey, int|string $integrationId): PaymentResponse
    {
        $amount = $data['amount'] ?? 0;
        $orderId = $data['order_id'] ?? null;
        $orderNumber = $data['order_number'] ?? null;
        $customerInfo = $data['customer'] ?? [];
        $currency = $data['currency'] ?? 'EGP';

        // Prepare customer data
        $firstName = !empty($customerInfo['first_name']) ? $customerInfo['first_name'] : 'Customer';
        $lastName = !empty($customerInfo['last_name']) ? $customerInfo['last_name'] : 'User';
        $email = !empty($customerInfo['email']) ? $customerInfo['email'] : 'customer@example.com';
        $phone = !empty($customerInfo['phone']) ? $customerInfo['phone'] : '01000000000';

        // Build the intention payload for Unified Checkout
        $intentionPayload = [
            'amount' => (int)($amount * 100), // Amount in cents
            'currency' => $currency,
            'payment_methods' => [(int)$integrationId, "card"], // Array of integration IDs
            'billing_data' => [
                'apartment' => !empty($customerInfo['apartment']) ? $customerInfo['apartment'] : 'NA',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'street' => !empty($customerInfo['street']) ? $customerInfo['street'] : 'NA',
                'building' => !empty($customerInfo['building']) ? $customerInfo['building'] : 'NA',
                'phone_number' => $phone,
                'city' => !empty($customerInfo['city']) ? $customerInfo['city'] : 'Cairo',
                'country' => !empty($customerInfo['country']) ? $customerInfo['country'] : 'EG',
                'email' => $email,
                'floor' => !empty($customerInfo['floor']) ? $customerInfo['floor'] : 'NA',
                'state' => !empty($customerInfo['state']) ? $customerInfo['state'] : 'NA',
            ],
            'customer' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
            ],
            'items' => [
                [
                    'name' => $data['item_name'] ?? 'Order Payment',
                    'amount' => (int)($amount * 100),
                    'description' => $data['item_description'] ?? 'Payment for order',
                    'quantity' => 1,
                ],
            ],
        ];

        // Add merchant order id if available
        if ($orderNumber || $orderId) {
            $intentionPayload['merchant_order_id'] = (string)($orderNumber ?? $orderId);
        }

        // Add notification/callback URL if configured
        $callbackUrl = $this->getConfigValue('callback_url') ?? $data['callback_url'] ?? null;
        if ($callbackUrl) {
            $intentionPayload['notification_url'] = $callbackUrl;
        }

        // Add redirect URL if configured (for after payment completion)
        $redirectUrl = $this->getConfigValue('redirect_url') ?? $data['redirect_url'] ?? null;
        if ($redirectUrl) {
            $intentionPayload['redirection_url'] = $redirectUrl;
        }

        Log::info('Creating Paymob payment intention (Unified Checkout)', [
            'integration_id' => $integrationId,
            'amount_cents' => $intentionPayload['amount'],
            'currency' => $currency,
            'merchant_order_id' => $intentionPayload['merchant_order_id'] ?? null,
        ]);

        // Call the Intention API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->post(self::INTENTION_API_URL, $intentionPayload);

            $responseData = $response->json();

            Log::info('Paymob Intention API response', [
                'status' => $response->status(),
                'has_client_secret' => isset($responseData['client_secret']),
                'has_id' => isset($responseData['id']),
            ]);

            if (!$response->successful()) {
                Log::error('Paymob Intention API failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                $errorMessage = $responseData['message'] ?? $responseData['detail'] ?? 'Failed to create payment intention';
                return PaymentResponse::failure($errorMessage, gateway: $this->getName());
            }

            // Get the client_secret from response
            $clientSecret = $responseData['client_secret'] ?? null;
            $intentionId = $responseData['id'] ?? null;

            if (empty($clientSecret)) {
                Log::error('Paymob Intention API response missing client_secret', [
                    'response' => $responseData,
                ]);
                return PaymentResponse::failure('Invalid response from Paymob: missing client_secret', gateway: $this->getName());
            }

            // Build the Unified Checkout redirect URL
            // Format: https://accept.paymob.com/unifiedcheckout/?publicKey={PUBLIC_KEY}&clientSecret={CLIENT_SECRET}
            $publicKey = $this->getConfigValue('public_key');

            if (!empty($publicKey)) {
                // If public_key is available, use the SDK-style URL
                $checkoutUrl = "https://accept.paymob.com/unifiedcheckout/?publicKey={$publicKey}&clientSecret={$clientSecret}";
            } else {
                // Alternative: Direct redirect URL (simpler, works without public_key)
                $checkoutUrl = "https://accept.paymob.com/unifiedcheckout/?clientSecret={$clientSecret}";
            }

            Log::info('Paymob Unified Checkout URL generated', [
                'intention_id' => $intentionId,
                'checkout_url' => $checkoutUrl,
                'has_public_key' => !empty($publicKey),
            ]);

            return PaymentResponse::success(
                message: 'Payment URL generated successfully',
                redirectUrl: $checkoutUrl,
                transactionId: (string)$intentionId,
                data: [
                    'intention_id' => $intentionId,
                    'client_secret' => $clientSecret,
                    'paymob_order_id' => $intentionId, // For backward compatibility
                ],
                gateway: $this->getName()
            );
        } catch (\Exception $e) {
            Log::error('Paymob Intention API error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return PaymentResponse::failure(
                'Failed to create payment intention: ' . $e->getMessage(),
                gateway: $this->getName()
            );
        }
    }

    /**
     * Process wallet payment using the legacy flow
     * Wallet payments still work well with the old API
     */
    private function processWalletPayment(array $data, string $secretKey, int|string $integrationId): PaymentResponse
    {
        $amount = $data['amount'] ?? 0;
        $orderId = $data['order_id'] ?? null;
        $orderNumber = $data['order_number'] ?? null;
        $customerInfo = $data['customer'] ?? [];
        $walletPhone = $data['wallet_phone'] ?? null;
        $currency = $data['currency'] ?? 'EGP';

        if (empty($walletPhone)) {
            return PaymentResponse::failure('Wallet phone is required', gateway: $this->getName());
        }

        // Step 1: Get authentication token (using secret_key as api_key)
        $authToken = $this->getAuthToken($secretKey);
        if (!$authToken) {
            return PaymentResponse::failure(
                'Failed to authenticate with Paymob',
                gateway: $this->getName()
            );
        }

        // Step 2: Create order
        $paymobOrder = $this->createOrder($authToken, [
            'amount_cents' => (int)($amount * 100),
            'currency' => $currency,
            'merchant_order_id' => $orderNumber ?? $orderId,
        ]);

        if (!$paymobOrder) {
            return PaymentResponse::failure(
                'Failed to create Paymob order',
                gateway: $this->getName()
            );
        }

        // Step 3: Create payment key
        $firstName = !empty($customerInfo['first_name']) ? $customerInfo['first_name'] : 'Customer';
        $lastName = !empty($customerInfo['last_name']) ? $customerInfo['last_name'] : 'User';
        $email = !empty($customerInfo['email']) ? $customerInfo['email'] : 'customer@example.com';
        $phone = !empty($customerInfo['phone']) ? $customerInfo['phone'] : '01000000000';

        $paymentKeyData = [
            'amount_cents' => (int)($amount * 100),
            'currency' => $currency,
            'order_id' => $paymobOrder['id'],
            'integration_id' => (int)$integrationId,
            'lock_order_when_paid' => false,
            'billing_data' => [
                'apartment' => !empty($customerInfo['apartment']) ? $customerInfo['apartment'] : 'NA',
                'email' => $email,
                'floor' => !empty($customerInfo['floor']) ? $customerInfo['floor'] : 'NA',
                'first_name' => $firstName,
                'street' => !empty($customerInfo['street']) ? $customerInfo['street'] : 'NA',
                'building' => !empty($customerInfo['building']) ? $customerInfo['building'] : 'NA',
                'phone_number' => $phone,
                'shipping_method' => 'PKG',
                'postal_code' => !empty($customerInfo['postal_code']) ? $customerInfo['postal_code'] : 'NA',
                'city' => !empty($customerInfo['city']) ? $customerInfo['city'] : 'Cairo',
                'country' => !empty($customerInfo['country']) ? $customerInfo['country'] : 'EG',
                'last_name' => $lastName,
                'state' => !empty($customerInfo['state']) ? $customerInfo['state'] : 'NA',
            ],
        ];

        $paymentKey = $this->createPaymentKey($authToken, $paymentKeyData);

        if (!$paymentKey) {
            return PaymentResponse::failure(
                'Failed to create payment key',
                gateway: $this->getName()
            );
        }

        // Step 4: Initiate wallet payment
        $walletResult = $this->initiateWalletPayment($paymentKey, $walletPhone);
        if (!$walletResult || empty($walletResult['redirect_url'])) {
            return PaymentResponse::failure('Failed to initiate wallet payment', gateway: $this->getName());
        }

        return PaymentResponse::success(
            message: 'Wallet payment initiated successfully',
            redirectUrl: $walletResult['redirect_url'],
            transactionId: (string)$paymobOrder['id'],
            data: [
                'paymob_order_id' => $paymobOrder['id'],
                'payment_key' => $paymentKey,
            ],
            gateway: $this->getName()
        );
    }


    // verify the payment
    public function verifyPayment(array $data): PaymentResponse
    {
        try {
            // Check if this is a redirect return (query params) or webhook (POST)
            $isRedirect = isset($data['success']) && !isset($data['obj']);

            if ($isRedirect) {
                // This is a redirect return from Paymob
                $success = $data['success'] === 'true' || $data['success'] === true || $data['success'] === '1';
                $transactionId = $data['id'] ?? $data['transaction_id'] ?? null;

                // For redirect returns, HMAC verification is optional
                // But if HMAC is provided, verify it
                if (isset($data['hmac']) && !empty($data['hmac'])) {
                    $this->validateConfig(['hmac_secret']);
                    $hmac = $data['hmac'] ?? '';
                    $calculatedHmac = $this->calculateHmac($data);

                    if ($hmac !== $calculatedHmac) {
                        return PaymentResponse::failure(
                            'Invalid HMAC signature',
                            gateway: $this->getName()
                        );
                    }
                }

                if ($success && $transactionId) {
                    return PaymentResponse::success(
                        message: 'Payment verified successfully',
                        transactionId: (string)$transactionId,
                        data: $data,
                        gateway: $this->getName()
                    );
                }

                return PaymentResponse::failure(
                    'Payment was not successful',
                    transactionId: $transactionId ? (string)$transactionId : null,
                    data: $data,
                    gateway: $this->getName()
                );
            }

            // This is a webhook callback
            $this->validateConfig(['hmac_secret']);

            // Verify HMAC signature
            $hmac = $data['hmac'] ?? '';
            $calculatedHmac = $this->calculateHmac($data);

            if ($hmac !== $calculatedHmac) {
                return PaymentResponse::failure(
                    'Invalid HMAC signature',
                    gateway: $this->getName()
                );
            }

            $success = $data['success'] ?? false;
            $transactionId = $data['obj']['id'] ?? null;

            if ($success && $transactionId) {
                return PaymentResponse::success(
                    message: 'Payment verified successfully',
                    transactionId: (string)$transactionId,
                    data: $data,
                    gateway: $this->getName()
                );
            }

            return PaymentResponse::failure(
                'Payment verification failed',
                transactionId: $transactionId ? (string)$transactionId : null,
                data: $data,
                gateway: $this->getName()
            );
        } catch (\Exception $e) {
            Log::error('Paymob verification error: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            return PaymentResponse::failure(
                'Payment verification failed: ' . $e->getMessage(),
                gateway: $this->getName()
            );
        }
    }

    /**
     * Get authentication token from Paymob
     * @param string|null $apiKey Optional API key - if not provided, uses config value
     */
    private function getAuthToken(?string $apiKey = null): ?string
    {
        try {
            $key = $apiKey ?? $this->getConfigValue('api_key') ?? $this->getConfigValue('secret_key');

            $response = Http::post(self::LEGACY_API_URL . '/auth/tokens', [
                'api_key' => $key,
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            Log::error('Paymob auth failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob auth error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create order in Paymob
     */
    private function createOrder(string $authToken, array $orderData): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$authToken}",
            ])->post(self::LEGACY_API_URL . '/ecommerce/orders', $orderData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Paymob create order failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob create order error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create payment key
     */
    private function createPaymentKey(string $authToken, array $paymentData): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$authToken}",
            ])->post(self::LEGACY_API_URL . '/acceptance/payment_keys', $paymentData);

            if ($response->successful()) {
                return $response->json('token');
            }

            Log::error('Paymob create payment key failed', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob create payment key error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate wallet payment and return redirect URL
     */
    private function initiateWalletPayment(string $paymentKey, string $walletPhone): ?array
    {
        try {
            $payload = [
                'source' => [
                    'identifier' => $walletPhone,
                    'subtype' => 'WALLET',
                    'type' => 'WALLET',
                ],
                'payment_token' => $paymentKey,
            ];

            $response = Http::post(self::LEGACY_API_URL . '/acceptance/payments/pay', $payload);

            $json = $response->json();
            // Paymob may return redirection_url or redirect_url
            $redirectUrl = $json['redirection_url']
                ?? $json['redirect_url']
                ?? ($json['data']['redirection_url'] ?? ($json['data']['redirect_url'] ?? null));

            if ($redirectUrl) {
                return ['redirect_url' => $redirectUrl];
            }

            Log::error('Paymob wallet payment init failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob wallet payment init error: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Get transaction details from Paymob API
     * This can provide detailed error information that's not in the redirect response
     */
    public function getTransactionDetails(string $transactionId): ?array
    {
        try {
            $authToken = $this->getAuthToken();
            if (!$authToken) {
                Log::warning('Failed to get auth token for transaction details', [
                    'transaction_id' => $transactionId,
                ]);
                return null;
            }

            // Paymob API endpoint to get transaction details
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$authToken}",
            ])->get(self::LEGACY_API_URL . '/acceptance/transactions/' . $transactionId);

            if ($response->successful()) {
                $transactionData = $response->json();

                Log::info('Paymob transaction details retrieved', [
                    'transaction_id' => $transactionId,
                    'has_data' => !empty($transactionData),
                ]);

                return $transactionData;
            }

            Log::warning('Failed to get transaction details from Paymob', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching Paymob transaction details: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Calculate HMAC for webhook verification
     */
    private function calculateHmac(array $data): string
    {
        $hmacSecret = $this->getConfigValue('hmac_secret');
        $amount = $data['amount_cents'] ?? '';
        $createdAt = $data['created_at'] ?? '';
        $currency = $data['currency'] ?? '';
        $errorOccurred = $data['error_occurred'] ?? '';
        $hasParentTransaction = $data['has_parent_transaction'] ?? '';
        $id = $data['obj']['id'] ?? '';
        $integrationId = $data['obj']['integration_id'] ?? '';
        $is3DSecure = $data['obj']['is_3d_secure'] ?? '';
        $isAuth = $data['obj']['is_auth'] ?? '';
        $isCapture = $data['obj']['is_capture'] ?? '';
        $isRefunded = $data['obj']['is_refunded'] ?? '';
        $isStandalonePayment = $data['obj']['is_standalone_payment'] ?? '';
        $isVoided = $data['obj']['is_voided'] ?? '';
        $orderId = $data['obj']['order']['id'] ?? '';
        $owner = $data['obj']['owner'] ?? '';
        $pending = $data['obj']['pending'] ?? '';
        $sourceDataPan = $data['obj']['source_data']['pan'] ?? '';
        $sourceDataSubType = $data['obj']['source_data']['sub_type'] ?? '';
        $sourceDataType = $data['obj']['source_data']['type'] ?? '';
        $success = $data['success'] ?? '';

        $string = $amount . $createdAt . $currency . $errorOccurred . $hasParentTransaction . $id . $integrationId . $is3DSecure . $isAuth . $isCapture . $isRefunded . $isStandalonePayment . $isVoided . $orderId . $owner . $pending . $sourceDataPan . $sourceDataSubType . $sourceDataType . $success;

        return hash_hmac('sha512', $string, $hmacSecret);
    }
}