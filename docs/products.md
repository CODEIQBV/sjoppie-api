# Products API

## Product Management

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
    "success": true,
    "status": 200,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Product Name",
                "description": "Product Description",
                "seo_title": "SEO Title",
                "seo_description": "SEO Description",
                "slug": "product-name",
                "status": "draft",
                "weight": 1.5,
                "length": 10.0,
                "width": 5.0,
                "height": 2.0,
                "barcode": "123456789",
                "sku": "PROD-001",
                "created_at": "2024-04-25T12:00:00Z",
                "updated_at": "2024-04-25T12:00:00Z",
                "variants": [],
                "prices": [],
                "stock": null,
                "images": [],
                "categories": [],
                "tags": []
            }
        ],
        "links": {
            "first": "https://api.sjoppie.nl/v1/products?page=1",
            "last": "https://api.sjoppie.nl/v1/products?page=1",
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "path": "https://api.sjoppie.nl/v1/products",
            "per_page": 10,
            "to": 1,
            "total": 1
        }
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Single Product

```http
GET /products/{slug}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "Product Name",
        "description": "Product Description",
        "seo_title": "SEO Title",
        "seo_description": "SEO Description",
        "slug": "product-name",
        "status": "draft",
        "weight": 1.5,
        "length": 10.0,
        "width": 5.0,
        "height": 2.0,
        "barcode": "123456789",
        "sku": "PROD-001",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z",
        "variants": [],
        "prices": [],
        "stock": null,
        "images": [],
        "categories": [],
        "tags": []
    },
    "timestamp": "2024-04-25T12:00:00Z"
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
    "status": "draft",
    "weight": 1.5,
    "length": 10.0,
    "width": 5.0,
    "height": 2.0,
    "barcode": "123456789",
    "sku": "PROD-001",
    "category_ids": [1, 2],
    "tag_ids": [1, 2]
}
```

#### Response

```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "name": "Product Name",
        "description": "Product Description",
        "seo_title": "SEO Title",
        "seo_description": "SEO Description",
        "slug": "product-name",
        "status": "draft",
        "weight": 1.5,
        "length": 10.0,
        "width": 5.0,
        "height": 2.0,
        "barcode": "123456789",
        "sku": "PROD-001",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
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
    "status": "published",
    "weight": 2.0,
    "category_ids": [1, 3],
    "tag_ids": [2, 4]
}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "Updated Product Name",
        "description": "Product Description",
        "seo_title": "SEO Title",
        "seo_description": "SEO Description",
        "slug": "updated-product-name",
        "status": "published",
        "weight": 2.0,
        "length": 10.0,
        "width": 5.0,
        "height": 2.0,
        "barcode": "123456789",
        "sku": "PROD-001",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Delete Product

```http
DELETE /products/{slug}
```

#### Response

Status: 204 No Content

## Product Variants

### List Variants

```http
GET /products/{product}/variants
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "product_id": 1,
            "title": "Variant Title",
            "option1_name": "Size",
            "option1_value": "Large",
            "option2_name": "Color",
            "option2_value": "Red",
            "option3_name": null,
            "option3_value": null,
            "sku": "PROD-001-L-R",
            "barcode": "123456789-001",
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z",
            "prices": [],
            "stock": null
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Variant

```http
POST /products/{product}/variants
```

#### Request Body

```json
{
    "title": "Variant Title",
    "option1_name": "Size",
    "option1_value": "Large",
    "option2_name": "Color",
    "option2_value": "Red",
    "sku": "PROD-001-L-R",
    "barcode": "123456789-001"
}
```

#### Response

```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "product_id": 1,
        "title": "Variant Title",
        "option1_name": "Size",
        "option1_value": "Large",
        "option2_name": "Color",
        "option2_value": "Red",
        "option3_name": null,
        "option3_value": null,
        "sku": "PROD-001-L-R",
        "barcode": "123456789-001",
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Product Prices

### List Prices

```http
GET /products/{product}/variants/{variant}/prices
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "priceable_type": "App\\Models\\ProductVariant",
            "priceable_id": 1,
            "price": 29.99,
            "compare_at_price": 39.99,
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

### Create Price

```http
POST /products/{product}/variants/{variant}/prices
```

#### Request Body

```json
{
    "price": 29.99,
    "compare_at_price": 39.99,
    "taxable": true,
    "currency": "EUR",
    "starts_at": "2024-04-25T00:00:00Z",
    "ends_at": null
}
```

#### Response

```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "priceable_type": "App\\Models\\ProductVariant",
        "priceable_id": 1,
        "price": 29.99,
        "compare_at_price": 39.99,
        "taxable": true,
        "currency": "EUR",
        "starts_at": "2024-04-25T00:00:00Z",
        "ends_at": null,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Product Stock

### Get Stock

```http
GET /products/{product}/variants/{variant}/stock
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "stockable_type": "App\\Models\\ProductVariant",
        "stockable_id": 1,
        "available": 100,
        "on_hand": 100,
        "reserved": 0,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Stock

```http
PUT /products/{product}/variants/{variant}/stock
```

#### Request Body

```json
{
    "available": 90,
    "on_hand": 100,
    "reserved": 10
}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "stockable_type": "App\\Models\\ProductVariant",
        "stockable_id": 1,
        "available": 90,
        "on_hand": 100,
        "reserved": 10,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

## Product Images

### List Images

```http
GET /products/{product}/images
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "product_id": 1,
            "path": "products/1/image1.jpg",
            "alt_text": "Product Image 1",
            "order": 0,
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z"
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Image

```http
POST /products/{product}/images
```

#### Request Body

```json
{
    "path": "products/1/image1.jpg",
    "alt_text": "Product Image 1",
    "order": 0
}
```

#### Response

```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "product_id": 1,
        "path": "products/1/image1.jpg",
        "alt_text": "Product Image 1",
        "order": 0,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Reorder Images

```http
POST /products/{product}/images/reorder
```

#### Request Body

```json
{
    "image_ids": [2, 1, 3]
}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "message": "Images reordered successfully"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
``` 