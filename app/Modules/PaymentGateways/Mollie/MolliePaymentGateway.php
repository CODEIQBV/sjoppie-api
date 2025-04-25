<?php

namespace App\Modules\PaymentGateways\Mollie;

use App\Modules\PaymentGateways\AbstractPaymentGateway;

class MolliePaymentGateway extends AbstractPaymentGateway
{
    public function getName(): string
    {
        return 'Mollie';
    }

    public function getDescription(): string
    {
        return 'Mollie Payment Gateway Integration';
    }

    public function getRequiredConfiguration(): array
    {
        return [
            'test_api_key' => [
                'type' => 'string',
                'required' => true,
                'label' => 'Test API Key',
                'description' => 'Your Mollie test API key',
            ],
            'live_api_key' => [
                'type' => 'string',
                'required' => true,
                'label' => 'Live API Key',
                'description' => 'Your Mollie live API key',
            ],
            'customer_id' => [
                'type' => 'string',
                'required' => true,
                'label' => 'Customer ID',
                'description' => 'Your Mollie customer ID',
            ],
        ];
    }

    public function initialize(array $configuration): void
    {
        parent::initialize($configuration);
        // Here we would initialize the Mollie client
        // $this->mollie = new \Mollie\Api\MollieApiClient();
        // $this->mollie->setApiKey($this->getConfigValue('test_api_key'));
    }
} 