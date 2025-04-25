# Sjoppie API Documentation

## Overview

The Sjoppie API provides a comprehensive set of endpoints for managing products, variants, prices, stock, images, categories, and tags. The API follows RESTful principles and uses JSON for request and response bodies.

## Authentication

All API requests require an API key to be included in the request header:

```
X-API-Key: your-api-key
```

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