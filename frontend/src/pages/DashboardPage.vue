<template>
  <q-page class="container padded">
    <!-- Welcome Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t('dashboard.welcome', { name: authStore.userName }) }}</h4>
          <p class="page-subtitle">{{ $t('dashboard.subtitle') }}</p>
        </div>
        <div class="header-actions">
          <q-btn color="positive" icon="notifications" :label="$t('dashboard.testNotification')"
            @click="testNotification" class="action-btn" outline />
          <q-btn color="primary" icon="add" :label="$t('dashboard.quickAction')" @click="showQuickActionDialog = true"
            class="action-btn" />
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
      <div class="row q-col-gutter-lg">
        <div class="col-12 col-sm-6 col-lg-3">
          <q-card class="stat-card stat-card--primary">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="people" size="32px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.totalUsers }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.totalUsers') }}</div>
                <div class="stat-change stat-change--positive">
                  <q-icon name="trending_up" size="14px" />
                  +{{ growthMetrics.userGrowth.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <q-card class="stat-card stat-card--success">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="admin_panel_settings" size="32px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.totalRoles }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.totalRoles') }}</div>
                <div class="stat-change"
                  :class="growthMetrics.roleUsage.percentage > 0 ? 'stat-change--positive' : 'stat-change--neutral'">
                  <q-icon :name="growthMetrics.roleUsage.percentage > 0 ? 'trending_up' : 'remove'" size="14px" />
                  {{ growthMetrics.roleUsage.percentage > 0 ? '+' : '' }}{{ growthMetrics.roleUsage.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <q-card class="stat-card stat-card--warning">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="login" size="32px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.activeUsers }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.activeToday') }}</div>
                <div class="stat-change stat-change--positive">
                  <q-icon name="trending_up" size="14px" />
                  +{{ growthMetrics.activeUsers.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <q-card class="stat-card stat-card--info">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="security" size="32px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.permissions }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.permissions') }}</div>
                <div class="stat-change stat-change--neutral">
                  <q-icon name="remove" size="14px" />
                  0%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
      <div class="row q-col-gutter-lg">
        <!-- Recent Activity -->
        <div class="col-12 col-lg-8">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div class="section-title">
                <q-icon name="history" />
                {{ $t('dashboard.recentActivity') }}
              </div>
              <q-btn flat icon="more_vert" round size="sm">
                <q-menu>
                  <q-list>
                    <q-item clickable v-close-popup>
                      <q-item-section>{{ $t('actions.refresh') }}</q-item-section>
                    </q-item>
                    <q-item clickable v-close-popup>
                      <q-item-section>{{ $t('actions.export') }}</q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
            </q-card-section>

            <q-card-section>
              <div class="activity-list">
                <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
                  <div class="activity-avatar">
                    <q-avatar :color="activity.color" text-color="white" size="36px">
                      <q-icon :name="activity.icon" />
                    </q-avatar>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">{{ activity.text }}</div>
                    <div class="activity-time">{{ activity.time }}</div>
                  </div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Right Sidebar -->
        <div class="col-12 col-lg-4">
          <div class="sidebar-content">
            <!-- Quick Actions -->
            <q-card flat bordered class="content-card">
              <q-card-section class="card-header">
                <div class="section-title">
                  <q-icon name="bolt" />
                  {{ $t('dashboard.quickActions') }}
                </div>
              </q-card-section>

              <q-card-section>
                <div class="quick-actions">
                  <q-btn v-if="authStore.hasPermission('create_users')" class="quick-action-btn" color="primary"
                    icon="person_add" :label="$t('dashboard.addUser')" :to="{ name: 'users.create' }" no-caps outline />
                  <q-btn v-if="authStore.hasPermission('create_roles')" class="quick-action-btn" color="secondary"
                    icon="add_moderator" :label="$t('dashboard.addRole')" :to="{ name: 'roles.create' }" no-caps
                    outline />
                  <q-btn v-if="authStore.hasPermission('view_users')" class="quick-action-btn" color="positive"
                    icon="manage_accounts" :label="$t('dashboard.manageUsers')" :to="{ name: 'users.index' }" no-caps
                    outline />
                  <q-btn class="quick-action-btn" color="info" icon="settings" :label="$t('dashboard.settings')"
                    :to="{ name: 'profile' }" no-caps outline />
                </div>
              </q-card-section>
            </q-card>

            <!-- System Status -->
            <q-card flat bordered class="content-card q-mt-lg">
              <q-card-section class="card-header">
                <div class="section-title">
                  <q-icon name="monitor_heart" />
                  {{ $t('dashboard.systemStatus') }}
                </div>
              </q-card-section>

              <q-card-section>
                <div class="system-status">
                  <div class="status-item">
                    <div class="status-indicator status-indicator--online"></div>
                    <div class="status-details">
                      <div class="status-label">{{ $t('dashboard.database') }}</div>
                      <div class="status-value">{{ $t('dashboard.online') }}</div>
                    </div>
                  </div>
                  <div class="status-item">
                    <div class="status-indicator status-indicator--online"></div>
                    <div class="status-details">
                      <div class="status-label">{{ $t('dashboard.apiServer') }}</div>
                      <div class="status-value">{{ $t('dashboard.online') }}</div>
                    </div>
                  </div>
                  <div class="status-item">
                    <div class="status-indicator status-indicator--warning"></div>
                    <div class="status-details">
                      <div class="status-label">{{ $t('dashboard.cache') }}</div>
                      <div class="status-value">{{ $t('dashboard.warning') }}</div>
                    </div>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Action Dialog -->
    <q-dialog v-model="showQuickActionDialog">
      <q-card style="min-width: 400px">
        <q-card-section class="card-header">
          <div class="card-title">{{ $t('dashboard.quickActions') }}</div>
        </q-card-section>

        <q-card-section>
          <div class="quick-actions-grid">
            <q-btn v-if="authStore.hasPermission('create_users')" class="quick-dialog-btn" color="primary"
              icon="person_add" :label="$t('dashboard.addNewUser')" :to="{ name: 'users.create' }" no-caps
              @click="showQuickActionDialog = false" />
            <q-btn v-if="authStore.hasPermission('create_roles')" class="quick-dialog-btn" color="secondary"
              icon="add_moderator" :label="$t('dashboard.createRole')" :to="{ name: 'roles.create' }" no-caps
              @click="showQuickActionDialog = false" />
            <q-btn class="quick-dialog-btn" color="positive" icon="analytics" :label="$t('dashboard.viewAnalytics')"
              :to="{ name: 'analytics' }" no-caps @click="showQuickActionDialog = false" />
            <q-btn class="quick-dialog-btn" color="info" icon="settings" :label="$t('dashboard.systemSettings')"
              :to="{ name: 'settings' }" no-caps @click="showQuickActionDialog = false" />
            <q-btn class="quick-dialog-btn" color="warning" icon="psychology" :label="$t('dashboard.testDialog')"
              no-caps @click="testDialog" />
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat :label="$t('actions.cancel')" color="grey" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from 'src/stores/auth'
import { api } from 'src/boot/axios'

// Composables
const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// Reactive data
const showQuickActionDialog = ref(false)
const stats = ref({
  totalUsers: 0,
  totalRoles: 0,
  activeUsers: 0,
  permissions: 0
})

const growthMetrics = ref({
  userGrowth: { percentage: 12 },
  roleUsage: { percentage: 0 },
  activeUsers: { percentage: 5 },
  permissions: { percentage: 0 }
})

const recentActivities = ref([
  {
    id: 1,
    icon: 'person_add',
    color: 'primary',
    text: 'New user "John Doe" was created',
    time: '2 minutes ago'
  },
  {
    id: 2,
    icon: 'edit',
    color: 'secondary',
    text: 'User "Jane Smith" updated their profile',
    time: '1 hour ago'
  },
  {
    id: 3,
    icon: 'admin_panel_settings',
    color: 'positive',
    text: 'Role "Manager" permissions updated',
    time: '3 hours ago'
  },
  {
    id: 4,
    icon: 'login',
    color: 'info',
    text: 'User "Admin" logged in',
    time: '5 hours ago'
  },
  {
    id: 5,
    icon: 'security',
    color: 'warning',
    text: 'Security settings updated',
    time: '1 day ago'
  }
])

// Methods
const fetchDashboardStats = async () => {
  // Show loading
  $q.loading.show({
    message: 'Loading dashboard stats...',
    boxClass: 'bg-grey-2 text-grey-9',
    spinnerColor: 'primary'
  })

  try {
    const response = await api.get('/dashboard/stats')

    if (response.data.success) {
      // Use the data from the API response
      stats.value = {
        totalUsers: response.data.data.totalUsers,
        totalRoles: response.data.data.totalRoles,
        activeUsers: response.data.data.activeUsers,
        permissions: response.data.data.permissions
      }

      // Update growth metrics from API if available
      if (response.data.data.growthMetrics) {
        growthMetrics.value = {
          userGrowth: { percentage: response.data.data.growthMetrics.userGrowth.percentage },
          roleUsage: { percentage: response.data.data.growthMetrics.roleUsage.percentage },
          activeUsers: { percentage: response.data.data.growthMetrics.activeUsers.percentage },
          permissions: { percentage: 0 } // Static for now
        }
      }

      // Update recent activities from API if available
      if (response.data.data.recentActivity && response.data.data.recentActivity.length > 0) {
        recentActivities.value = response.data.data.recentActivity
      }

      $q.notify({
        type: 'positive',
        message: 'Dashboard stats loaded successfully!',
        icon: 'check_circle',
        position: 'top-right',
        timeout: 2000
      })
    } else {
      throw new Error(response.data.message || 'Failed to load dashboard stats')
    }
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)

    $q.notify({
      type: 'negative',
      message: 'Failed to load dashboard stats. Using fallback data.',
      icon: 'error',
      position: 'top-right',
      timeout: 3000
    })

    // Fallback to mock data
    stats.value = {
      totalUsers: 156,
      totalRoles: 8,
      activeUsers: 42,
      permissions: 24
    }
  } finally {
    // Hide loading
    $q.loading.hide()
  }
}

const testNotification = () => {
  const notifications = [
    { type: 'positive', message: 'Success! This is a positive notification', icon: 'check_circle' },
    { type: 'negative', message: 'Error! This is a negative notification', icon: 'error' },
    { type: 'warning', message: 'Warning! This is a warning notification', icon: 'warning' },
    { type: 'info', message: 'Info! This is an info notification', icon: 'info' }
  ]

  const randomNotif = notifications[Math.floor(Math.random() * notifications.length)]

  $q.notify({
    type: randomNotif.type,
    message: randomNotif.message,
    icon: randomNotif.icon,
    position: 'top-right',
    timeout: 3000,
    actions: [
      { label: t('common.ok'), color: 'white', handler: () => { } }
    ]
  })
}

const testDialog = () => {
  showQuickActionDialog.value = false

  $q.dialog({
    title: t('dashboard.testDialog'),
    message: t('messages.info'),
    persistent: true,
    ok: {
      push: true,
      label: t('common.ok'),
      color: 'primary'
    },
    cancel: {
      push: true,
      color: 'negative',
      label: t('common.cancel')
    }
  }).onOk(() => {
    $q.notify({
      type: 'positive',
      message: t('messages.success'),
      icon: 'thumb_up'
    })
  }).onCancel(() => {
    $q.notify({
      type: 'info',
      message: t('common.cancel'),
      icon: 'info'
    })
  })
}

// Lifecycle
onMounted(() => {
  fetchDashboardStats()
})
</script>

<style lang="scss" scoped>
// Dashboard specific styles - using global classes where possible

// Stats Cards - enhanced styling beyond global classes
.stats-section {
  margin-bottom: 2rem;

  .stat-card {
    height: 120px;
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    background: white;
    transition: all 0.3s ease;

    .body--dark & {
      background: #2d3748;
      border-color: rgba(255, 255, 255, 0.1);
    }

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    }

    .stat-content {
      display: flex;
      align-items: center;
      gap: 1rem;
      height: 100%;
      padding: 1.25rem;
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .stat-details {
      flex: 1;
      min-width: 0; // Prevent text overflow

      .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
        color: #2d3748;

        .body--dark & {
          color: #f7fafc;
        }
      }

      .stat-label {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        .body--dark & {
          color: #a0aec0;
        }
      }

      .stat-change {
        display: flex;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 500;

        &--positive {
          color: #22c55e;
        }

        &--neutral {
          color: #718096;

          .body--dark & {
            color: #a0aec0;
          }
        }
      }
    }

    &--primary {
      .stat-icon {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
      }
    }

    &--success {
      .stat-icon {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
      }
    }

    &--warning {
      .stat-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
      }
    }

    &--info {
      .stat-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
      }
    }
  }
}

// Content Grid Layout
.content-grid {
  .sidebar-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
}

// Activity List - specific styling
.activity-list {
  .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;

    .body--dark & {
      border-bottom-color: #4a5568;
    }

    &:last-child {
      border-bottom: none;
    }

    .activity-avatar {
      flex-shrink: 0;
    }

    .activity-content {
      flex: 1;

      .activity-text {
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #2d3748;

        .body--dark & {
          color: #f7fafc;
        }
      }

      .activity-time {
        font-size: 0.8rem;
        color: #718096;

        .body--dark & {
          color: #a0aec0;
        }
      }
    }
  }
}

// Quick Actions - specific styling
.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;

  .quick-action-btn {
    width: 100%;
    justify-content: flex-start;
    padding: 0.75rem 1rem;
    border-radius: 12px;
  }
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;

  .quick-dialog-btn {
    width: 100%;
    padding: 1rem;
    flex-direction: column;
    gap: 0.5rem;
    border-radius: 12px;
  }
}

