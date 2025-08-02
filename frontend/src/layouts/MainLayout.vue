<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Header -->
    <q-header elevated :class="$q.dark.isActive ? 'bg-dark text-white header-dark' : 'bg-white text-dark header-light'"
      height-hint="64" style="transition: background-color 0.3s ease, color 0.3s ease;">
      <q-toolbar class="q-px-md">
        <!-- Always visible menu toggle button with animation -->
        <q-btn flat dense round @click="toggleLeftDrawer" class="menu-toggle-btn q-mr-md"
          :class="{ 'state-changing': isToggling }" aria-label="Toggle Menu">
          <q-icon :name="leftDrawerOpen ? 'menu_open' : 'menu'" size="24px" class="menu-icon"
            :class="{ 'icon-rotated': leftDrawerOpen }" />
          <q-tooltip>{{ leftDrawerOpen ? 'Close Sidebar' : 'Open Sidebar' }}</q-tooltip>
        </q-btn>

        <!-- Logo/Brand -->
        <div class="header-brand">
          <q-icon name="dashboard_customize" size="32px" color="primary" class="q-mr-sm" />
          <div class="brand-text">
            <div class="brand-title">{{ $t('app.dashboard') }}</div>
            <div class="brand-subtitle">{{ $t('app.management') }}</div>
          </div>
        </div>

        <q-space />

        <!-- Header Actions -->
        <div class="header-actions">
          <!-- Theme Toggle -->
          <q-btn flat round dense :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'" @click="toggleTheme"
            class="q-mr-xs">
            <q-tooltip>Toggle {{ $q.dark.isActive ? 'Light' : 'Dark' }} Mode</q-tooltip>
          </q-btn>

          <!-- Language Switcher -->
          <LanguageSwitcher class="q-mr-xs" />

          <!-- Notifications -->
          <q-btn flat round dense icon="notifications" class="q-mr-xs">
            <q-badge color="red" floating>3</q-badge>
            <q-tooltip>Notifications</q-tooltip>
          </q-btn>

          <!-- Profile Menu -->
          <q-btn flat round dense class="profile-btn">
            <q-avatar size="32px" :color="userAvatar ? 'transparent' : 'primary'" text-color="white">
              <img v-if="userAvatar" :src="userAvatar" :alt="authStore.userName + ' avatar'" />
              <span v-else>{{ userInitials }}</span>
            </q-avatar>
            <q-tooltip>Profile</q-tooltip>
            <q-menu>
              <q-list style="min-width: 300px">
                <q-item>
                  <q-item-section avatar>
                    <q-avatar size="40px" :color="userAvatar ? 'transparent' : 'primary'" text-color="white">
                      <img v-if="userAvatar" :src="userAvatar" :alt="authStore.userName + ' avatar'" />
                      <span v-else>{{ userInitials }}</span>
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ authStore.userName }}</q-item-label>
                    <q-item-label caption>{{ authStore.userEmail }}</q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-item clickable :to="{ name: 'profile' }" v-close-popup>
                  <q-item-section avatar>
                    <q-icon name="person" />
                  </q-item-section>
                  <q-item-section>Profile</q-item-section>
                </q-item>

                <q-item clickable v-close-popup>
                  <q-item-section avatar>
                    <q-icon name="settings" />
                  </q-item-section>
                  <q-item-section>Settings</q-item-section>
                </q-item>

                <q-separator />

                <q-item clickable @click="handleLogout" v-close-popup>
                  <q-item-section avatar>
                    <q-icon name="logout" />
                  </q-item-section>
                  <q-item-section>Logout</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <!-- Sidebar Drawer -->
    <q-drawer v-model="leftDrawerOpen" show-if-above :width="320" :breakpoint="1024" bordered class="sidebar-drawer">
      <!-- Sidebar Header -->
      <div class="sidebar-header">
        <div class="user-info">
          <q-avatar size="48px" :color="userAvatar ? 'transparent' : 'primary'" text-color="white" class="q-mb-sm">
            <img v-if="userAvatar" :src="userAvatar" :alt="authStore.userName + ' avatar'" />
            <span v-else>{{ userInitials }}</span>
          </q-avatar>
          <div class="user-details">
            <div class="user-name">{{ authStore.userName }}</div>
            <div class="user-role">{{ authStore.userRoles.join(', ') }}</div>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <q-list class="navigation-menu">
        <!-- Dashboard -->
        <q-item clickable :to="{ name: 'dashboard' }" exact class="nav-item">
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('nav.dashboard') }}</q-item-label>
          </q-item-section>
        </q-item>

        <!-- User Management -->
        <q-expansion-item v-if="authStore.hasPermission('view_users')" icon="people" :label="$t('nav.users')"
          class="nav-expansion">
          <q-item clickable :to="{ name: 'users.index' }" class="nav-sub-item">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('nav.allUsers') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-item v-if="authStore.hasPermission('create_users')" clickable :to="{ name: 'users.create' }"
            class="nav-sub-item">
            <q-item-section avatar>
              <q-icon name="person_add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('nav.addUser') }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <!-- Role Management -->
        <q-expansion-item v-if="authStore.hasPermission('view_roles')" icon="admin_panel_settings"
          :label="$t('nav.roles')" class="nav-expansion">
          <q-item clickable :to="{ name: 'roles.index' }" class="nav-sub-item">
            <q-item-section avatar>
              <q-icon name="list" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('nav.allRoles') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-item v-if="authStore.hasPermission('create_roles')" clickable :to="{ name: 'roles.create' }"
            class="nav-sub-item">
            <q-item-section avatar>
              <q-icon name="add" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ $t('nav.addRole') }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-expansion-item>

        <!-- Analytics -->
        <q-item v-if="authStore.hasPermission('view_analytics')" clickable :to="{ name: 'analytics' }" class="nav-item">
          <q-item-section avatar>
            <q-icon name="analytics" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('nav.analytics') }}</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md" />

        <!-- Settings -->
        <q-item v-if="authStore.hasPermission('manage_settings')" clickable :to="{ name: 'settings' }" class="nav-item">
          <q-item-section avatar>
            <q-icon name="settings" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('nav.settings') }}</q-item-label>
          </q-item-section>
        </q-item>

        <!-- System Logs -->
        <q-item v-if="authStore.hasPermission('view_logs')" clickable :to="{ name: 'logs' }" class="nav-item">
          <q-item-section avatar>
            <q-icon name="description" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('nav.systemLogs') }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>

      <!-- Sidebar Footer -->
      <div class="sidebar-footer">
        <q-separator />
        <div class="footer-content">
          <div class="app-version">
            <q-icon name="info" size="16px" class="q-mr-xs" />
            Version 1.0.0
          </div>
        </div>
      </div>
    </q-drawer>

    <!-- Main Content -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth'
