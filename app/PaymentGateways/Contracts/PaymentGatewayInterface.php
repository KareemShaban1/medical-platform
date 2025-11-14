<?php

namespace App\PaymentGateways\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Get the name of the payment gateway
     */
    public function getName(): string;

    /**
     * Get the display name of the payment gateway
     */
    public function getDisplayName(): string;

    /**
     * Process payment and return payment response
     * 
     * @param array $data Payment data (amount, order_id, customer_info, etc.)
     * @return PaymentResponse
     */
    public function processPayment(array $data): PaymentResponse;

    /**
     * Verify payment callback/webhook
     * 
     * @param array $data Callback data from gateway
     * @return PaymentResponse
     */
    public function verifyPayment(array $data): PaymentResponse;

    /**
     * Check if gateway is enabled
     */
    public function isEnabled(): bool;

    /**
     * Get gateway configuration
     */
    public function getConfig(): array;
}


