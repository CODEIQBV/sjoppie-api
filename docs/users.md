# User Management API

This document outlines the API endpoints for managing users and authentication in the Sjoppie API.

## Authentication

### Login
Authenticate a user and receive an API token.

**Endpoint:** `POST /api/auth/login`

**Headers:**
- `X-API-Key: {api_key}`

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Logout
Logout the current user.

**Endpoint:** `POST /api/auth/logout`

**Headers:**
- `X-API-Key: {api_key}`

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "message": "Successfully logged out"
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

## User Management

All user management endpoints require API key authentication.

### Get All Users
Retrieve a list of all users.

**Endpoint:** `GET /api/users`

**Headers:**
- `X-API-Key: {api_key}`

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Get User
Retrieve a specific user by ID.

**Endpoint:** `GET /api/users/{id}`

**Headers:**
- `X-API-Key: {api_key}`

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Create User
Create a new user.

**Endpoint:** `POST /api/users`

**Headers:**
- `X-API-Key: {api_key}`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "status": 201,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Update User
Update an existing user.

**Endpoint:** `PUT /api/users/{id}`

**Headers:**
- `X-API-Key: {api_key}`

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "updated@example.com",
    "password": "newpassword123"
}
```

**Response:**
```json
{
    "success": true,
    "status": 200,
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        "email": "updated@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Delete User
Delete a user.

**Endpoint:** `DELETE /api/users/{id}`

**Headers:**
- `X-API-Key: {api_key}`

**Response:**
```json
{
    "success": true,
    "status": 204,
    "data": null,
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

## Error Responses

### Invalid Credentials
```json
{
    "success": false,
    "status": 401,
    "message": "Invalid credentials",
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### User Not Found
```json
{
    "success": false,
    "status": 404,
    "message": "User not found",
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### Validation Error
```json
{
    "success": false,
    "status": 422,
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email has already been taken."
        ]
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

## Notes

1. All endpoints require authentication using the X-API-Key header.
2. Passwords are automatically hashed before storage.
3. The API key should be included in the X-API-Key header for all endpoints.
4. User management endpoints are protected and require API key authentication.
5. The API follows RESTful conventions for user management.
6. All responses are wrapped in the standard API response format. 