import LanguageSwitcher from 'src/components/LanguageSwitcher.vue'

// Composables
const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const leftDrawerOpen = ref(false)
const isToggling = ref(false)

// Computed
const userInitials = computed(() => {
  if (!authStore.userName) return '?'
  return authStore.userName
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const userAvatar = computed(() => {
  // Check localStorage first for demo avatar
  const savedAvatar = localStorage.getItem('userAvatar')
  if (savedAvatar) {
    return savedAvatar
  }

  // Then check auth store
  return authStore.user?.avatar || null
})

// Methods
const toggleLeftDrawer = () => {
  isToggling.value = true
  leftDrawerOpen.value = !leftDrawerOpen.value

  // Remove animation class after animation completes
  setTimeout(() => {
    isToggling.value = false
  }, 600)
}

const toggleTheme = () => {
  $q.dark.toggle()
  // Save theme preference to localStorage
  localStorage.setItem('darkMode', $q.dark.isActive)
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/auth/login')
}

// Lifecycle
onMounted(() => {
  // Load theme preference from localStorage
  const savedTheme = localStorage.getItem('darkMode')
  if (savedTheme !== null) {
    $q.dark.set(savedTheme === 'true')
  }

  // Initialize auth if needed
  if (authStore.token && !authStore.user) {
    authStore.initAuth()
  }
})
</script>

<style lang="scss" scoped>
// Header styles
.q-header {
  transition: background-color 0.3s ease, color 0.3s ease;

  &.header-light {
    background-color: #ffffff !important;
    color: #2d3748 !important;

    .q-btn {
      color: #4a5568;

      &:hover {
        background-color: rgba(0, 0, 0, 0.04);
      }
    }
  }

  &.header-dark {
    background-color: #1a202c !important;
    color: #f7fafc !important;

    .q-btn {
      color: #e2e8f0;

      &:hover {
        background-color: rgba(255, 255, 255, 0.08);
      }
    }
  }
}

// Menu toggle button with animation
.menu-toggle-btn {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 50%;
  position: relative;
  overflow: hidden;

  // Ripple effect background
  &::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(102, 126, 234, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translate(-50%, -50%);
    z-index: 0;
  }

  .menu-icon {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;

    &.icon-rotated {
      transform: rotate(180deg) scale(1.1);
      color: #667eea;
    }
  }

  &:hover {
    transform: scale(1.05);

    &::before {
      width: 100%;
      height: 100%;
    }

    .menu-icon {
      transform: scale(1.1);

      &.icon-rotated {
        transform: rotate(180deg) scale(1.2);
      }
    }
  }

  &:active {
    transform: scale(0.95);

    .menu-icon {
      transform: scale(0.9);

      &.icon-rotated {
        transform: rotate(180deg) scale(0.9);
      }
    }
  }

  // Pulse animation when sidebar state changes
  &.state-changing {
    animation: pulse 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
  }
}

// Dark mode specific animations
.body--dark {
  .menu-toggle-btn {
    &::before {
      background: rgba(255, 255, 255, 0.1);
    }

    .menu-icon {
      &.icon-rotated {
        color: #e2e8f0;
      }
    }

    &.state-changing {
      animation: pulse-dark 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
  }
}

@keyframes pulse-dark {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.3);
  }

  70% {
    box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
  }

  100% {
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
  }
}

.header-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;

  .brand-text {
    .brand-title {
      font-size: 1.2rem;
      font-weight: 700;
      line-height: 1;
      color: currentColor;
      opacity: 0.9;
      transition: color 0.3s ease, opacity 0.3s ease;
    }

    .brand-subtitle {
      font-size: 0.75rem;
      color: currentColor;
      opacity: 0.6;
      line-height: 1;
      margin-top: 2px;
      transition: color 0.3s ease, opacity 0.3s ease;
    }
  }
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.25rem;

  .profile-btn {
    padding: 4px;
  }
}

