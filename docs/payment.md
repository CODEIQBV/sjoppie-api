# Payment System

This document outlines the payment system implementation in the Sjoppie API. The system is designed to be modular, allowing for easy integration of multiple payment gateways.

## Table of Contents
- [Overview](#overview)
- [Database Structure](#database-structure)
- [Payment Gateway Modules](#payment-gateway-modules)
- [API Endpoints](#api-endpoints)
- [Payment Flow](#payment-flow)
- [Webhook Handling](#webhook-handling)
- [Error Handling](#error-handling)
- [Examples](#examples)

## Overview

The payment system consists of several components:
- Payment model and database table
- Payment gateway modules (e.g., Mollie)
- Payment service for business logic
- API endpoints for payment operations
- Webhook handling for payment status updates

## Database Structure

The `payments` table stores all payment information:

```sql
payments
├── id
├── customer_id (FK)
├── payment_gateway_id (FK)
├── gateway_payment_id
├── status
├── amount
├── currency
├── description
├── redirect_url
├── webhook_url
├── metadata (JSON)
├── gateway_response (JSON)
├── paid_at
├── expires_at
├── created_at
├── updated_at
└── deleted_at
```

### Status Values
- `pending`: Initial state when payment is created
- `paid`: Payment successfully completed
- `failed`: Payment failed
- `cancelled`: Payment was cancelled
- `expired`: Payment expired

## Payment Gateway Modules

Payment gateways are implemented as modules in `app/Modules/PaymentGateways/`. Each gateway must implement the `PaymentGatewayInterface`:

```php
interface PaymentGatewayInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getRequiredConfiguration(): array;
    public function validateConfiguration(array $configuration): bool;
    public function initialize(array $configuration): void;
    public function isConfigured(): bool;
    public function createPayment(Payment $payment): array;
    public function handleWebhook(Payment $payment, array $data): void;
}
```

### Available Gateways
- Mollie: `app/Modules/PaymentGateways/Mollie/MolliePaymentGateway.php`

## API Endpoints

### Create Payment
```http
POST /api/payments
```

Request body:
```json
{
    "payment_gateway_id": 1,
    "customer_id": 1,
    "amount": 10.00,
    "currency": "EUR",
    "description": "Order #12345",
    "redirect_url": "https://your-app.com/order/success",
    "metadata": {
        "order_id": "12345"
    }
}
```

Response:
```json
{
    "success": true,
    "status": 201,
    "data": {
        "payment": {
            "id": 1,
            "customer_id": 1,
            "payment_gateway_id": 1,
            "amount": "10.00",
            "currency": "EUR",
            "status": "pending",
            "description": "Order #12345",
            "redirect_url": "https://your-app.com/order/success",
            "webhook_url": "https://api.sjoppie.com/api/payments/webhook/mollie",
            "metadata": {
                "order_id": "12345"
            },
            "gateway_response": {
                "id": "tr_123456789",
                "checkout_url": "https://checkout.mollie.com/payment/...",
                "status": "open",
                "created_at": "2024-04-25T21:52:54+00:00",
                "expires_at": "2024-04-26T21:52:54+00:00"
            }
        },
        "checkout_url": "https://checkout.mollie.com/payment/..."
    },
    "timestamp": "2024-04-25T21:52:54+00:00"
}
```

### Get Payment
```http
GET /api/payments/{id}
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "customer_id": 1,
        "payment_gateway_id": 1,
        "amount": "10.00",
        "currency": "EUR",
        "status": "paid",
        "description": "Order #12345",
        "redirect_url": "https://your-app.com/order/success",
        "webhook_url": "https://api.sjoppie.com/api/payments/webhook/mollie",
        "metadata": {
            "order_id": "12345"
        },
        "gateway_response": {
            "id": "tr_123456789",
            "status": "paid",
            "paid_at": "2024-04-25T21:53:00+00:00"
        },
        "paid_at": "2024-04-25T21:53:00+00:00",
        "created_at": "2024-04-25T21:52:54+00:00",
        "updated_at": "2024-04-25T21:53:00+00:00"
    },
    "timestamp": "2024-04-25T21:53:00+00:00"
}
```

### Webhook Endpoint
```http
POST /api/payments/webhook/{gateway}
```

The webhook endpoint is automatically configured when creating a payment. The gateway parameter in the URL determines which payment gateway module handles the webhook.

## Payment Flow

1. Client creates a payment through the API
2. System creates a payment record and initializes the payment with the selected gateway
3. Client receives the checkout URL and redirects the customer
4. Customer completes the payment on the gateway's checkout page
5. Gateway sends a webhook notification to our API
6. System updates the payment status based on the webhook data

## Webhook Handling

The system automatically handles webhook notifications from payment gateways. Each gateway module implements its own webhook handling logic to:
- Verify the webhook signature (if applicable)
- Update the payment status
- Store additional payment details
- Trigger any necessary business logic

## Error Handling

The API follows a standardized error response format:

```json
{
    "success": false,
    "message": "Error message",
    "status": 500,
    "debug": {
        "message": "Detailed error message",
        "file": "app/Http/Controllers/Api/PaymentController.php",
        "line": 42,
        "trace": "..."
    }
}
```

Debug information is only included when `APP_DEBUG=true`.

## Examples

### Creating a Payment with Mollie

1. First, ensure you have a configured Mollie payment gateway:
```sql
INSERT INTO payment_gateways (name, module_name, is_active, is_test_mode, configuration)
VALUES (
    'Mollie',
    'Mollie',
    true,
    true,
    '{"test_api_key": "test_...", "live_api_key": "live_..."}'
);
```

2. Create a payment:
```http
POST /api/payments
{
    "payment_gateway_id": 1,
    "customer_id": 1,
    "amount": 10.00,
    "description": "Test Payment",
    "redirect_url": "https://your-app.com/payment/success",
    "metadata": {
        "test": true
    }
}
```

3. Redirect the customer to the checkout URL:
```javascript
window.location.href = response.data.checkout_url;
```

4. Handle the webhook notification (automatically processed by the system)

### Checking Payment Status

```http
GET /api/payments/1
```

The response will include the current payment status and any updates from the payment gateway.

## Best Practices

1. Always verify payment status through the API before fulfilling orders
2. Implement proper error handling for failed payments
3. Use test mode when developing
4. Keep payment gateway API keys secure
5. Monitor webhook notifications for failed payments
6. Implement proper logging for payment operations
7. Use the metadata field to store relevant order information
8. Implement proper error handling for webhook notifications 