// System Status - specific styling
.system-status {
  .status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;

    .body--dark & {
      border-bottom-color: #4a5568;
    }

    &:last-child {
      border-bottom: none;
    }

    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      flex-shrink: 0;

      &--online {
        background: #22c55e;
      }

      &--warning {
        background: #f59e0b;
      }

      &--offline {
        background: #ef4444;
      }
    }

    .status-details {
      flex: 1;
      display: flex;
      justify-content: space-between;

      .status-label {
        font-weight: 500;
        color: #2d3748;

        .body--dark & {
          color: #f7fafc;
        }
      }

      .status-value {
        font-size: 0.875rem;
        color: #718096;

        .body--dark & {
          color: #a0aec0;
        }
      }
    }
  }
}

// Responsive styles
@media (max-width: 1023px) {
  .content-grid {
    .sidebar-content {
      margin-top: 1rem;
    }
  }
}

@media (max-width: 599px) {
  .stats-section {
    .stat-card {
      height: auto;
      min-height: 100px;

      .stat-content {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
      }

      .stat-icon {
        width: 40px;
        height: 40px;
      }

      .stat-details {
        .stat-value {
          font-size: 1.5rem;
        }

        .stat-label {
          white-space: normal;
          text-align: center;
        }
      }
    }
  }

  .quick-actions-grid {
    grid-template-columns: 1fr;
  }

  .content-grid {
    .sidebar-content {
      gap: 1rem;
    }
  }
}
</style>
