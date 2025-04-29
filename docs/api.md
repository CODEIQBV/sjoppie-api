# Sjoppie API Documentation

## Overview

The Sjoppie API provides a comprehensive set of endpoints for managing products, variants, prices, stock, images, categories, and tags. The API follows RESTful principles and uses JSON for request and response bodies.

## Authentication

The API uses a two-factor authentication system:

1. **API Key Authentication**
   All API requests require an API key to be included in the request header:
   ```
   X-API-Key: your-api-key
   ```

2. **User Authentication**
   Most endpoints require user authentication using a Bearer token. To get a token:
   
   1. First, login using the authentication endpoint:
   ```http
   POST /auth/login
   ```
   Request:
   ```json
   {
       "email": "user@example.com",
       "password": "your-password"
   }
   ```
   Response:
   ```json
   {
       "success": true,
       "status": 200,
       "data": {
           "user": {
               "id": 1,
               "name": "John Doe",
               "email": "user@example.com"
           },
           "token": "your-bearer-token"
       },
       "timestamp": "2024-04-25T12:00:00Z"
   }
   ```

   2. Include the received token in subsequent requests:
   ```
   Authorization: Bearer your-bearer-token
   ```

   Note: The login and logout endpoints only require the API key, not the Bearer token.

## Base URL

```
https://api.sjoppie.nl/v1
```

## Response Format

All responses follow a standardized format:

```json
{
    "success": true,
    "status": 200,
    "data": {
        // Response data
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Error Handling

Errors are returned with appropriate HTTP status codes and follow this format:

```json
{
    "success": false,
    "status": 400,
    "message": "Error message",
    "errors": {
        // Validation errors if applicable
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

Common authentication errors:
```json
{
    "success": false,
    "status": 401,
    "message": "Missing authentication token",
    "timestamp": "2024-04-25T12:00:00Z"
}
```
```json
{
    "success": false,
    "status": 401,
    "message": "Invalid or expired token",
    "timestamp": "2024-04-25T12:00:00Z"
}
```
```json
{
    "success": false,
    "status": 401,
    "message": "Invalid API key",
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Table of Contents

1. [Products](products.md)
   - [Product Management](products.md#product-management)
   - [Product Variants](products.md#product-variants)
   - [Product Prices](products.md#product-prices)
   - [Product Stock](products.md#product-stock)
   - [Product Images](products.md#product-images)

2. [Categories](categories.md)
   - [Category Management](categories.md#category-management)
   - [Category Tree](categories.md#category-tree)

3. [Tags](tags.md)
   - [Tag Management](tags.md#tag-management)

## Product Images

### List Product Images
```http
GET /products/{product}/images
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "product_id": 1,
            "path": "https://example.com/image.jpg",
            "alt_text": "Product image",
            "order": 0,
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z"
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Product Image
```http
POST /products/{product}/images
```

Request:
```json
{
    "path": "https://example.com/image.jpg",
    "alt_text": "Product image",
    "order": 0
}
```

### Update Product Image
```http
PUT /products/{product}/images/{image}
```

Request:
```json
{
    "path": "https://example.com/new-image.jpg",
    "alt_text": "Updated product image",
    "order": 1
}
```

### Delete Product Image
```http
DELETE /products/{product}/images/{image}
```

### Reorder Product Images
```http
POST /products/{product}/images/reorder
```

Request:
```json
{
    "image_ids": [1, 2, 3]
}
```

## Product Prices

### List Product Prices
```http
GET /products/{product}/prices
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "price": 99.99,
            "compare_at_price": 129.99,
            "taxable": true,
            "currency": "EUR",
            "starts_at": "2024-04-25T00:00:00Z",
            "ends_at": null,
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z"
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Current Price
```http
GET /products/{product}/prices/current
```

### Create Product Price
```http
POST /products/{product}/prices
```

Request:
```json
{
    "price": 99.99,
    "compare_at_price": 129.99,
    "taxable": true,
    "currency": "EUR",
    "starts_at": "2024-04-25T00:00:00Z",
    "ends_at": null
}
```

### Update Product Price
```http
PUT /products/{product}/prices/{price}
```

Request:
```json
{
    "price": 89.99,
    "compare_at_price": 119.99,
    "taxable": true,
    "currency": "EUR",
    "starts_at": "2024-04-25T00:00:00Z",
    "ends_at": "2024-05-25T00:00:00Z"
}
```

### Delete Product Price
```http
DELETE /products/{product}/prices/{price}
```

## Product Stock

### Get Product Stock
```http
GET /products/{product}/stock
```

Response:
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "available": 100,
        "on_hand": 120,
        "reserved": 20,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Product Stock
```http
PUT /products/{product}/stock
```

Request:
```json
{
    "available": 100,
    "on_hand": 120,
    "reserved": 20
}
```

### Update Available Stock
```http
PUT /products/{product}/stock/available
```

Request:
```json
{
    "quantity": 100
}
```

### Update On Hand Stock
```http
PUT /products/{product}/stock/on-hand
```

Request:
```json
{
    "quantity": 120
}
```

### Update Reserved Stock
```http
PUT /products/{product}/stock/reserved
```

Request:
```json
{
    "quantity": 20
}
```

## Rate Limiting

The API is rate-limited to prevent abuse. The current limits are:

- 100 requests per minute per API key
- 1000 requests per hour per API key

Rate limit headers are included in all responses:

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 99
X-RateLimit-Reset: 1620000000
```

## Versioning

The API is versioned to ensure backward compatibility. The current version is v1.

## Support

For support, please contact:
- Email: support@sjoppie.nl
- Phone: +31 (0)20 123 4567 