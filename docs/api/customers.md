# Customer API Documentation

## Overview
This document describes the API endpoints for managing customers and their addresses in the system.

## Customer Endpoints

### List Customers
```http
GET /api/customers
```

Query Parameters:
- `per_page` (optional): Number of items per page (default: 15)
- `search` (optional): Search term to filter customers

Response:
```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": "uuid",
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "phone_number": "+1234567890",
            "company": "ACME Inc",
            "vat_number": "NL123456789B01",
            "notes": "Important customer",
            "is_active": true,
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z",
            "addresses": [
                {
                    "id": "uuid",
                    "type": "shipping",
                    "street": "Main Street",
                    "house_number": "123",
                    "postal_code": "1234 AB",
                    "city": "Amsterdam",
                    "country": "NL",
                    "is_default": true,
                    "additional_info": "Apartment 4B",
                    "created_at": "2024-04-25T12:00:00Z",
                    "updated_at": "2024-04-25T12:00:00Z"
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Customer
```http
POST /api/customers
```

Request Body:
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890",
    "company": "ACME Inc",
    "vat_number": "NL123456789B01",
    "notes": "Important customer",
    "address": {
        "type": "shipping",
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "NL",
        "is_default": true,
        "additional_info": "Apartment 4B"
    }
}
```

Response:
```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": "uuid",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone_number": "+1234567890",
        "company": "ACME Inc",
        "vat_number": "NL123456789B01",
        "notes": "Important customer",
        "is_active": true,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z",
        "addresses": [
            {
                "id": "uuid",
                "type": "shipping",
                "street": "Main Street",
                "house_number": "123",
                "postal_code": "1234 AB",
                "city": "Amsterdam",
                "country": "NL",
                "is_default": true,
                "additional_info": "Apartment 4B",
                "created_at": "2024-04-25T12:00:00Z",
                "updated_at": "2024-04-25T12:00:00Z"
            }
        ]
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Customer
```http
GET /api/customers/{id}
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": "uuid",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone_number": "+1234567890",
        "company": "ACME Inc",
        "vat_number": "NL123456789B01",
        "notes": "Important customer",
        "is_active": true,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z",
        "addresses": [
            {
                "id": "uuid",
                "type": "shipping",
                "street": "Main Street",
                "house_number": "123",
                "postal_code": "1234 AB",
                "city": "Amsterdam",
                "country": "NL",
                "is_default": true,
                "additional_info": "Apartment 4B",
                "created_at": "2024-04-25T12:00:00Z",
                "updated_at": "2024-04-25T12:00:00Z"
            }
        ]
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Customer
```http
PUT /api/customers/{id}
```

Request Body:
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890",
    "company": "ACME Inc",
    "vat_number": "NL123456789B01",
    "notes": "Important customer"
}
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": "uuid",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone_number": "+1234567890",
        "company": "ACME Inc",
        "vat_number": "NL123456789B01",
        "notes": "Important customer",
        "is_active": true,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z",
        "addresses": [
            {
                "id": "uuid",
                "type": "shipping",
                "street": "Main Street",
                "house_number": "123",
                "postal_code": "1234 AB",
                "city": "Amsterdam",
                "country": "NL",
                "is_default": true,
                "additional_info": "Apartment 4B",
                "created_at": "2024-04-25T12:00:00Z",
                "updated_at": "2024-04-25T12:00:00Z"
            }
        ]
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Delete Customer
```http
DELETE /api/customers/{id}
```

Response:
```json
{
    "success": true,
    "status": 204,
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Address Endpoints

### List Customer Addresses
```http
GET /api/customers/{customerId}/addresses
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": "uuid",
            "type": "shipping",
            "street": "Main Street",
            "house_number": "123",
            "postal_code": "1234 AB",
            "city": "Amsterdam",
            "country": "NL",
            "is_default": true,
            "additional_info": "Apartment 4B",
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z"
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Address
```http
POST /api/customers/{customerId}/addresses
```

Request Body:
```json
{
    "type": "shipping",
    "street": "Main Street",
    "house_number": "123",
    "postal_code": "1234 AB",
    "city": "Amsterdam",
    "country": "NL",
    "is_default": true,
    "additional_info": "Apartment 4B"
}
```

Response:
```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": "uuid",
        "type": "shipping",
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "NL",
        "is_default": true,
        "additional_info": "Apartment 4B",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Address
```http
GET /api/customers/{customerId}/addresses/{id}
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": "uuid",
        "type": "shipping",
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "NL",
        "is_default": true,
        "additional_info": "Apartment 4B",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Address
```http
PUT /api/customers/{customerId}/addresses/{id}
```

Request Body:
```json
{
    "type": "shipping",
    "street": "Main Street",
    "house_number": "123",
    "postal_code": "1234 AB",
    "city": "Amsterdam",
    "country": "NL",
    "is_default": true,
    "additional_info": "Apartment 4B"
}
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": "uuid",
        "type": "shipping",
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "NL",
        "is_default": true,
        "additional_info": "Apartment 4B",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Delete Address
```http
DELETE /api/customers/{customerId}/addresses/{id}
```

Response:
```json
{
    "success": true,
    "status": 204,
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Default Address
```http
GET /api/customers/{customerId}/addresses/default/{type}
```

Where `type` can be either `billing` or `shipping`.

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": "uuid",
        "type": "shipping",
        "street": "Main Street",
        "house_number": "123",
        "postal_code": "1234 AB",
        "city": "Amsterdam",
        "country": "NL",
        "is_default": true,
        "additional_info": "Apartment 4B",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Error Responses

All endpoints may return the following error responses:

- `400 Bad Request`: Invalid input data
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error
- `500 Internal Server Error`: Server error

Example validation error response:
```json
{
    "success": false,
    "status": 422,
    "errors": {
        "email": ["The email has already been taken."],
        "address.house_number": ["The house number field is required when address is present."]
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

Example server error response (in debug mode):
```json
{
    "success": false,
    "status": 500,
    "message": "Database connection failed",
    "debug": {
        "message": "SQLSTATE[HY000] [2002] Connection refused",
        "file": "/app/vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php",
        "line": 70,
        "trace": "..."
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
``` 
