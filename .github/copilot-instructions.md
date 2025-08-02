# Copilot Instructions

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Overview

This is a fullstack application with:

- **Frontend**: Vue.js 3 with Quasar Framework (CLI)
- **Backend**: Laravel Framework as REST API
- **Database**: MySQL with UUID primary keys
- **State Management**: Pinia
- **Authentication**: Laravel Sanctum + JWT
- **Permissions**: Laravel Permission (Spatie)

## Architecture Guidelines

- Use composition API for Vue.js components
- Follow Quasar Framework conventions
- Implement clean REST API architecture in Laravel
- Use UUID for all primary keys
- Follow PSR standards for PHP code
- Use TypeScript definitions where applicable

## Code Style

- Use ES6+ features
- Follow Vue.js 3 best practices
- Use async/await for API calls
- Implement proper error handling
- Use Pinia stores for state management

## Security

- Implement proper CORS configuration
- Use Laravel Sanctum for API authentication
- Validate all inputs on both frontend and backend
- Implement proper role-based access control
