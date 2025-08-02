# Frontend Guide - Quasar Vue.js

## Project Structure

```
frontend/
├── src/
│   ├── assets/          # Static assets
│   ├── boot/            # Boot files (plugins, etc)
│   ├── components/      # Reusable components
│   ├── css/            # Global styles
│   ├── i18n/           # Internationalization
│   ├── layouts/        # App layouts
│   ├── pages/          # Page components
│   ├── router/         # Vue Router configuration
│   ├── stores/         # Pinia stores
│   └── utils/          # Utility functions
├── public/             # Public assets
└── dist/               # Build output
```

## Key Features

### 1. Authentication System

- **Login Page**: Modern design dengan form validation
- **Forgot Password**: Email reset functionality
- **Route Guards**: Melindungi halaman yang memerlukan auth
- **Token Management**: Automatic token refresh

### 2. Dashboard

- **Statistics Cards**: Total users, roles, activities
- **Charts**: User growth, activity metrics
- **Recent Activities**: Real-time activity feed
- **Quick Actions**: Shortcut ke fitur utama

### 3. User Management

- **User List**: Paginated table dengan search & filter
- **User Form**: Create/Edit dengan role assignment
- **Bulk Actions**: Multiple user operations
- **Export**: Export user data ke Excel/PDF

### 4. Role & Permission Management

- **Role List**: Manage roles dan permissions
- **Permission Matrix**: Visual permission assignment
- **Role Assignment**: Assign roles ke users

### 5. Layout Features

- **Responsive Sidebar**: Mini mode, collapsible
- **Header**: Profile menu, notifications, theme toggle
- **Language Switcher**: EN/ID localization
- **Theme System**: Dark/Light/System modes

## Components Architecture

### 1. Base Components

#### `BaseButton.vue`

```vue
<template>
  <q-btn
    :class="buttonClass"
    :color="color"
    :size="size"
    :loading="loading"
    v-bind="$attrs"
  >
    <slot />
  </q-btn>
</template>
```

#### `BaseTable.vue`

```vue
<template>
  <q-table
    :rows="rows"
    :columns="columns"
    :loading="loading"
    :pagination="pagination"
    @request="onRequest"
  >
    <!-- Custom slots untuk actions, dll -->
  </q-table>
</template>
```

### 2. Layout Components

#### `MainLayout.vue`

- Sidebar navigation
- Header dengan profile menu
- Content area
- Footer

#### `AuthLayout.vue`

- Simple layout untuk auth pages
- Centered content
- Brand elements

### 3. Page Components

#### `LoginPage.vue`

```vue
<script setup>
import { ref } from "vue";
import { useAuthStore } from "stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  email: "",
  password: "",
});

const loading = ref(false);

const onSubmit = async () => {
  loading.value = true;
  try {
    await authStore.login(form.value);
    router.push("/dashboard");
  } catch (error) {
    // Handle error
  } finally {
    loading.value = false;
  }
};
</script>
```

## State Management (Pinia)

### Auth Store

```javascript
// stores/auth.js
import { defineStore } from "pinia";
import { api } from "boot/axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token"),
    isAuthenticated: false,
  }),

  getters: {
    userRoles: (state) => state.user?.roles || [],
    hasPermission: (state) => (permission) => {
      return state.user?.permissions?.includes(permission) || false;
    },
  },

  actions: {
    async login(credentials) {
      const response = await api.post("/auth/login", credentials);
      this.token = response.data.token;
      this.user = response.data.user;
      this.isAuthenticated = true;
      localStorage.setItem("token", this.token);
    },

    async logout() {
      await api.post("/auth/logout");
      this.token = null;
      this.user = null;
      this.isAuthenticated = false;
      localStorage.removeItem("token");
    },
  },
});
```

### UI Store

