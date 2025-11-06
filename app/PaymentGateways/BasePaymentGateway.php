<?php

namespace App\PaymentGateways;

use App\PaymentGateways\Contracts\PaymentGatewayInterface;
use App\PaymentGateways\Contracts\PaymentResponse;

abstract class BasePaymentGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get gateway configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get a specific config value
     */
    protected function getConfigValue(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Check if gateway is enabled
     */
    public function isEnabled(): bool
    {
        return $this->getConfigValue('enabled', false);
    }

    /**
     * Validate required configuration
     */
    protected function validateConfig(array $requiredKeys): void
    {
        foreach ($requiredKeys as $key) {
            if (!isset($this->config[$key]) || empty($this->config[$key])) {
                throw new \Exception("Missing required configuration for {$this->getName()}: {$key}");
            }
        }
    }
}


