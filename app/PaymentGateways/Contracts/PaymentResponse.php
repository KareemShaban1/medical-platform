<?php

namespace App\PaymentGateways\Contracts;

class PaymentResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?string $redirectUrl = null,
        public ?string $transactionId = null,
        public ?array $data = null,
        public ?string $gateway = null
    ) {}

    public static function success(
        string $message,
        ?string $redirectUrl = null,
        ?string $transactionId = null,
        ?array $data = null,
        ?string $gateway = null
    ): self {
        return new self(
            success: true,
            message: $message,
            redirectUrl: $redirectUrl,
            transactionId: $transactionId,
            data: $data,
            gateway: $gateway
        );
    }

    public static function failure(
        string $message,
        ?string $transactionId = null,
        ?array $data = null,
        ?string $gateway = null
    ): self {
        return new self(
            success: false,
            message: $message,
            transactionId: $transactionId,
            data: $data,
            gateway: $gateway
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'redirect_url' => $this->redirectUrl,
            'transaction_id' => $this->transactionId,
            'data' => $this->data,
            'gateway' => $this->gateway,
        ];
    }
}


