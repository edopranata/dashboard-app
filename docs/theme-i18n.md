# Theme & Internationalization

## Theme System

### Overview

Aplikasi menggunakan sistem theme yang mendukung:

- **Light Mode**: Theme terang untuk penggunaan sehari-hari
- **Dark Mode**: Theme gelap untuk mengurangi eye strain
- **System Mode**: Mengikuti preferensi sistem operasi

### Implementation

#### 1. Theme Store (Pinia)

```javascript
// stores/theme.js
import { defineStore } from "pinia";
import { Dark, LocalStorage } from "quasar";

export const useThemeStore = defineStore("theme", {
  state: () => ({
    currentTheme: LocalStorage.getItem("theme") || "system",
    availableThemes: [
      { value: "light", label: "Light", icon: "light_mode" },
      { value: "dark", label: "Dark", icon: "dark_mode" },
      { value: "system", label: "System", icon: "brightness_auto" },
    ],
  }),

  getters: {
    isDark: () => Dark.isActive,
    themeIcon: (state) => {
      const theme = state.availableThemes.find(
        (t) => t.value === state.currentTheme
      );
      return theme?.icon || "brightness_auto";
    },
  },

  actions: {
    setTheme(theme) {
      this.currentTheme = theme;
      LocalStorage.set("theme", theme);

      if (theme === "system") {
        Dark.set("auto");
      } else {
        Dark.set(theme === "dark");
      }

      // Apply CSS custom properties
      this.applyCSSVariables(theme);

      // Emit event for components that need to react
      document.dispatchEvent(
        new CustomEvent("theme-changed", {
          detail: { theme },
        })
      );
    },

    applyCSSVariables(theme) {
      const root = document.documentElement;

      if (theme === "dark" || (theme === "system" && Dark.isActive)) {
        root.style.setProperty("--sidebar-bg", "#1e1e1e");
        root.style.setProperty("--header-bg", "#2d2d2d");
        root.style.setProperty("--card-bg", "#2d2d2d");
        root.style.setProperty("--text-primary", "#ffffff");
        root.style.setProperty("--text-secondary", "#b0b0b0");
        root.style.setProperty("--border-color", "#404040");
      } else {
        root.style.setProperty("--sidebar-bg", "#ffffff");
        root.style.setProperty("--header-bg", "#f5f5f5");
        root.style.setProperty("--card-bg", "#ffffff");
        root.style.setProperty("--text-primary", "#000000");
        root.style.setProperty("--text-secondary", "#666666");
        root.style.setProperty("--border-color", "#e0e0e0");
      }
    },

    initializeTheme() {
      this.setTheme(this.currentTheme);
    },
  },
});
```

#### 2. Theme Toggle Component

```vue
<!-- components/ThemeToggle.vue -->
<template>
  <q-btn-dropdown flat round :icon="themeStore.themeIcon" class="theme-toggle">
    <q-list>
      <q-item
        v-for="theme in themeStore.availableThemes"
        :key="theme.value"
        clickable
        v-close-popup
        @click="setTheme(theme.value)"
        :class="{ 'bg-primary text-white': currentTheme === theme.value }"
      >
        <q-item-section avatar>
          <q-icon :name="theme.icon" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $t(`themes.${theme.value}`) }}</q-item-label>
        </q-item-section>
        <q-item-section side v-if="currentTheme === theme.value">
          <q-icon name="check" color="positive" />
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script setup>
import { computed } from "vue";
import { useThemeStore } from "stores/theme";

const themeStore = useThemeStore();

const currentTheme = computed(() => themeStore.currentTheme);

const setTheme = (theme) => {
  themeStore.setTheme(theme);
};
</script>

<style lang="scss" scoped>
.theme-toggle {
  transition: all 0.3s ease;

  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
}
</style>
```

#### 3. CSS Custom Properties

