<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Modules\PaymentGateways\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;

class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayService $paymentGatewayService
    ) {}

    public function createPayment(array $data): Payment
    {
        try {
            $gateway = PaymentGateway::findOrFail($data['payment_gateway_id']);
            $gatewayInstance = $this->paymentGatewayService->getGatewayInstance($gateway);

            if (!$gatewayInstance) {
                throw new \RuntimeException('Payment gateway not found or not properly configured');
            }

            // Generate webhook URL with proper domain
            $webhookUrl = $this->generateWebhookUrl($gateway->module_name);

            $payment = Payment::create([
                'customer_id' => $data['customer_id'],
                'payment_gateway_id' => $data['payment_gateway_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'EUR',
                'description' => $data['description'],
                'redirect_url' => $data['redirect_url'],
                'webhook_url' => $webhookUrl,
                'metadata' => $data['metadata'] ?? null,
                'status' => 'pending',
            ]);

            $gatewayResponse = $gatewayInstance->createPayment($payment);

            $payment->update([
                'gateway_payment_id' => $gatewayResponse['id'],
                'gateway_response' => $gatewayResponse,
            ]);

            return $payment->fresh();
        } catch (Throwable $e) {
            Log::error('Error creating payment: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function generateWebhookUrl(string $gateway): string
    {
        // Use APP_URL from config if set, otherwise fallback to current request
        $baseUrl = config('app.url') ?: URL::current();

        // Remove any trailing slashes
        $baseUrl = rtrim($baseUrl, '/');

        // Generate the webhook URL with v1 prefix
        return sprintf(
            '%s/v1/payments/webhook/%s',
            $baseUrl,
            $gateway
        );
    }

    public function handleWebhook(PaymentGateway $gateway, array $data): void
    {
        try {
            // Get the payment first to ensure it exists
            $payment = Payment::where('gateway_payment_id', $data['id'])
                ->where('payment_gateway_id', $gateway->id)
                ->firstOrFail();

            // Get a fresh instance of the gateway with proper configuration
            $gatewayInstance = $this->paymentGatewayService->getGatewayInstance($gateway);
            
            if (!$gatewayInstance) {
                throw new \RuntimeException('Payment gateway not found or not properly configured');
            }

            $gatewayInstance->handleWebhook($payment, $data);
        } catch (Throwable $e) {
            Log::error('Error handling webhook: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $gateway->id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getPayment(string $id): ?Payment
    {
        return Payment::find($id);
    }
}
