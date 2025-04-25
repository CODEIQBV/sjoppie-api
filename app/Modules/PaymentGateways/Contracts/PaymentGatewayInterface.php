<?php

namespace App\Modules\PaymentGateways\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Get the name of the payment gateway
     */
    public function getName(): string;

    /**
     * Get the description of the payment gateway
     */
    public function getDescription(): string;

    /**
     * Get the required configuration fields
     * @return array<string, array{type: string, required: bool, label: string, description: string}>
     */
    public function getRequiredConfiguration(): array;

    /**
     * Validate the configuration
     */
    public function validateConfiguration(array $configuration): bool;

    /**
     * Initialize the payment gateway with configuration
     */
    public function initialize(array $configuration): void;

    /**
     * Check if the payment gateway is properly configured
     */
    public function isConfigured(): bool;

    /**
     * Create a new payment
     * @return array The payment response from the gateway
     */
    public function createPayment(Payment $payment): array;

    /**
     * Handle a webhook notification from the gateway
     */
    public function handleWebhook(Payment $payment, array $data): void;
} 