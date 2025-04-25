<?php

namespace App\Modules\PaymentGateways;

use App\Modules\PaymentGateways\Contracts\PaymentGatewayInterface;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $configuration = [];
    protected bool $isTestMode = false;

    public function initialize(array $configuration): void
    {
        $this->configuration = $configuration;
        $this->isTestMode = $configuration['is_test_mode'] ?? false;
    }

    public function validateConfiguration(array $configuration): bool
    {
        $required = $this->getRequiredConfiguration();
        
        foreach ($required as $key => $field) {
            if ($field['required'] && !isset($configuration[$key])) {
                return false;
            }
        }

        return true;
    }

    public function isConfigured(): bool
    {
        return $this->validateConfiguration($this->configuration);
    }

    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }

    protected function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->configuration[$key] ?? $default;
    }
} 