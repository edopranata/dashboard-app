<template>
  <q-page class="dashboard-page">
    <!-- Welcome Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="welcome-section">
          <h4 class="page-title">{{ $t('dashboard.welcome', { name: authStore.userName }) }}</h4>
          <p class="page-subtitle">{{ $t('dashboard.subtitle') }}</p>
        </div>
        <div class="header-actions">
          <q-btn color="positive" icon="notifications" :label="$t('dashboard.testNotification')"
            @click="testNotification" class="q-mr-sm" outline />
          <q-btn color="primary" icon="add" :label="$t('dashboard.quickAction')"
            @click="showQuickActionDialog = true" />
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
      <div class="row q-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card stat-card--primary">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="people" size="40px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.totalUsers }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.totalUsers') }}</div>
                <div class="stat-change stat-change--positive">
                  <q-icon name="trending_up" size="16px" />
                  +{{ growthMetrics.userGrowth.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card stat-card--success">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="admin_panel_settings" size="40px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.totalRoles }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.totalRoles') }}</div>
                <div class="stat-change"
                  :class="growthMetrics.roleUsage.percentage > 0 ? 'stat-change--positive' : 'stat-change--neutral'">
                  <q-icon :name="growthMetrics.roleUsage.percentage > 0 ? 'trending_up' : 'remove'" size="16px" />
                  {{ growthMetrics.roleUsage.percentage > 0 ? '+' : '' }}{{ growthMetrics.roleUsage.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card stat-card--warning">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="login" size="40px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.activeUsers }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.activeToday') }}</div>
                <div class="stat-change stat-change--positive">
                  <q-icon name="trending_up" size="16px" />
                  +{{ growthMetrics.activeUsers.percentage }}%
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card stat-card--info">
            <q-card-section class="stat-content">
              <div class="stat-icon">
                <q-icon name="security" size="40px" />
              </div>
              <div class="stat-details">
                <div class="stat-value">{{ stats.permissions }}</div>
                <div class="stat-label">{{ $t('dashboard.stats.permissions') }}</div>
                <div class="stat-change stat-change--neutral">
                  <q-icon name="remove" size="16px" />
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
      <div class="row q-gutter-md">
        <!-- Recent Activity -->
        <div class="col-12 col-lg-8">
          <q-card class="content-card">
            <q-card-section class="card-header">
              <div class="card-title">
                <q-icon name="history" class="q-mr-sm" />
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

            <q-card-section class="q-pt-none">
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

        <!-- Quick Actions -->
        <div class="col-12 col-lg-4">
          <q-card class="content-card">
            <q-card-section class="card-header">
              <div class="card-title">
                <q-icon name="bolt" class="q-mr-sm" />
                Quick Actions
              </div>
            </q-card-section>

            <q-card-section class="q-pt-none">
              <div class="quick-actions">
                <q-btn v-if="authStore.hasPermission('create_users')" class="quick-action-btn" color="primary"
                  icon="person_add" :label="$t('dashboard.addUser')" :to="{ name: 'users.create' }" no-caps outline />
                <q-btn v-if="authStore.hasPermission('create_roles')" class="quick-action-btn" color="secondary"
                  icon="add_moderator" :label="$t('dashboard.addRole')" :to="{ name: 'roles.create' }" no-caps outline />
                <q-btn v-if="authStore.hasPermission('view_users')" class="quick-action-btn" color="positive"
                  icon="manage_accounts" :label="$t('dashboard.manageUsers')" :to="{ name: 'users.index' }" no-caps outline />
                <q-btn class="quick-action-btn" color="info" icon="settings" :label="$t('dashboard.settings')" :to="{ name: 'profile' }"
                  no-caps outline />
              </div>
            </q-card-section>
          </q-card>

          <!-- System Status -->
          <q-card class="content-card q-mt-md">
            <q-card-section class="card-header">
              <div class="card-title">
                <q-icon name="monitor_heart" class="q-mr-sm" />
                {{ $t('dashboard.systemStatus') }}
              </div>
            </q-card-section>

            <q-card-section class="q-pt-none">
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

    <!-- Quick Action Dialog -->
    <q-dialog v-model="showQuickActionDialog">
      <q-card style="min-width: 400px">
        <q-card-section class="card-header">
          <div class="card-title">Quick Actions</div>
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
            <q-btn class="quick-dialog-btn" color="warning" icon="psychology" :label="$t('dashboard.testDialog')" no-caps
              @click="testDialog" />
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
import { useAuthStore } from 'src/stores/auth'
import { api } from 'src/boot/axios'

// Composables
const $q = useQuasar()
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
      { label: 'Dismiss', color: 'white', handler: () => { } }
    ]
  })
}

