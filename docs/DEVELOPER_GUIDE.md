# 🚀 Developer Guide

> **Comprehensive development guide untuk Dashboard Management Application**

## 📋 Project Overview

**Fullstack Vue.js Quasar + Laravel application** dengan fitur lengkap:

- **Frontend**: Vue.js 3 + Quasar Framework (CLI)
- **Backend**: Laravel Framework REST API
- **Database**: MySQL with UUID primary keys
- **State Management**: Pinia
- **Authentication**: Laravel Sanctum + JWT
- **Permissions**: Laravel Permission (Spatie)

---

## 🏗️ Project Structure

```
dashboard-app/
├── backend/                 # Laravel API Backend
│   ├── app/
│   │   ├── Http/Controllers/API/  # API Controllers
│   │   ├── Models/         # Eloquent Models
│   │   └── ...
│   ├── database/
│   │   ├── migrations/     # Database migrations
│   │   └── seeders/        # Database seeders
│   └── ...
├── frontend/               # Vue.js + Quasar Frontend
│   ├── src/
│   │   ├── components/     # Reusable components
│   │   ├── layouts/        # App layouts
│   │   ├── pages/          # Page components
│   │   ├── stores/         # Pinia stores
│   │   ├── services/       # API services
│   │   ├── i18n/           # Internationalization
│   │   └── css/            # Global styles
│   └── ...
└── docs/                   # Project documentation
```

---

## 🛠️ Development Setup

### Prerequisites

- **Node.js** 18+ dan npm/yarn
- **PHP** 8.1+
- **Composer**
- **MySQL** 8.0+
- **Git**

### Backend Setup (Laravel)

```bash
# Navigate to backend directory
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_DATABASE=dashboard_app
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate:fresh --seed

# Start development server
php artisan serve
```

### Frontend Setup (Quasar)

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Start development server
quasar dev

# For production build
quasar build
```

---

## 🔐 Authentication System

### Default Accounts

| Role            | Email                 | Password         | Capabilities       |
| --------------- | --------------------- | ---------------- | ------------------ |
| **Super Admin** | `admin@dashboard.com` | `SuperAdmin123!` | Full system access |
| **Owner**       | `owner@dashboard.com` | `Owner123!`      | User management    |
| **User**        | `user@dashboard.com`  | `User123!`       | Basic access       |

### API Authentication

```javascript
// Login request
POST /api/auth/login
{
  "email": "admin@dashboard.com",
  "password": "SuperAdmin123!"
}

// Response
{
  "success": true,
  "data": {
    "user": {...},
    "token": "bearer_token_here"
  }
}

// Authenticated requests
headers: {
  'Authorization': 'Bearer bearer_token_here'
}
```

---

## 🎨 Frontend Architecture

### Component Structure

```
src/components/
├── AvatarDisplay.vue       # User avatar component
├── AvatarUpload.vue        # Avatar upload functionality
├── LanguageSwitcher.vue    # Language selection
└── AuthLanguageSwitcher.vue # Language switcher for auth pages
```

### Page Structure

```
src/pages/
├── auth/
│   ├── LoginPage.vue       # Authentication page
│   └── ResetPasswordPage.vue
├── users/
│   ├── UserListPage.vue    # User management list
│   └── UserFormPage.vue    # User create/edit form
├── roles/
│   ├── RoleListPage.vue    # Role management list
│   └── RoleFormPage.vue    # Role create/edit form
├── DashboardPage.vue       # Main dashboard
└── ProfilePage.vue         # User profile page
```

### State Management (Pinia)

```javascript
// stores/auth.js - Authentication store
export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
  }),
  actions: {
    async login(credentials) {
      /* ... */
    },
    async logout() {
      /* ... */
    },
    async fetchUser() {
      /* ... */
    },
  },
});
```

### API Services

```javascript
// services/userService.js
export const userService = {
  async getUsers(params = {}) {
    return await apiClient.get("/users", { params });
  },
  async createUser(userData) {
    return await apiClient.post("/users", userData);
  },
  async updateUser(id, userData) {
    return await apiClient.put(`/users/${id}`, userData);
  },
  async deleteUser(id) {
    return await apiClient.delete(`/users/${id}`);
  },
};
```

---

## 🔧 Backend Architecture

### API Controllers

```php
// app/Http/Controllers/API/UserController.php
class UserController extends Controller
{
    public function index(Request $request)
    {
        // Permission check
        if (!$request->user()->can('view_users')) {
            return response()->json(['success' => false], 403);
        }

        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($request->per_page ?? 15);

        return response()->json(['success' => true, 'data' => $users]);
    }
}
```

### Database Models

```php
// app/Models/User.php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'email', 'password', 'avatar'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];
}
```

### Permissions System

```php
// 19 predefined permissions
$permissions = [
    // User Management
    'view_users', 'create_users', 'edit_users', 'delete_users',

    // Role Management
    'view_roles', 'create_roles', 'edit_roles', 'delete_roles',

    // Dashboard
    'view_dashboard', 'view_analytics',

    // Profile
    'view_profile', 'edit_profile',

    // System
    'view_settings', 'edit_settings', 'view_logs',
    'export_data', 'import_data', 'backup_system', 'manage_system'
];
```

---

## 🌐 Internationalization (i18n)

### Language Support

- **English (en-US)**: Complete translation (390+ keys)
- **Indonesian (id-ID)**: Complete translation (390+ keys)

### Translation Structure

```javascript
// src/i18n/en-US/index.js
export default {
  auth: {
    login: "Login",
    logout: "Logout",
    forgotPassword: "Forgot Password",
  },
  users: {
    title: "User Management",
    createUser: "Create User",
    editUser: "Edit User",
  },
  roles: {
    title: "Role Management",
    permissions: "Permissions",
  },
};
```

### Usage in Components

```vue
<template>
  <q-btn :label="$t('auth.login')" />
  <q-input :label="$t('users.name')" />
