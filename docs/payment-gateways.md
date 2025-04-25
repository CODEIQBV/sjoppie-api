# Payment Gateways

This document outlines the payment gateway system implementation in the Sjoppie API.

## Overview

The payment gateway system is designed to be modular and extensible, allowing for easy integration of multiple payment providers. The system currently supports Mollie as a payment gateway, with the architecture in place to add more providers in the future.

## Directory Structure

```
app/
├── Modules/
│   └── PaymentGateways/
│       ├── Contracts/
│       │   └── PaymentGatewayInterface.php
│       ├── AbstractPaymentGateway.php
│       └── Mollie/
│           └── MolliePaymentGateway.php
├── Models/
│   └── PaymentGateway.php
├── Services/
│   └── PaymentGatewayService.php
└── Http/
    └── Controllers/
        └── Api/
            └── PaymentGatewayController.php
```

## Database Structure

The `payment_gateways` table contains the following fields:

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Display name of the payment gateway |
| module_name | string | Name of the module implementing the gateway |
| is_active | boolean | Whether the gateway is active |
| is_test_mode | boolean | Whether the gateway is in test mode |
| configuration | json | Gateway-specific configuration |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |
| deleted_at | timestamp | Soft delete timestamp |

## API Endpoints

### List Available Modules
```
GET /api/payment-gateways/modules
```

Returns a list of available payment gateway modules that can be configured.

Response:
```json
[
    {
        "name": "Mollie",
        "module_name": "Mollie",
        "description": "Mollie Payment Gateway Integration",
        "configuration": {
            "test_api_key": {
                "type": "string",
                "required": true,
                "label": "Test API Key",
                "description": "Your Mollie test API key"
            },
            "live_api_key": {
                "type": "string",
                "required": true,
                "label": "Live API Key",
                "description": "Your Mollie live API key"
            },
            "customer_id": {
                "type": "string",
                "required": true,
                "label": "Customer ID",
                "description": "Your Mollie customer ID"
            }
        }
    }
]
```

### List Configured Gateways
```
GET /api/payment-gateways
```

Returns a list of all configured payment gateways.

Response:
```json
[
    {
        "id": 1,
        "name": "Mollie Gateway",
        "module_name": "Mollie",
        "is_active": true,
        "is_test_mode": true,
        "configuration": {
            "test_api_key": "test_...",
            "live_api_key": "live_...",
            "customer_id": "cst_..."
        },
        "created_at": "2024-04-25T21:34:53.000000Z",
        "updated_at": "2024-04-25T21:34:53.000000Z"
    }
]
```

### Create Gateway
```
POST /api/payment-gateways
```

Creates a new payment gateway configuration.

Request:
```json
{
    "name": "Mollie Gateway",
    "module_name": "Mollie",
    "is_active": true,
    "is_test_mode": true,
    "configuration": {
        "test_api_key": "test_...",
        "live_api_key": "live_...",
        "customer_id": "cst_..."
    }
}
```

### Update Gateway
```
PUT /api/payment-gateways/{id}
```

Updates an existing payment gateway configuration.

Request:
```json
{
    "name": "Updated Mollie Gateway",
    "is_active": false,
    "is_test_mode": false,
    "configuration": {
        "test_api_key": "new_test_...",
        "live_api_key": "new_live_...",
        "customer_id": "new_cst_..."
    }
}
```

### Delete Gateway
```
DELETE /api/payment-gateways/{id}
```

Deletes a payment gateway configuration.

## Module Development

To add a new payment gateway module:

1. Create a new directory under `app/Modules/PaymentGateways/` with the module name
2. Create a class that extends `AbstractPaymentGateway`
3. Implement the required methods from `PaymentGatewayInterface`

Example module structure:
```php
namespace App\Modules\PaymentGateways\NewProvider;

use App\Modules\PaymentGateways\AbstractPaymentGateway;

class NewProviderPaymentGateway extends AbstractPaymentGateway
{
    public function getName(): string
    {
        return 'New Provider';
    }

    public function getDescription(): string
    {
        return 'New Provider Payment Gateway Integration';
    }

    public function getRequiredConfiguration(): array
    {
        return [
            'api_key' => [
                'type' => 'string',
                'required' => true,
                'label' => 'API Key',
                'description' => 'Your API key'
            ]
        ];
    }

    public function initialize(array $configuration): void
    {
        parent::initialize($configuration);
        // Initialize the provider's client
    }
}
```

## Best Practices

1. **Configuration**
   - Always validate configuration before saving
   - Use appropriate field types (string, boolean, etc.)
   - Provide clear labels and descriptions
   - Mark required fields appropriately

2. **Security**
   - Never log sensitive configuration data
   - Use environment variables for sensitive data
   - Validate all input data
   - Use proper encryption for sensitive data

3. **Error Handling**
   - Implement proper error handling
   - Provide clear error messages
   - Log errors appropriately
   - Handle API failures gracefully

4. **Testing**
   - Test both test and live modes
   - Mock external API calls
   - Test error scenarios
   - Validate configuration handling

## Future Enhancements

1. **Additional Features**
   - Payment method management
   - Transaction history
   - Webhook handling
   - Refund processing

2. **New Providers**
   - Stripe
   - PayPal
   - Adyen
   - Custom providers

3. **Improvements**
   - Better error handling
   - More detailed logging
   - Enhanced security measures
   - Performance optimizations 