```javascript
// stores/ui.js
export const useUIStore = defineStore("ui", {
  state: () => ({
    sidebarMini: false,
    theme: "auto", // light, dark, auto
    language: "en",
  }),

  actions: {
    toggleSidebar() {
      this.sidebarMini = !this.sidebarMini;
    },

    setTheme(theme) {
      this.theme = theme;
      this.applyTheme();
    },

    setLanguage(lang) {
      this.language = lang;
      this.$i18n.locale = lang;
    },
  },
});
```

## Routing

### Route Configuration

```javascript
// router/routes.js
const routes = [
  {
    path: "/",
    component: () => import("layouts/AuthLayout.vue"),
    children: [
      { path: "", redirect: "/login" },
      { path: "login", component: () => import("pages/auth/LoginPage.vue") },
      {
        path: "forgot-password",
        component: () => import("pages/auth/ForgotPasswordPage.vue"),
      },
    ],
  },
  {
    path: "/dashboard",
    component: () => import("layouts/MainLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      { path: "", component: () => import("pages/DashboardPage.vue") },
      {
        path: "/users",
        component: () => import("pages/users/UserListPage.vue"),
        meta: { permission: "view_users" },
      },
      {
        path: "/roles",
        component: () => import("pages/roles/RoleListPage.vue"),
        meta: { permission: "manage_roles" },
      },
    ],
  },
];
```

### Route Guards

```javascript
// router/index.js
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  } else if (
    to.meta.permission &&
    !authStore.hasPermission(to.meta.permission)
  ) {
    next("/unauthorized");
  } else {
    next();
  }
});
```

## Internationalization

### Language Files

```javascript
// i18n/en-US/index.js
export default {
  common: {
    save: "Save",
    cancel: "Cancel",
    delete: "Delete",
    edit: "Edit",
    search: "Search",
  },
  auth: {
    login: "Login",
    email: "Email",
    password: "Password",
    forgotPassword: "Forgot Password?",
  },
  dashboard: {
    title: "Dashboard",
    totalUsers: "Total Users",
    activeUsers: "Active Users",
  },
};
```

### Usage in Components

```vue
<template>
  <div>
    <h1>{{ $t("dashboard.title") }}</h1>
    <q-btn :label="$t('common.save')" />
  </div>
</template>
```

## Theme System

### CSS Variables

```scss
// css/quasar.variables.scss
:root {
  --q-primary: #1976d2;
  --q-secondary: #26a69a;
  --q-accent: #9c27b0;
  --q-dark: #1d1d1d;
}

[data-theme="dark"] {
  --q-primary: #90caf9;
  --q-secondary: #80cbc4;
}
```

### Theme Composable

```javascript
// composables/useTheme.js
import { ref, watch } from "vue";
import { Dark } from "quasar";

export function useTheme() {
  const theme = ref(localStorage.getItem("theme") || "auto");

  const applyTheme = (newTheme) => {
    if (newTheme === "auto") {
      Dark.set("auto");
    } else {
      Dark.set(newTheme === "dark");
    }

    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);
  };

  watch(theme, applyTheme, { immediate: true });

  return { theme, applyTheme };
}
```

## API Integration

### Axios Configuration

```javascript
// boot/axios.js
import { boot } from "quasar/wrappers";
import axios from "axios";

const api = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL,
});

// Request interceptor
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login
      router.push("/login");
    }
    return Promise.reject(error);
  }
);

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = api;
});

export { api };
```

## Development Practices

### 1. Component Naming

- PascalCase untuk components
- kebab-case untuk events
- camelCase untuk props

### 2. Composition API

- Gunakan `<script setup>`
- Group related logic
- Extract reusable logic ke composables

### 3. Performance

- Lazy load pages
- Use v-memo untuk list items
- Implement virtual scrolling untuk large lists

### 4. Testing

```javascript
// tests/components/BaseButton.spec.js
import { mount } from "@vue/test-utils";
import BaseButton from "src/components/BaseButton.vue";

describe("BaseButton", () => {
  it("renders correctly", () => {
    const wrapper = mount(BaseButton, {
      props: { color: "primary" },
      slots: { default: "Click me" },
    });

    expect(wrapper.text()).toContain("Click me");
  });
});
```
