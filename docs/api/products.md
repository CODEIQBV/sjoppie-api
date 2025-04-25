# Product API Documentation

## Create a Product

```http
POST /api/products
```

Create a new product with optional nested data for stock, price, images, variants, tags, and categories.

### Request Body

```json
{
    "name": "Product Name",
    "description": "Product Description",
    "seo_title": "SEO Title",
    "seo_description": "SEO Description",
    "slug": "product-slug",
    "status": "active",
    
    "stock": {
        "available": 100,
        "on_hand": 100,
        "reserved": 0
    },
    
    "price": {
        "amount": 9.99,
        "currency": "EUR"
    },
    
    "images": [
        {
            "url": "https://example.com/image1.jpg",
            "alt": "Product Image 1",
            "order": 1
        },
        {
            "url": "https://example.com/image2.jpg",
            "alt": "Product Image 2",
            "order": 2
        }
    ],
    
    "variants": [
        {
            "title": "Variant 1",
            "option1_name": "Size",
            "option1_value": "Large",
            "option2_name": "Color",
            "option2_value": "Red",
            "sku": "VAR-001",
            "barcode": "123456789",
            "stock": {
                "available": 50,
                "on_hand": 50,
                "reserved": 0
            },
            "price": {
                "amount": 12.99,
                "currency": "EUR"
            }
        }
    ],
    
    "tags": ["tag1", "tag2", "tag3"],
    
    "categories": [1, 2, 3]
}
```

### Response

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
        "slug": "product-slug",
        "status": "active",
        "created_at": "2024-03-21T10:00:00.000000Z",
        "updated_at": "2024-03-21T10:00:00.000000Z",
        "stock": {
            "available": 100,
            "on_hand": 100,
            "reserved": 0
        },
        "prices": [
            {
                "amount": 9.99,
                "currency": "EUR",
                "starts_at": "2024-03-21T10:00:00.000000Z"
            }
        ],
        "images": [
            {
                "url": "https://example.com/image1.jpg",
                "alt": "Product Image 1",
                "order": 1
            },
            {
                "url": "https://example.com/image2.jpg",
                "alt": "Product Image 2",
                "order": 2
            }
        ],
        "variants": [
            {
                "title": "Variant 1",
                "option1_name": "Size",
                "option1_value": "Large",
                "option2_name": "Color",
                "option2_value": "Red",
                "sku": "VAR-001",
                "barcode": "123456789",
                "stock": {
                    "available": 50,
                    "on_hand": 50,
                    "reserved": 0
                },
                "prices": [
                    {
                        "amount": 12.99,
                        "currency": "EUR",
                        "starts_at": "2024-03-21T10:00:00.000000Z"
                    }
                ]
            }
        ],
        "tags": [
            {
                "name": "tag1"
            },
            {
                "name": "tag2"
            },
            {
                "name": "tag3"
            }
        ],
        "categories": [
            {
                "id": 1,
                "name": "Category 1"
            },
            {
                "id": 2,
                "name": "Category 2"
            },
            {
                "id": 3,
                "name": "Category 3"
            }
        ]
    },
    "timestamp": "2024-03-21T10:00:00.000000Z"
}
```

### Notes

- All nested data (stock, price, images, variants, tags, categories) is optional
- When creating variants, you can also specify stock and price for each variant
- Tags can be provided as an array of strings - they will be created if they don't exist
- Categories should be provided as an array of category IDs
- The response includes all created relationships 