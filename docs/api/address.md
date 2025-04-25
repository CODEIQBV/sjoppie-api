# Address API Documentation

## Overview
This document describes the API endpoints for managing customer addresses in the system.

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
        "type": ["The type field is required."],
        "house_number": ["The house number field is required."]
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