# Installation Guide

## Prerequisites

Pastikan Anda memiliki tools berikut ter-install:

- **Node.js** 18+ dan npm/yarn
- **PHP** 8.1+
- **Composer**
- **MySQL** 8.0+
- **Git**

## Backend Setup (Laravel)

### 1. Install Dependencies

```bash
cd backend
composer install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dashboard_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Create Database

```bash
mysql -u root -p
CREATE DATABASE dashboard_app;
```

### 5. Configure Packages

```bash
# Publish Laravel Sanctum migration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Publish Laravel Permission migration
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 6. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

### 7. Start Development Server

```bash
php artisan serve
# Server akan berjalan di http://localhost:8000
```

## Frontend Setup (Quasar Vue.js)

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Environment Configuration

Create `.env` file in frontend directory:

```env
VUE_APP_API_BASE_URL=http://localhost:8000/api
```

### 3. Start Development Server

```bash
npm run dev
# atau
quasar dev
# Server akan berjalan di http://localhost:9000
```

## Development Workflow

### Backend Commands

```bash
# Run migrations
php artisan migrate

# Reset database
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName -m

# Create new controller
php artisan make:controller ControllerName

# Run tests
php artisan test
```

### Frontend Commands

```bash
# Start development server
npm run dev

# Build for production
npm run build

# Lint code
npm run lint

# Fix lint issues
npm run lint -- --fix
```

## Production Setup

### Backend Production

```bash
# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### Frontend Production

```bash
# Build for production
npm run build

# Files akan tersedia di dist/ directory
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Pastikan MySQL service berjalan
   - Periksa credentials di `.env`

2. **npm install gagal**

   - Coba: `npm install --legacy-peer-deps`
   - Atau gunakan yarn: `yarn install`

3. **Laravel Permission Error**

   - Jalankan: `php artisan config:clear`
   - Lalu: `php artisan migrate:fresh --seed`

4. **CORS Error**
   - Periksa konfigurasi CORS di `config/cors.php`
   - Pastikan frontend URL ada di allowed origins

### Performance Tips

1. **Backend**

   - Gunakan database indexing
   - Implement caching (Redis/Memcached)
   - Optimize database queries

2. **Frontend**
   - Implement lazy loading
   - Use Quasar's tree-shaking
   - Optimize images dan assets