</template>

<script setup>
import { useI18n } from "vue-i18n";
const { t } = useI18n();

// In JavaScript
const message = t("users.created");
</script>
```

---

## 🎨 Styling System

### CSS Architecture

```scss
// src/css/global.scss
:root {
  // Color Variables
  --primary-color: #1976d2;
  --secondary-color: #26a69a;
  --accent-color: #9c27b0;

  // Spacing
  --spacing-xs: 4px;
  --spacing-sm: 8px;
  --spacing-md: 16px;
  --spacing-lg: 24px;
  --spacing-xl: 32px;

  // Border Radius
  --border-radius-sm: 4px;
  --border-radius-md: 8px;
  --border-radius-lg: 12px;
}
```

### Component Classes

```scss
// Utility Classes
.text-gradient {
  background: linear-gradient(
    135deg,
    var(--primary-color),
    var(--secondary-color)
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.card-elevated {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  }
}
```

### Dark Mode Support

```scss
body.body--dark {
  --bg-color: #121212;
  --surface-color: #1e1e1e;
  --text-color: #ffffff;
}

body.body--light {
  --bg-color: #ffffff;
  --surface-color: #f5f5f5;
  --text-color: #000000;
}
```

---

## 🧪 Testing & Quality

### Backend Testing

```bash
# Run PHP tests
php artisan test

# Run specific test
php artisan test --filter UserControllerTest

# Generate coverage report
php artisan test --coverage
```

### Frontend Testing

```bash
# Run Vue component tests
npm run test:unit

# Run E2E tests
npm run test:e2e

# Linting
npm run lint

# Format code
npm run format
```

### Code Quality Standards

- **Backend**: PSR-12 coding standards
- **Frontend**: ESLint + Prettier configuration
- **Git**: Conventional commits format
- **API**: RESTful design principles

---

## 🚀 Deployment

### Production Build

```bash
# Backend preparation
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend build
cd frontend
npm run build
```

### Environment Configuration

```env
# Production .env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

---

## 📚 API Reference

### Base URL

```
Production: https://api.yourdomain.com
Development: http://localhost:8000/api
```

### Authentication Endpoints

| Method | Endpoint                | Description            |
| ------ | ----------------------- | ---------------------- |
| POST   | `/auth/login`           | User login             |
| POST   | `/auth/logout`          | User logout            |
| POST   | `/auth/refresh`         | Refresh token          |
| POST   | `/auth/forgot-password` | Request password reset |
| POST   | `/auth/reset-password`  | Reset password         |

### User Management Endpoints

| Method | Endpoint      | Description                |
| ------ | ------------- | -------------------------- |
| GET    | `/users`      | List users with pagination |
| POST   | `/users`      | Create new user            |
| GET    | `/users/{id}` | Get user details           |
| PUT    | `/users/{id}` | Update user                |
| DELETE | `/users/{id}` | Delete user                |

### Role Management Endpoints

| Method | Endpoint             | Description                 |
| ------ | -------------------- | --------------------------- |
| GET    | `/roles`             | List roles with permissions |
| POST   | `/roles`             | Create new role             |
| GET    | `/roles/{id}`        | Get role details            |
| PUT    | `/roles/{id}`        | Update role                 |
| DELETE | `/roles/{id}`        | Delete role                 |
| GET    | `/roles/permissions` | List all permissions        |

---

## 🛠️ Development Tools

### VS Code Extensions

```json
{
  "recommendations": [
    "Vue.volar",
    "bradlc.vscode-tailwindcss",
    "esbenp.prettier-vscode",
    "ms-vscode.vscode-typescript-next",
    "bmewburn.vscode-intelephense-client"
  ]
}
```

### Useful Commands

```bash
# Generate new Vue component
quasar new component ComponentName

# Generate new API resource
php artisan make:resource UserResource

# Generate new migration
php artisan make:migration create_table_name

# Clear all caches
php artisan optimize:clear
```

---

## 🐛 Troubleshooting

### Common Issues

1. **CORS Errors**

   ```php
   // config/cors.php
   'allowed_origins' => ['http://localhost:9000'],
   ```

2. **Token Refresh Issues**

   ```javascript
   // Check token expiration
   if (error.response?.status === 401) {
     await authStore.logout();
   }
   ```

3. **Permission Denied**
   ```bash
   # Reset permissions
   php artisan permission:cache-reset
   ```

### Debug Mode

```bash
# Enable debug mode
APP_DEBUG=true

# View logs
tail -f storage/logs/laravel.log

# Database queries
DB_LOG_QUERIES=true
```

---

## 📞 Support & Contributing

### Getting Help

- **Documentation**: Check this guide and API docs
- **Issues**: Create GitHub issue with reproduction steps
- **Discussions**: Use GitHub Discussions for questions

### Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Code Style

- Follow PSR-12 for PHP
- Use Vue 3 Composition API
- Write meaningful commit messages
- Add tests for new features

---

**📊 Last Updated**: August 3, 2025  
**🏷️ Current Version**: v1.3.0-beta.1  
**🎯 Status**: Production Ready Core Features
