<?php

namespace App\PaymentGateways\Gateways;

use App\PaymentGateways\BasePaymentGateway;
use App\PaymentGateways\Contracts\PaymentResponse;

class CodGateway extends BasePaymentGateway
{
    public function getName(): string
    {
        return 'cod';
    }

    public function getDisplayName(): string
    {
        return 'Cash on Delivery';
    }

    public function processPayment(array $data): PaymentResponse
    {
        // COD doesn't require payment processing, just mark as pending
        return PaymentResponse::success(
            message: 'Order placed successfully. Payment will be collected on delivery.',
            transactionId: null,
            data: ['payment_method' => 'cod'],
            gateway: $this->getName()
        );
    }

    public function verifyPayment(array $data): PaymentResponse
    {
        // COD doesn't have webhook verification
        return PaymentResponse::success(
            message: 'COD payment verified',
            gateway: $this->getName()
        );
    }

    public function isEnabled(): bool
    {
        // COD is always enabled
        return true;
    }
}


