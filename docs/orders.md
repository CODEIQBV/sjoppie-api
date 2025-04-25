# Orders API Documentation

This document outlines the API endpoints and functionality for managing orders in the Sjoppie API.

## Table of Contents
- [Overview](#overview)
- [Models](#models)
- [Endpoints](#endpoints)
- [Request/Response Examples](#requestresponse-examples)

## Overview

The Orders API allows you to create and manage orders in the system. Each order can contain multiple order lines, and optionally create a payment for the order.

### Key Features
- Create orders with multiple products
- Support for line-level and order-level discounts
- Automatic tax calculation based on store settings
- Optional payment creation during order creation
- Address data preservation for historical accuracy
- Comprehensive order status tracking
- Flexible address handling (existing or new addresses)
- Shipping address defaults to billing address

## Models

### Order
```php
class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'billing_address_id',
        'shipping_address_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'billing_address_data',
        'shipping_address_data',
        'notes',
    ];
}
```

### OrderLine
```php
class OrderLine extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total_price',
        'product_data',
    ];
}
```

## Endpoints

### Create Order
```
POST /api/orders
```

Creates a new order with the specified products and optional payment. You can either use existing addresses or create new ones during order creation. By default, the shipping address will be the same as the billing address.

#### Request Body
```json
{
    "customer_id": "uuid",
    "billing_address_id": "uuid", // Optional if billing_address is provided
    "shipping_address_id": "uuid", // Optional if different_shipping is true and shipping_address is provided
    "different_shipping": true, // Optional, if true, requires shipping_address
    "billing_address": { // Optional if billing_address_id is provided
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "Netherlands",
        "additional_info": "Optional additional information"
    },
    "shipping_address": { // Required if different_shipping is true
        "street": "Shipping Street",
        "house_number": "456",
        "postal_code": "5678 CD",
        "city": "Rotterdam",
        "country": "Netherlands",
        "additional_info": "Optional additional information"
    },
    "lines": [
        {
            "product_id": "uuid",
            "quantity": 1,
            "unit_price": 10.00,
            "discount_amount": 0.00
        }
    ],
    "discount_amount": 0.00,
    "notes": "Optional order notes",
    "create_payment": true,
    "payment_gateway_id": "uuid",
    "redirect_url": "https://example.com/return"
}
```

#### Response
```json
{
    "id": "uuid",
    "order_number": "O10001",
    "customer_id": "uuid",
    "status": "open",
    "subtotal": 10.00,
    "tax_amount": 2.10,
    "discount_amount": 0.00,
    "total_amount": 12.10,
    "currency": "EUR",
    "lines": [
        {
            "id": "uuid",
            "product_id": "uuid",
            "quantity": 1,
            "unit_price": 10.00,
            "discount_amount": 0.00,
            "tax_amount": 2.10,
            "total_price": 12.10
        }
    ],
    "payment": {
        "id": "uuid",
        "status": "pending",
        "payment_url": "https://payment-provider.com/checkout/..."
    }
}
```

### Get Order
```
GET /api/orders/{id}
```

Retrieves a specific order by ID.

#### Response
```json
{
    "id": "uuid",
    "order_number": "O10001",
    "customer_id": "uuid",
    "status": "open",
    "subtotal": 10.00,
    "tax_amount": 2.10,
    "discount_amount": 0.00,
    "total_amount": 12.10,
    "currency": "EUR",
    "lines": [
        {
            "id": "uuid",
            "product_id": "uuid",
            "quantity": 1,
            "unit_price": 10.00,
            "discount_amount": 0.00,
            "tax_amount": 2.10,
            "total_price": 12.10
        }
    ],
    "payment": {
        "id": "uuid",
        "status": "pending"
    }
}
```

### Update Order Status
```
PUT /api/orders/{id}/status
```

Updates the status of an order.

#### Request Body
```json
{
    "status": "shipped"
}
```

#### Response
```json
{
    "success": true,
    "message": "Order status updated successfully"
}
```

### List Orders
```
GET /api/orders
```

Retrieves a paginated list of all orders.

#### Query Parameters
- `per_page` (optional): Number of items per page (default: 10)

#### Response
```json
{
    "data": [
        {
            "id": "uuid",
            "order_number": "O10001",
            "customer_id": "uuid",
            "status": "open",
            "subtotal": 10.00,
            "tax_amount": 2.10,
            "discount_amount": 0.00,
            "total_amount": 12.10,
            "currency": "EUR",
            "billing_address": {
                "street": "Main Street",
                "house_number": "123",
                "postal_code": "1234 AB",
                "city": "Amsterdam",
                "country": "Netherlands"
            },
            "shipping_address": {
                "street": "Main Street",
                "house_number": "123",
                "postal_code": "1234 AB",
                "city": "Amsterdam",
                "country": "Netherlands"
            },
            "lines": [
                {
                    "id": "uuid",
                    "product_id": "uuid",
                    "quantity": 1,
                    "unit_price": 10.00,
                    "discount_amount": 0.00,
                    "tax_amount": 2.10,
                    "total_price": 12.10
                }
            ],
            "payment": {
                "id": "uuid",
                "status": "pending"
            },
            "created_at": "2024-04-25T12:00:00.000000Z",
            "updated_at": "2024-04-25T12:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    },
    "links": {
        "first": "http://api.example.com/orders?page=1",
        "last": "http://api.example.com/orders?page=1",
        "prev": null,
        "next": null
    }
}
```

## Order Statuses

The following order statuses are supported:
- `open`: Order has been created but not yet processed
- `processing`: Order is being prepared for shipping
- `shipped`: Order has been shipped to the customer
- `delivered`: Order has been delivered to the customer
- `cancelled`: Order has been cancelled
- `refunded`: Order has been refunded

## Error Handling

The API uses standard HTTP status codes and returns error messages in the following format:

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Error message"]
    }
}
```

Common error codes:
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 500: Internal Server Error

## Notes

- All monetary values are in the store's currency
- Tax calculation is based on store settings
- Address data is preserved at the time of order creation
- Payment creation is optional during order creation
- Order lines can have individual discounts
- The entire order can have a discount applied
- Addresses can be provided as IDs or as new address data
- Shipping address defaults to billing address unless `different_shipping` is true