```scss
// css/quasar.variables.scss

// Light Theme Colors
:root {
  --q-primary: #1976d2;
  --q-secondary: #26a69a;
  --q-accent: #9c27b0;
  --q-positive: #21ba45;
  --q-negative: #c10015;
  --q-info: #31ccec;
  --q-warning: #f2c037;

  // Custom Variables
  --sidebar-bg: #{$grey-1};
  --header-bg: #{$grey-2};
  --card-bg: #{$white};
  --text-primary: #{$grey-10};
  --text-secondary: #{$grey-7};
  --border-color: #{$grey-4};
  --shadow-color: rgba(0, 0, 0, 0.12);
}

// Dark Theme Colors
body.body--dark {
  --q-primary: #90caf9;
  --q-secondary: #80cbc4;
  --q-accent: #ce93d8;

  // Custom Variables
  --sidebar-bg: #{$grey-10};
  --header-bg: #{$grey-9};
  --card-bg: #{$grey-9};
  --text-primary: #{$grey-1};
  --text-secondary: #{$grey-4};
  --border-color: #{$grey-7};
  --shadow-color: rgba(0, 0, 0, 0.4);
}

// Component Styles
.sidebar {
  background-color: var(--sidebar-bg);
  border-right: 1px solid var(--border-color);

  .q-item {
    color: var(--text-primary);

    &:hover {
      background-color: rgba(var(--q-primary-rgb), 0.1);
    }

    &.q-item--active {
      background-color: rgba(var(--q-primary-rgb), 0.15);
      color: var(--q-primary);
    }
  }
}

.header {
  background-color: var(--header-bg);
  border-bottom: 1px solid var(--border-color);
  box-shadow: 0 1px 3px var(--shadow-color);
}

.card {
  background-color: var(--card-bg);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-color);
}
```

#### 4. Theme Composable

```javascript
// composables/useTheme.js
import { computed, onMounted, onUnmounted } from "vue";
import { useThemeStore } from "stores/theme";

export function useTheme() {
  const themeStore = useThemeStore();

  const currentTheme = computed(() => themeStore.currentTheme);
  const isDark = computed(() => themeStore.isDark);

  const setTheme = (theme) => {
    themeStore.setTheme(theme);
  };

  const toggleTheme = () => {
    const nextTheme = currentTheme.value === "light" ? "dark" : "light";
    setTheme(nextTheme);
  };

  // Listen for system theme changes
  const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

  const handleSystemThemeChange = (e) => {
    if (currentTheme.value === "system") {
      themeStore.applyCSSVariables("system");
    }
  };

  onMounted(() => {
    mediaQuery.addEventListener("change", handleSystemThemeChange);
  });

  onUnmounted(() => {
    mediaQuery.removeEventListener("change", handleSystemThemeChange);
  });

  return {
    currentTheme,
    isDark,
    setTheme,
    toggleTheme,
  };
}
```

## Internationalization (i18n)

### Overview

Aplikasi mendukung multi-bahasa dengan:

- **English (EN)**: Bahasa default
- **Indonesian (ID)**: Bahasa lokal

### Implementation

#### 1. Language Store (Pinia)

```javascript
// stores/language.js
import { defineStore } from "pinia";
import { LocalStorage } from "quasar";
import { i18n } from "boot/i18n";

export const useLanguageStore = defineStore("language", {
  state: () => ({
    currentLocale: LocalStorage.getItem("locale") || "en",
    availableLocales: [
      {
        value: "en",
        label: "English",
        flag: "us",
        nativeName: "English",
      },
      {
        value: "id",
        label: "Indonesian",
        flag: "id",
        nativeName: "Bahasa Indonesia",
      },
    ],
  }),

  getters: {
    currentLocaleInfo: (state) => {
      return (
        state.availableLocales.find(
          (locale) => locale.value === state.currentLocale
        ) || state.availableLocales[0]
      );
    },
  },

  actions: {
    setLocale(locale) {
      this.currentLocale = locale;
      i18n.global.locale.value = locale;
      LocalStorage.set("locale", locale);

      // Update document language
      document.documentElement.lang = locale;

      // Update Quasar language pack
      this.setQuasarLang(locale);

      // Emit event for components that need to react
      document.dispatchEvent(
        new CustomEvent("locale-changed", {
          detail: { locale },
        })
      );
    },

    async setQuasarLang(locale) {
      try {
        const langMap = {
          en: "en-US",
          id: "id",
        };

        const quasarLang = langMap[locale] || "en-US";
        const langPack = await import(`quasar/lang/${quasarLang}`);

        // Apply Quasar language pack
        this.$q.lang.set(langPack.default);
      } catch (error) {
        console.warn(`Failed to load Quasar language pack for ${locale}`);
      }
    },

    initializeLocale() {
      this.setLocale(this.currentLocale);
    },
  },
});
```