// Sidebar styles
.sidebar-drawer {
  .sidebar-header {
    padding: 1.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;

    .user-info {
      text-align: center;

      .user-details {
        .user-name {
          font-weight: 600;
          font-size: 1rem;
          margin-bottom: 0.25rem;
        }

        .user-role {
          font-size: 0.8rem;
          opacity: 0.9;
        }
      }
    }
  }

  .navigation-menu {
    padding: 1rem 0;

    .nav-item {
      margin: 0 0.5rem;
      border-radius: 8px;

      &.q-router-link--exact-active {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;

        .q-icon {
          color: #667eea;
        }
      }

      &:hover {
        background-color: rgba(0, 0, 0, 0.04);
      }
    }

    .nav-expansion {
      margin: 0 0.5rem;

      :deep(.q-expansion-item__container) {
        .q-item {
          border-radius: 8px;
        }
      }
    }

    .nav-sub-item {
      margin: 0 0.5rem 0 2rem;
      border-radius: 8px;
      font-size: 0.9rem;

      &.q-router-link--active {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;

        .q-icon {
          color: #667eea;
        }
      }

      &:hover {
        background-color: rgba(0, 0, 0, 0.04);
      }
    }
  }

  .sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;

    .footer-content {
      padding: 1rem;
      text-align: center;

      .app-version {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #718096;
      }
    }
  }
}

// Dark mode styles
.body--dark {
  .sidebar-drawer {
    .navigation-menu {
      .nav-item {
        &:hover {
          background-color: rgba(255, 255, 255, 0.05);
        }
      }

      .nav-sub-item {
        &:hover {
          background-color: rgba(255, 255, 255, 0.05);
        }
      }
    }

    .sidebar-footer {
      .footer-content {
        .app-version {
          color: #a0aec0;
        }
      }
    }
  }
}

// Responsive styles
@media (max-width: 1023px) {
  .header-brand {
    .brand-text {
      display: none;
    }
  }
}

@media (max-width: 599px) {
  .header-actions {
    .q-btn {
      &:not(.profile-btn) {
        display: none;
      }
    }
  }
}

// Avatar image styles
.q-avatar {
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
  }
}
</style>
