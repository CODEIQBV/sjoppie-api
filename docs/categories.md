# Categories API

## Category Management

### List Categories

```http
GET /categories
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
                "name": "Category Name",
                "description": "Category Description",
                "slug": "category-name",
                "order": 0,
                "parent_id": null,
                "created_at": "2024-04-25T12:00:00Z",
                "updated_at": "2024-04-25T12:00:00Z",
                "children": [],
                "products": [],
                "tags": []
            }
        ],
        "links": {
            "first": "https://api.sjoppie.nl/v1/categories?page=1",
            "last": "https://api.sjoppie.nl/v1/categories?page=1",
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "path": "https://api.sjoppie.nl/v1/categories",
            "per_page": 10,
            "to": 1,
            "total": 1
        }
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Get Single Category

```http
GET /categories/{slug}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "Category Name",
        "description": "Category Description",
        "slug": "category-name",
        "order": 0,
        "parent_id": null,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z",
        "parent": null,
        "children": [],
        "products": [],
        "tags": []
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Create Category

```http
POST /categories
```

#### Request Body

```json
{
    "name": "Category Name",
    "description": "Category Description",
    "slug": "category-name",
    "order": 0,
    "parent_id": null
}
```

#### Response

```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "name": "Category Name",
        "description": "Category Description",
        "slug": "category-name",
        "order": 0,
        "parent_id": null,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Update Category

```http
PUT /categories/{slug}
```

#### Request Body

```json
{
    "name": "Updated Category Name",
    "description": "Updated Category Description",
    "order": 1,
    "parent_id": 2
}
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "Updated Category Name",
        "description": "Updated Category Description",
        "slug": "updated-category-name",
        "order": 1,
        "parent_id": 2,
        "created_at": "2024-04-25T12:00:00Z",
        "updated_at": "2024-04-25T12:00:00Z"
    },
    "timestamp": "2024-04-25T12:00:00Z"
}
```

### Delete Category

```http
DELETE /categories/{slug}
```

#### Response

Status: 204 No Content

### Get Category Tree

```http
GET /categories/tree
```

#### Response

```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "name": "Parent Category",
            "description": "Parent Category Description",
            "slug": "parent-category",
            "order": 0,
            "parent_id": null,
            "created_at": "2024-04-25T12:00:00Z",
            "updated_at": "2024-04-25T12:00:00Z",
            "children": [
                {
                    "id": 2,
                    "name": "Child Category",
                    "description": "Child Category Description",
                    "slug": "child-category",
                    "order": 0,
                    "parent_id": 1,
                    "created_at": "2024-04-25T12:00:00Z",
                    "updated_at": "2024-04-25T12:00:00Z",
                    "children": []
                }
            ]
        }
    ],
    "timestamp": "2024-04-25T12:00:00Z"
}
``` 