#### 2. Language Switcher Component

```vue
<!-- components/LanguageSwitcher.vue -->
<template>
  <q-btn-dropdown flat round class="language-switcher">
    <template v-slot:label>
      <q-avatar size="24px">
        <img
          :src="`https://flagcdn.com/24x18/${currentLocaleInfo.flag}.png`"
          :alt="currentLocaleInfo.label"
        />
      </q-avatar>
    </template>

    <q-list>
      <q-item
        v-for="locale in availableLocales"
        :key="locale.value"
        clickable
        v-close-popup
        @click="setLocale(locale.value)"
        :class="{ 'bg-primary text-white': currentLocale === locale.value }"
      >
        <q-item-section avatar>
          <q-avatar size="20px">
            <img
              :src="`https://flagcdn.com/20x15/${locale.flag}.png`"
              :alt="locale.label"
            />
          </q-avatar>
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ locale.nativeName }}</q-item-label>
          <q-item-label caption>{{ locale.label }}</q-item-label>
        </q-item-section>
        <q-item-section side v-if="currentLocale === locale.value">
          <q-icon name="check" color="positive" />
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script setup>
import { computed } from "vue";
import { useLanguageStore } from "stores/language";

const languageStore = useLanguageStore();

const currentLocale = computed(() => languageStore.currentLocale);
const availableLocales = computed(() => languageStore.availableLocales);
const currentLocaleInfo = computed(() => languageStore.currentLocaleInfo);

const setLocale = (locale) => {
  languageStore.setLocale(locale);
};
</script>

