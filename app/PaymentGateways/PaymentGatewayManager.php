<?php

namespace App\PaymentGateways;

use App\Enums\PaymentGateway;
use App\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\PaymentGateways\Gateways\CodGateway;
use App\PaymentGateways\Gateways\PaymobGateway;
use Illuminate\Support\Facades\Config;

class PaymentGatewayManager
{
    /**
     * Get payment gateway instance
     */
    public function gateway(string|PaymentGateway $gateway): PaymentGatewayInterface
    {
        $gatewayName = $gateway instanceof PaymentGateway ? $gateway->value : $gateway;

        return match ($gatewayName) {
            'cod' => new CodGateway($this->getGatewayConfig('cod')),
            'paymob' => new PaymobGateway($this->getGatewayConfig('paymob')),
            default => throw new \Exception("Payment gateway '{$gatewayName}' not found"),
        };
    }

    /**
     * Get all available gateways
     */
    public function getAvailableGateways(): array
    {
        $gateways = [];

        foreach (PaymentGateway::cases() as $gateway) {
            $gatewayInstance = $this->gateway($gateway);
            if ($gatewayInstance->isEnabled()) {
                $gateways[] = [
                    'name' => $gateway->value,
                    'display_name' => $gatewayInstance->getDisplayName(),
                ];
            }
        }

        return $gateways;
    }

    /**
     * Get gateway configuration
     */
    protected function getGatewayConfig(string $gateway): array
    {
        return Config::get("payment_gateways.{$gateway}", []);
    }

    /**
     * Check if gateway is enabled
     */
    public function isGatewayEnabled(string|PaymentGateway $gateway): bool
    {
        try {
            return $this->gateway($gateway)->isEnabled();
        } catch (\Exception $e) {
            return false;
        }
    }
}
