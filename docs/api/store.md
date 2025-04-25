# Store API Documentation

## Get Store Details

Get the current store details.

```http
GET /api/store
```

### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "My Store",
        "support_email": "support@example.com",
        "vat_number": null,
        "is_active": true,
        "phone": null,
        "address": null,
        "city": null,
        "postal_code": null,
        "country": null,
        "currency": "EUR",
        "timezone": "UTC",
        "settings": {
            "maintenance_mode": false,
            "allow_guest_checkout": true,
            "min_order_amount": 0,
            "tax_included": false
        },
        "created_at": "2024-04-25T15:44:10.000000Z",
        "updated_at": "2024-04-25T15:44:10.000000Z",
        "deleted_at": null
    },
    "timestamp": "2024-04-25T15:44:10.000000Z"
}
```

## Update Store Details

Update the store details.

```http
PUT /api/store
```

### Request Body

| Field | Type | Description | Required | Default |
|-------|------|-------------|----------|---------|
| name | string | Store name | No | - |
| support_email | string | Support email address | No | - |
| vat_number | string | VAT number | No | null |
| is_active | boolean | Whether the store is active | No | true |
| phone | string | Store phone number | No | null |
| address | string | Store address | No | null |
| city | string | Store city | No | null |
| postal_code | string | Store postal code | No | null |
| country | string | Store country | No | null |
| currency | string | Store currency (3 characters) | No | EUR |
| timezone | string | Store timezone | No | UTC |
| settings | object | Store settings | No | - |

### Settings Object

| Field | Type | Description | Default |
|-------|------|-------------|---------|
| maintenance_mode | boolean | Whether the store is in maintenance mode | false |
| allow_guest_checkout | boolean | Whether guests can checkout without account | true |
| min_order_amount | number | Minimum order amount in cents | 0 |
| tax_included | boolean | Whether prices include tax | false |

### Example Request

```json
{
    "name": "Updated Store Name",
    "support_email": "new-support@example.com",
    "vat_number": "NL123456789B01",
    "is_active": true,
    "phone": "+31 123 456 789",
    "address": "Store Street 1",
    "city": "Amsterdam",
    "postal_code": "1234 AB",
    "country": "Netherlands",
    "currency": "EUR",
    "timezone": "Europe/Amsterdam",
    "settings": {
        "maintenance_mode": true,
        "allow_guest_checkout": false,
        "min_order_amount": 1000,
        "tax_included": true
    }
}
```

### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "Updated Store Name",
        "support_email": "new-support@example.com",
        "vat_number": "NL123456789B01",
        "is_active": true,
        "phone": "+31 123 456 789",
        "address": "Store Street 1",
        "city": "Amsterdam",
        "postal_code": "1234 AB",
        "country": "Netherlands",
        "currency": "EUR",
        "timezone": "Europe/Amsterdam",
        "settings": {
            "maintenance_mode": true,
            "allow_guest_checkout": false,
            "min_order_amount": 1000,
            "tax_included": true
        },
        "created_at": "2024-04-25T15:44:10.000000Z",
        "updated_at": "2024-04-25T15:44:10.000000Z",
        "deleted_at": null
    },
    "message": "Store details updated successfully.",
    "timestamp": "2024-04-25T15:44:10.000000Z"
}
``` 