<style lang="scss" scoped>
.language-switcher {
  transition: all 0.3s ease;

  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
}
</style>
```

#### 3. Translation Files

**English (en-US)**

```javascript
// i18n/en-US/index.js
export default {
  // Common
  common: {
    save: "Save",
    cancel: "Cancel",
    delete: "Delete",
    edit: "Edit",
    create: "Create",
    update: "Update",
    search: "Search",
    filter: "Filter",
    actions: "Actions",
    loading: "Loading...",
    noData: "No data available",
    confirm: "Confirm",
    yes: "Yes",
    no: "No",
    close: "Close",
  },

  // Navigation
  nav: {
    dashboard: "Dashboard",
    users: "Users",
    roles: "Roles",
    permissions: "Permissions",
    profile: "Profile",
    settings: "Settings",
    logout: "Logout",
  },

  // Authentication
  auth: {
    login: "Login",
    logout: "Logout",
    email: "Email",
    password: "Password",
    confirmPassword: "Confirm Password",
    forgotPassword: "Forgot Password?",
    resetPassword: "Reset Password",
    rememberMe: "Remember Me",
    loginButton: "Sign In",
    loginSuccess: "Login successful",
    loginError: "Invalid credentials",
    logoutSuccess: "Logged out successfully",
  },

  // Dashboard
  dashboard: {
    title: "Dashboard",
    welcome: "Welcome back",
    totalUsers: "Total Users",
    activeUsers: "Active Users",
    totalRoles: "Total Roles",
    recentActivities: "Recent Activities",
    userGrowth: "User Growth",
    systemHealth: "System Health",
  },

  // Users
  users: {
    title: "User Management",
    addUser: "Add User",
    editUser: "Edit User",
    deleteUser: "Delete User",
    userList: "User List",
    name: "Name",
    email: "Email",
    roles: "Roles",
    status: "Status",
    createdAt: "Created At",
    filterByRole: "Filter by Role",
    userCreated: "User created successfully",
    userUpdated: "User updated successfully",
    userDeleted: "User deleted successfully",
    confirmDelete: "Are you sure you want to delete this user?",
  },

  // Roles
  roles: {
    title: "Role Management",
    addRole: "Add Role",
    editRole: "Edit Role",
    deleteRole: "Delete Role",
    roleName: "Role Name",
    permissions: "Permissions",
    description: "Description",
    roleCreated: "Role created successfully",
    roleUpdated: "Role updated successfully",
    roleDeleted: "Role deleted successfully",
  },

  // Themes
  themes: {
    light: "Light",
    dark: "Dark",
    system: "System",
  },

  // Errors
  errors: {
    unauthorized: "Unauthorized access",
    forbidden: "Access forbidden",
    notFound: "Page not found",
    serverError: "Internal server error",
    networkError: "Network connection error",
    validationError: "Validation error",
  },

  // Validation
  validation: {
    required: "This field is required",
    email: "Invalid email format",
    minLength: "Minimum {min} characters required",
    maxLength: "Maximum {max} characters allowed",
    passwordMismatch: "Passwords do not match",
  },
};
```

**Indonesian (id)**

```javascript
// i18n/id/index.js
export default {
  // Common
  common: {
    save: "Simpan",
    cancel: "Batal",
    delete: "Hapus",
    edit: "Ubah",
    create: "Buat",
    update: "Perbarui",
    search: "Cari",
    filter: "Filter",
    actions: "Aksi",
    loading: "Memuat...",
    noData: "Tidak ada data",
    confirm: "Konfirmasi",
    yes: "Ya",
    no: "Tidak",
    close: "Tutup",
  },

  // Navigation
  nav: {
    dashboard: "Dasbor",
    users: "Pengguna",
    roles: "Peran",
    permissions: "Izin",
    profile: "Profil",
    settings: "Pengaturan",
    logout: "Keluar",
  },

  // Authentication
  auth: {
    login: "Masuk",
    logout: "Keluar",
    email: "Email",
    password: "Kata Sandi",
    confirmPassword: "Konfirmasi Kata Sandi",
    forgotPassword: "Lupa Kata Sandi?",
    resetPassword: "Atur Ulang Kata Sandi",
    rememberMe: "Ingat Saya",
    loginButton: "Masuk",
    loginSuccess: "Berhasil masuk",
    loginError: "Kredensial tidak valid",
    logoutSuccess: "Berhasil keluar",
  },

  // Dashboard
  dashboard: {
    title: "Dasbor",
    welcome: "Selamat datang kembali",
    totalUsers: "Total Pengguna",
    activeUsers: "Pengguna Aktif",
    totalRoles: "Total Peran",
    recentActivities: "Aktivitas Terbaru",
    userGrowth: "Pertumbuhan Pengguna",
    systemHealth: "Kesehatan Sistem",
  },

  // Users
  users: {
    title: "Manajemen Pengguna",
    addUser: "Tambah Pengguna",
    editUser: "Ubah Pengguna",
    deleteUser: "Hapus Pengguna",
    userList: "Daftar Pengguna",
    name: "Nama",
    email: "Email",
    roles: "Peran",
    status: "Status",
    createdAt: "Dibuat Pada",
    filterByRole: "Filter berdasarkan Peran",
    userCreated: "Pengguna berhasil dibuat",
    userUpdated: "Pengguna berhasil diperbarui",
    userDeleted: "Pengguna berhasil dihapus",
    confirmDelete: "Apakah Anda yakin ingin menghapus pengguna ini?",
  },

  // Roles
  roles: {
    title: "Manajemen Peran",
    addRole: "Tambah Peran",
    editRole: "Ubah Peran",
    deleteRole: "Hapus Peran",
    roleName: "Nama Peran",
    permissions: "Izin",
    description: "Deskripsi",
    roleCreated: "Peran berhasil dibuat",
    roleUpdated: "Peran berhasil diperbarui",
    roleDeleted: "Peran berhasil dihapus",
  },

  // Themes
  themes: {
    light: "Terang",
    dark: "Gelap",
    system: "Sistem",
  },

  // Errors
  errors: {
    unauthorized: "Akses tidak diizinkan",
    forbidden: "Akses dilarang",
    notFound: "Halaman tidak ditemukan",
    serverError: "Kesalahan server internal",
    networkError: "Kesalahan koneksi jaringan",
    validationError: "Kesalahan validasi",
  },

  // Validation
  validation: {
    required: "Bidang ini wajib diisi",
    email: "Format email tidak valid",
    minLength: "Minimal {min} karakter diperlukan",
    maxLength: "Maksimal {max} karakter diizinkan",
    passwordMismatch: "Kata sandi tidak cocok",
  },
};
```

#### 4. i18n Boot File

```javascript
// boot/i18n.js
import { boot } from "quasar/wrappers";
import { createI18n } from "vue-i18n";
import messages from "src/i18n";

