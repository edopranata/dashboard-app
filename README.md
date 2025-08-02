# Dashboard Management Application

> Modern fullstack web application dengan Vue.js Quasar Framework dan Laravel REST API untuk manajemen pengguna dengan sistem role-based permissions.

## 🚀 Overview

Aplikasi manajemen dashboard yang komprehensif dengan fitur:

- **Authentication System** dengan Login & Forgot Password
- **User Management** dengan CRUD operations
- **Role & Permission Management** menggunakan Laravel Permission
- **Modern UI/UX** dengan Quasar Framework
- **Multi-language Support** (English & Indonesian)
- **Dark/Light Theme** dengan system preference detection
- **Responsive Design** untuk semua device sizes

## 📋 Features

### ✅ Completed

- [x] Project setup (Laravel + Quasar)
- [x] Authentication system foundation
- [x] Database structure with UUID primary keys
- [x] Documentation structure
- [x] Development environment setup

### 🔄 In Progress

- [ ] Backend API development
- [ ] Frontend component development
- [ ] Authentication UI/UX
- [ ] Dashboard implementation

### 📝 Planned

- [ ] User management CRUD
- [ ] Role & permission system
- [ ] Theme & language switcher
- [ ] Profile management
- [ ] API integration testing

## 🛠️ Tech Stack

### Backend

- **Laravel 10.x** - PHP Framework
- **MySQL 8.0+** - Database
- **Laravel Sanctum** - API Authentication
- **Spatie Laravel Permission** - Role & Permission Management
- **UUID** - Primary Keys

### Frontend

- **Vue.js 3** - Progressive JavaScript Framework
- **Quasar Framework** - Vue.js Component Library
- **Pinia** - State Management
- **Vue Router 4** - Client-side Routing
- **Vue I18n** - Internationalization
- **Axios** - HTTP Client

### Development Tools

- **Vite** - Build Tool
- **ESLint** - Code Linting
- **Prettier** - Code Formatting
- **PHPUnit** - Backend Testing
- **Vitest** - Frontend Testing

## 📁 Project Structure

```
based/
├── backend/                 # Laravel API Application
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── ...
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   └── ...
├── frontend/               # Quasar Vue.js Application
│   ├── src/
│   │   ├── components/
│   │   ├── layouts/
│   │   ├── pages/
│   │   ├── stores/
│   │   ├── router/
│   │   └── ...
│   ├── public/
│   └── ...
├── docs/                   # Project Documentation
│   ├── installation.md
│   ├── api.md
│   ├── frontend.md
│   ├── user-management.md
│   └── theme-i18n.md
└── README.md              # This file
```

## 🚀 Quick Start

### Prerequisites

- Node.js 18+ & npm/yarn
- PHP 8.1+
- Composer
- MySQL 8.0+

### Installation

1. **Clone repository**

   ```bash
   git clone <repository-url>
   cd based
   ```

2. **Setup Backend**

   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   # Configure database in .env
   php artisan migrate --seed
   php artisan serve
   ```

3. **Setup Frontend**

   ```bash
   cd frontend
   npm install
   npm run dev
   ```

4. **Access Application**
   - Frontend: http://localhost:9000
   - Backend API: http://localhost:8000

### Default Super Admin Account

```
Email: admin@dashboard.com
Password: SuperAdmin123!
```

## 📚 Documentation

- [📖 Installation Guide](./docs/installation.md) - Detailed setup instructions
- [🔌 API Documentation](./docs/api.md) - REST API endpoints & usage
- [🎨 Frontend Guide](./docs/frontend.md) - Vue.js & Quasar development
- [👥 User Management](./docs/user-management.md) - RBAC system overview
- [🎭 Theme & i18n](./docs/theme-i18n.md) - Theming & internationalization

## 🏗️ Development

### Backend Commands

```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Create model with migration
php artisan make:model ModelName -m

# Run tests
php artisan test
```

### Frontend Commands

```bash
# Development server
npm run dev

# Build for production
npm run build

# Lint & fix code
npm run lint -- --fix

# Run tests
npm run test
```

## 🎨 UI/UX Features

### Authentication

- Modern login form design
- Forgot password workflow
- Form validation & error handling
- Loading states & animations

### Dashboard

- Statistics cards with icons
- Interactive charts & graphs
- Recent activities feed
- Quick action buttons
- Responsive grid layout

### Layout

- Collapsible sidebar with mini mode
- Fixed/static header options
- Profile dropdown menu
- Theme switcher (Dark/Light/System)
- Language switcher (EN/ID)

### Components

- Reusable base components
- Data tables with pagination
- Modal dialogs & forms
- Toast notifications
- Loading indicators

## 🔒 Security Features

- Laravel Sanctum authentication
- Role-based access control (RBAC)
- Input validation & sanitization
- CORS configuration
- Rate limiting
- XSS protection
- CSRF protection

## 🌐 Internationalization

### Supported Languages

- **English (EN)** - Default
- **Indonesian (ID)** - Local language

### Features

- Dynamic language switching
- Persistent language preference
- Formatted dates & numbers
- RTL support ready
- Quasar component translations

## 🎭 Theme System

### Available Themes

- **Light Mode** - Default light theme
- **Dark Mode** - Dark theme for low-light environments
- **System Mode** - Follows OS preference

### Features

- Smooth theme transitions
- CSS custom properties
- Component-level theme support
- Persistent theme preference

## 📊 Monitoring & Analytics

### Planned Features

- User activity tracking
- System performance metrics
- Error logging & monitoring
- Usage analytics dashboard
- API request monitoring

## 🧪 Testing

### Backend Testing

- Feature tests for API endpoints
- Unit tests for models & services
- Policy testing for authorization
- Database testing with factories

### Frontend Testing

- Component unit tests
- Integration tests for stores
- E2E testing with Cypress
- Visual regression testing

## 🚀 Deployment

### Production Setup

- Environment configuration
- Database optimization
- Asset compilation
- SSL certificate setup
- Server configuration
- CI/CD pipeline

### Performance Optimization

- Laravel optimization commands
- Database indexing
- Asset minification
- CDN integration
- Caching strategies

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Development Guidelines

- Follow PSR standards for PHP
- Use Vue.js 3 Composition API
- Implement proper error handling
- Write comprehensive tests
- Update documentation

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Lead Developer** - Full-stack development
- **UI/UX Designer** - Interface design
- **DevOps Engineer** - Deployment & infrastructure

## 📞 Support

For support and questions:

- 📧 Email: support@dashboard.com
- 📱 Issues: [GitHub Issues](link-to-issues)
- 📖 Documentation: [Project Docs](./docs/)

## 🔄 Changelog

### v1.0.0 (In Development)

- Initial project setup
- Authentication system
- User management foundation
- Theme & i18n system
- API documentation

---

**Made with ❤️ using Vue.js, Quasar, and Laravel**
