# API Documentation

## Base URL

```
http://localhost:8000/api
```

## Authentication

API menggunakan Laravel Sanctum untuk authentication. Setiap request (kecuali login/register) harus menyertakan Bearer token di header:

```
Authorization: Bearer {token}
```

## Response Format

Semua API response menggunakan format JSON yang konsisten:

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Authentication Endpoints

### POST /api/auth/login

Login user dan mendapatkan access token.

**Request Body:**

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "uuid",
      "name": "Admin User",
      "email": "admin@example.com",
      "roles": ["Super Admin"]
    },
    "token": "sanctum_token_here"
  }
}
```

### POST /api/auth/logout

Logout user dan revoke token.

**Headers:** Authorization: Bearer {token}

**Response:**

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### POST /api/auth/forgot-password

Kirim reset password link ke email.

**Request Body:**

```json
{
  "email": "user@example.com"
}
```

### POST /api/auth/reset-password

Reset password dengan token.

**Request Body:**

```json
{
  "email": "user@example.com",
  "token": "reset_token",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

## User Management Endpoints

### GET /api/users

Mendapatkan daftar users dengan pagination.

**Headers:** Authorization: Bearer {token}

**Query Parameters:**

- `page` (int): Page number
- `per_page` (int): Items per page (default: 15)
- `search` (string): Search query
- `role` (string): Filter by role

**Response:**

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": "uuid",
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["User"],
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "current_page": 1,
    "total": 100,
    "per_page": 15
  }
}
```

### GET /api/users/{id}

Mendapatkan detail user berdasarkan ID.

**Headers:** Authorization: Bearer {token}

**Response:**

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "name": "John Doe",
    "email": "john@example.com",
    "roles": ["User"],
    "permissions": ["view_users"],
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### POST /api/users

Membuat user baru (hanya Super Admin).

**Headers:** Authorization: Bearer {token}

**Request Body:**

```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "roles": ["User"]
}
```

### PUT /api/users/{id}

Update user (hanya Super Admin).

**Headers:** Authorization: Bearer {token}

**Request Body:**

```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "roles": ["Owner"]
}
```

### DELETE /api/users/{id}

Hapus user (hanya Super Admin).

**Headers:** Authorization: Bearer {token}

## Role Management Endpoints

### GET /api/roles

Mendapatkan daftar roles.

**Headers:** Authorization: Bearer {token}

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "name": "Super Admin",
      "permissions": ["*"]
    },
    {
      "id": "uuid",
      "name": "Owner",
      "permissions": ["manage_users", "view_reports"]
    }
  ]
}
```

### POST /api/roles

Membuat role baru (hanya Super Admin).

**Request Body:**

```json
{
  "name": "Manager",
  "permissions": ["view_users", "edit_users"]
}
```

### PUT /api/roles/{id}

Update role (hanya Super Admin).

### DELETE /api/roles/{id}

Hapus role (hanya Super Admin).

## Permission Management Endpoints

### GET /api/permissions

Mendapatkan daftar semua permissions.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "name": "view_users",
      "description": "Can view users list"
    },
    {
      "id": "uuid",
      "name": "manage_users",
      "description": "Can create, update, delete users"
    }
  ]
}
```

## Profile Management

### GET /api/profile

Mendapatkan profil user yang sedang login.

**Headers:** Authorization: Bearer {token}

### PUT /api/profile

Update profil user.

**Request Body:**

```json
{
  "name": "Updated Name",
  "email": "updated@example.com"
}
```

### PUT /api/profile/password

Ganti password.

**Request Body:**

```json
{
  "current_password": "old_password",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

## Dashboard Endpoints

### GET /api/dashboard/stats

Mendapatkan statistik dashboard.

**Response:**

```json
{
  "success": true,
  "data": {
    "total_users": 150,
    "total_roles": 5,
    "active_sessions": 25,
    "monthly_growth": 12.5
  }
}
```

## Error Codes

- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API memiliki rate limiting:

- Authentication endpoints: 5 requests per minute
- General endpoints: 60 requests per minute
