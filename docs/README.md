# Fullstack Vue.js Quasar + Laravel Application

## 📋 Project Overview

Aplikasi manajemen pengguna berbasis web dengan fitur lengkap menggunakan:

- **Frontend**: Vue.js 3 + Quasar Framework
- **Backend**: Laravel Framework (REST API)
- **Database**: MySQL dengan UUID primary keys
- **Authentication**: Laravel Sanctum
- **Permissions**: Laravel Permission (Spatie)

## 🚀 Features

### ✅ Completed Features

- [ ] Authentication System (Login/Forgot Password)
- [ ] Dashboard dengan sample data
- [ ] User Management (CRUD)
- [ ] Role & Permission Management
- [ ] Sidebar dengan toggle modes
- [ ] Header dengan profile menu
- [ ] Language switcher (EN/ID)
- [ ] Theme switcher (Dark/Light/System)
- [ ] UUID primary keys
- [ ] Default Super Admin user

### 🔄 In Progress

- Setting up project structure
- Creating backend Laravel API
- Creating frontend Quasar application

### 📝 To Do

- API integration testing
- UI/UX improvements
- Performance optimization
- Documentation completion

## 🏗️ Project Structure

```
based/
├── backend/                 # Laravel API
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
├── frontend/               # Quasar Vue.js App
│   ├── src/
│   ├── public/
│   └── ...
├── docs/                   # Documentation
└── README.md
```

## 🛠️ Technology Stack

### Backend (Laravel)

- **Framework**: Laravel 10.x
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **UUID**: Laravel UUID traits

### Frontend (Quasar Vue.js)

- **Framework**: Vue.js 3 (Composition API)
- **UI Framework**: Quasar Framework
- **State Management**: Pinia
- **HTTP Client**: Axios
- **Router**: Vue Router 4
- **Internationalization**: Vue I18n

## 📦 Required Packages

### Backend Dependencies

```json
{
  "laravel/sanctum": "^3.2",
  "spatie/laravel-permission": "^5.10",
  "ramsey/uuid": "^4.7"
}
```

### Frontend Dependencies

```json
{
  "@quasar/extras": "^1.16.4",
  "quasar": "^2.12.0",
  "vue": "^3.3.4",
  "pinia": "^2.1.4",
  "vue-router": "^4.2.4",
  "axios": "^1.4.0",
  "vue-i18n": "^9.2.2"
}
```

## 🚀 Getting Started

### Prerequisites

- Node.js 18+ & npm/yarn
- PHP 8.1+
- Composer
- MySQL 8.0+

### Installation Steps

1. Clone repository
2. Setup backend (Laravel)
3. Setup frontend (Quasar)
4. Configure database
5. Run migrations & seeders
6. Start development servers

## 📚 Documentation

- [Installation Guide](./installation.md)
- [API Documentation](./api.md)
- [Frontend Guide](./frontend.md)
- [User Management](./user-management.md)
- [Theme & Internationalization](./theme-i18n.md)
