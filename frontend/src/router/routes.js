const routes = [
  // Public routes (no authentication required)
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/auth/LoginPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'forgot-password',
        name: 'forgot-password',
        component: () => import('pages/auth/ForgotPasswordPage.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'reset-password',
        name: 'reset-password',
        component: () => import('pages/auth/ResetPasswordPage.vue'),
        meta: { requiresGuest: true },
      },
    ],
  },

  // Protected routes (authentication required)
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('pages/DashboardPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_dashboard',
          title: 'Dashboard',
        },
      },

      // User Management Routes
      {
        path: 'users',
        name: 'users.index',
        component: () => import('pages/users/UserListPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_users',
          title: 'User Management',
        },
      },
      {
        path: 'users/create',
        name: 'users.create',
        component: () => import('pages/users/UserFormPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'create_users',
          title: 'Create User',
        },
      },
      {
        path: 'users/:id/edit',
        name: 'users.edit',
        component: () => import('pages/users/UserFormPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'edit_users',
          title: 'Edit User',
        },
      },

      // Role Management Routes
      {
        path: 'roles',
        name: 'roles.index',
        component: () => import('pages/roles/RoleListPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_roles',
          title: 'Role Management',
        },
      },
      {
        path: 'roles/create',
        name: 'roles.create',
        component: () => import('pages/roles/RoleFormPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'create_roles',
          title: 'Create Role',
        },
      },
      {
        path: 'roles/:id/edit',
        name: 'roles.edit',
        component: () => import('pages/roles/RoleFormPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'edit_roles',
          title: 'Edit Role',
        },
      },

      // Profile Routes
      {
        path: 'profile',
        name: 'profile',
        component: () => import('pages/ProfilePage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_profile',
          title: 'Profile',
        },
      },

      // Demo/Under Development Routes
      {
        path: 'analytics',
        name: 'analytics',
        component: () => import('pages/UnderDevelopmentPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_analytics',
          title: 'Analytics',
        },
      },
      {
        path: 'settings',
        name: 'settings',
        component: () => import('pages/UnderDevelopmentPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'manage_settings',
          title: 'Settings',
        },
      },
      {
        path: 'logs',
        name: 'logs',
        component: () => import('pages/UnderDevelopmentPage.vue'),
        meta: {
          requiresAuth: true,
          permission: 'view_logs',
          title: 'System Logs',
        },
      },
    ],
  },

  // Redirect /login to /auth/login
  {
    path: '/login',
    redirect: '/auth/login',
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