const testDialog = () => {
  showQuickActionDialog.value = false

  $q.dialog({
    title: 'Test Dialog',
    message: 'This is a test dialog to verify the Dialog plugin is working correctly!',
    persistent: true,
    ok: {
      push: true,
      label: 'Awesome!',
      color: 'primary'
    },
    cancel: {
      push: true,
      color: 'negative',
      label: 'Close'
    }
  }).onOk(() => {
    $q.notify({
      type: 'positive',
      message: 'Dialog confirmed!',
      icon: 'thumb_up'
    })
  }).onCancel(() => {
    $q.notify({
      type: 'info',
      message: 'Dialog cancelled',
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
.dashboard-page {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

// Page Header
.page-header {
  margin-bottom: 2rem;

  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;

    .welcome-section {
      .page-title {
        margin: 0 0 0.5rem 0;
        font-weight: 600;
        color: #2d3748;
      }

      .page-subtitle {
        margin: 0;
        color: #718096;
        font-size: 1rem;
      }
    }

    .header-actions {
      flex-shrink: 0;
    }
  }
}

// Stats Section
.stats-section {
  margin-bottom: 2rem;

  .stat-card {
    height: 120px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-content {
      display: flex;
      align-items: center;
      gap: 1rem;
      height: 100%;
      padding: 1rem;
    }

    .stat-icon {
      flex-shrink: 0;
      padding: 0.75rem;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .stat-details {
      flex: 1;

      .stat-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
      }

      .stat-label {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
      }

      .stat-change {
        display: flex;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 500;

        &--positive {
          color: #22c55e;
        }

        &--neutral {
          color: #718096;
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

// Content Cards
.content-card {
  border: none;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 0;

    .card-title {
      display: flex;
      align-items: center;
      font-size: 1.1rem;
      font-weight: 600;
      color: #2d3748;
    }
  }
}

// Activity List
.activity-list {
  .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;

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
      }

      .activity-time {
        font-size: 0.8rem;
        color: #718096;
      }
    }
  }
}

// Quick Actions
.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;

  .quick-action-btn {
    width: 100%;
    justify-content: flex-start;
    padding: 0.75rem 1rem;
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
  }
}

// System Status
.system-status {
  .status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;

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
      }

      .status-value {
        font-size: 0.875rem;
        color: #718096;
      }
    }
  }
}

// Dark mode styles
.body--dark {
  .page-header {
    .header-content {
      .welcome-section {
        .page-title {
          color: #f7fafc;
        }

        .page-subtitle {
          color: #a0aec0;
        }
      }
    }
  }

  .content-card {
    .card-header {
      .card-title {
        color: #f7fafc;
      }
    }
  }

  .activity-list {
    .activity-item {
      border-bottom-color: #4a5568;

      .activity-content {
        .activity-text {
          color: #f7fafc;
        }

        .activity-time {
          color: #a0aec0;
        }
      }
    }
  }

  .system-status {
    .status-item {
      border-bottom-color: #4a5568;

      .status-details {
        .status-label {
          color: #f7fafc;
        }

        .status-value {
          color: #a0aec0;
        }
      }
    }
  }
}

// Responsive styles
@media (max-width: 1023px) {
  .dashboard-page {
    padding: 1rem;
  }

  .page-header {
    .header-content {
      flex-direction: column;
      align-items: stretch;

      .header-actions {
        align-self: flex-start;
      }
    }
  }
}

@media (max-width: 599px) {
  .dashboard-page {
    padding: 0.75rem;
  }

  .stats-section {
    .stat-card {
      .stat-content {
        padding: 0.75rem;
      }
    }
  }

  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
