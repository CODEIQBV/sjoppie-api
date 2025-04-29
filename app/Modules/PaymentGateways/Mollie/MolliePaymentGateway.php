<?php

namespace App\Modules\PaymentGateways\Mollie;

use App\Models\Payment;
use App\Modules\PaymentGateways\AbstractPaymentGateway;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Laravel\Facades\Mollie;

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
        ];
    }

    /**
     * @throws ApiException
     */
    public function initialize(array $configuration): void
    {
        // Ensure we have the test mode flag
        $configuration['is_test_mode'] = $configuration['is_test_mode'] ?? true;

        // Initialize parent with full configuration
        parent::initialize($configuration);

        // Get the appropriate API key based on test mode
        $apiKey = $this->isTestMode()
            ? $this->getConfigValue('test_api_key')
            : $this->getConfigValue('live_api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('API key is required for Mollie integration');
        }

        Mollie::api()->setApiKey($apiKey);
    }

    public function createPayment(Payment $payment): array
    {
        $molliePayment = Mollie::api()->payments->create([
            "amount" => [
                "currency" => $payment->currency,
                "value" => number_format($payment->amount, 2, '.', '')
            ],
            "description" => $payment->description,
            "redirectUrl" => $payment->redirect_url,
            "webhookUrl" => $payment->webhook_url,
            "metadata" => array_merge(
                $payment->metadata ?? [],
                [
                    'payment_id' => $payment->id,
                    'customer_id' => $payment->customer_id,
                    'payment_gateway_id' => $payment->payment_gateway_id,
                ]
            ),
        ]);

        return [
            'id' => $molliePayment->id,
            'checkout_url' => $molliePayment->getCheckoutUrl(),
            'status' => $molliePayment->status,
            'created_at' => $molliePayment->createdAt,
            'expires_at' => $molliePayment->expiresAt,
            'details' => $molliePayment->details,
        ];
    }

    public function handleWebhook(Payment $payment, array $data): void
    {
        $molliePayment = Mollie::api()->payments->get($data['id']);

        switch ($molliePayment->status) {
            case 'paid':
                $payment->markAsPaid();
                break;
            case 'failed':
                $payment->markAsFailed();
                break;
            case 'canceled':
                $payment->markAsCancelled();
                break;
            case 'expired':
                $payment->markAsExpired();
                break;
        }

        $payment->update([
            'gateway_response' => array_merge(
                $payment->gateway_response ?? [],
                ['webhook' => $data]
            ),
        ]);
    }
}
