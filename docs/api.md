# API Documentation

## Authentication

All API requests must include an `X-API-Key` header with a valid API key.

```http
X-API-Key: your-api-key-here
```

## Base URL

```
https://your-api-domain.com/api/v1
```

## Products

### List Products

```http
GET /products
```

#### Query Parameters

| Parameter | Type   | Description                    | Default |
|-----------|--------|--------------------------------|---------|
| per_page  | int    | Number of items per page       | 10      |
| page      | int    | Page number                    | 1       |

#### Response

```json
{
    "data": [
        {
            "id": 1,
            "name": "Product Name",
            "description": "Product Description",
            "seo_title": "SEO Title",
            "seo_description": "SEO Description",
            "slug": "product-name",
            "status": "active",
            "created_at": "2024-03-21T12:00:00.000000Z",
            "updated_at": "2024-03-21T12:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://api.example.com/products?page=1",
        "last": "http://api.example.com/products?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://api.example.com/products",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

### Get Single Product

```http
GET /products/{slug}
```

#### Response

```json
{
    "id": 1,
    "name": "Product Name",
    "description": "Product Description",
    "seo_title": "SEO Title",
    "seo_description": "SEO Description",
    "slug": "product-name",
    "status": "active",
    "created_at": "2024-03-21T12:00:00.000000Z",
    "updated_at": "2024-03-21T12:00:00.000000Z"
}
```

### Create Product

```http
POST /products
```

#### Request Body

```json
{
    "name": "Product Name",
    "description": "Product Description",
    "seo_title": "SEO Title",
    "seo_description": "SEO Description",
    "slug": "product-name",
    "status": "active"
}
```

#### Response

```json
{
    "id": 1,
    "name": "Product Name",
    "description": "Product Description",
    "seo_title": "SEO Title",
    "seo_description": "SEO Description",
    "slug": "product-name",
    "status": "active",
    "created_at": "2024-03-21T12:00:00.000000Z",
    "updated_at": "2024-03-21T12:00:00.000000Z"
}
```

### Update Product

```http
PUT /products/{slug}
```

#### Request Body

```json
{
    "name": "Updated Product Name",
    "status": "concept"
}
```

#### Response

```json
{
    "id": 1,
    "name": "Updated Product Name",
    "description": "Product Description",
    "seo_title": "SEO Title",
    "seo_description": "SEO Description",
    "slug": "updated-product-name",
    "status": "concept",
    "created_at": "2024-03-21T12:00:00.000000Z",
    "updated_at": "2024-03-21T12:00:00.000000Z"
}
```

### Delete Product

```http
DELETE /products/{slug}
```

#### Response

Status: 204 No Content

## Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "errors": {
        "name": [
            "The name field is required."
        ],
        "status": [
            "The selected status is invalid."
        ]
    }
}
```

### Not Found Error (404)

```json
{
    "success": false,
    "message": "Product not found"
}
```

### Unauthorized Error (401)

```json
{
    "success": false,
    "message": "Invalid API key"
}
``` 