const i18n = createI18n({
  locale: "en",
  fallbackLocale: "en",
  messages,
  legacy: false,
  globalInjection: true,
  missingWarn: false,
  fallbackWarn: false,
});

export default boot(({ app }) => {
  app.use(i18n);
});

export { i18n };
```

#### 5. Usage in Components

```vue
<template>
  <div>
    <!-- Basic translation -->
    <h1>{{ $t("dashboard.title") }}</h1>

    <!-- Translation with parameters -->
    <p>{{ $t("validation.minLength", { min: 8 }) }}</p>

    <!-- Conditional translation -->
    <q-btn :label="$t(loading ? 'common.loading' : 'common.save')" />

    <!-- Pluralization -->
    <span>{{ $tc("users.count", userCount) }}</span>
  </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";

const { t, tc } = useI18n();

// Usage in script
const getMessage = () => {
  return t("common.confirm");
};
</script>
```

#### 6. Language-aware Composables

```javascript
// composables/useLocale.js
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { useLanguageStore } from "stores/language";

export function useLocale() {
  const { t, tc, locale } = useI18n();
  const languageStore = useLanguageStore();

  const currentLocale = computed(() => locale.value);
  const isRTL = computed(() => ["ar", "he", "fa"].includes(locale.value));

  const formatDate = (date, options = {}) => {
    return new Intl.DateTimeFormat(locale.value, {
      year: "numeric",
      month: "long",
      day: "numeric",
      ...options,
    }).format(new Date(date));
  };

  const formatNumber = (number, options = {}) => {
    return new Intl.NumberFormat(locale.value, options).format(number);
  };

  const formatCurrency = (amount, currency = "USD") => {
    return new Intl.NumberFormat(locale.value, {
      style: "currency",
      currency,
    }).format(amount);
  };

  return {
    t,
    tc,
    currentLocale,
    isRTL,
    formatDate,
    formatNumber,
    formatCurrency,
    setLocale: languageStore.setLocale,
  };
}
```

## Integration in Main Layout

```vue
<!-- layouts/MainLayout.vue -->
<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Header -->
    <q-header elevated class="header">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          {{ $t("nav.dashboard") }}
        </q-toolbar-title>

        <q-space />

        <!-- Language Switcher -->
        <LanguageSwitcher />

        <!-- Theme Toggle -->
        <ThemeToggle />

        <!-- Profile Menu -->
        <ProfileMenu />
      </q-toolbar>
    </q-header>

    <!-- Sidebar -->
    <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="sidebar">
      <NavigationMenu />
    </q-drawer>

    <!-- Main Content -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useThemeStore } from "stores/theme";
import { useLanguageStore } from "stores/language";
import LanguageSwitcher from "components/LanguageSwitcher.vue";
import ThemeToggle from "components/ThemeToggle.vue";
import ProfileMenu from "components/ProfileMenu.vue";
import NavigationMenu from "components/NavigationMenu.vue";

const leftDrawerOpen = ref(false);
const themeStore = useThemeStore();
const languageStore = useLanguageStore();

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

onMounted(() => {
  // Initialize theme and language
  themeStore.initializeTheme();
  languageStore.initializeLocale();
});
</script>
```

## Best Practices

### 1. Theme Management

- Always use CSS custom properties untuk konsistensi
- Implement smooth transitions antara themes
- Test semua components di kedua light dan dark mode
- Provide fallback values untuk custom properties

### 2. Internationalization

- Gunakan namespace untuk organize translations
- Implement lazy loading untuk translation files
- Test aplikasi dengan text yang panjang (German, Arabic)
- Provide context untuk translator dengan comments

### 3. Performance

- Cache theme dan language preferences
- Minimize DOM manipulations saat theme switching
- Use efficient translation key structures
- Implement translation preloading untuk critical paths
