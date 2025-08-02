<template>
  <q-page class="container padded">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t('users.userManagement') }}</h4>
          <p class="page-subtitle">{{ $t('users.userManagementDescription') }}</p>
        </div>
        <div class="header-actions">
          <q-btn color="primary" icon="add" :label="$t('users.addUser')" :to="{ name: 'users.create' }"
            :disable="!canCreateUsers" class="action-btn" />
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-section">
      <q-card flat class="filter-card">
        <q-card-section>
          <div class="filter-row">
            <div class="filter-group">
              <q-input v-model="filters.search" :placeholder="$t('users.searchUsers')" outlined dense clearable
                @input="debouncedSearch">
                <template v-slot:prepend>
                  <q-icon name="search" />
                </template>
              </q-input>
            </div>

            <div class="filter-group">
              <q-select v-model="filters.role" :options="roleOptions" :label="$t('users.filterByRole')" outlined dense
                emit-value map-options clearable @update:model-value="fetchUsers" />
            </div>

            <div class="filter-actions">
              <q-btn flat icon="refresh" @click="fetchUsers" :loading="loading" />
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Users Table -->
    <q-card flat bordered class="enhanced-table">
      <q-table :rows="users" :columns="columns" :loading="loading" :pagination="pagination" @request="onRequest"
        row-key="id" binary-state-sort :rows-per-page-options="[10, 25, 50]">
        <!-- Name column with avatar -->
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <div class="user-info">
              <q-avatar size="32px" color="transparent">
                <img :src="props.row.avatar" :alt="props.row.name" />
              </q-avatar>
              <div class="user-details">
                <div class="user-name">{{ props.row.name }}</div>
                <div class="user-email">{{ props.row.email }}</div>
              </div>
            </div>
          </q-td>
        </template>

        <!-- Roles column -->
        <template v-slot:body-cell-roles="props">
          <q-td :props="props">
            <div class="roles-list">
              <q-chip v-for="role in props.row.roles" :key="role.id" :color="getRoleColor(role.name)" text-color="white"
                size="sm" dense>
                {{ role.name }}
              </q-chip>
            </div>
          </q-td>
        </template>

        <!-- Status column -->
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <q-chip :color="props.row.email_verified_at ? 'green' : 'orange'" text-color="white" size="sm" dense>
              {{ props.row.email_verified_at ? 'Active' : 'Pending' }}
            </q-chip>
          </q-td>
        </template>

        <!-- Actions column -->
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <q-btn-group flat>
              <q-btn flat dense icon="visibility" color="primary" @click="viewUser(props.row)">
                <q-tooltip>View Details</q-tooltip>
              </q-btn>
              <q-btn flat dense icon="edit" color="orange" :to="{ name: 'users.edit', params: { id: props.row.id } }"
                v-if="canEditUsers">
                <q-tooltip>{{ $t('users.editUser') }}</q-tooltip>
              </q-btn>
              <q-btn flat dense icon="lock_reset" color="blue" @click="showResetPasswordDialog(props.row)"
                v-if="canEditUsers && !props.row.roles.some(role => role.name === 'Super Admin')">
                <q-tooltip>{{ $t('users.resetPassword') }}</q-tooltip>
              </q-btn>
              <q-btn flat dense icon="delete" color="red" @click="confirmDelete(props.row)"
                v-if="canDeleteUsers && !props.row.roles.some(role => role.name === 'Super Admin')">
                <q-tooltip>{{ $t('users.deleteUser') }}</q-tooltip>
              </q-btn>
            </q-btn-group>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- User Details Dialog -->
    <q-dialog v-model="showDetailsDialog">
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center">
          <div class="text-h6">{{ $t('users.userDetails') }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedUser">
          <div class="user-details-content">
            <div class="text-center q-mb-md">
              <q-avatar size="64px" color="transparent">
                <img :src="selectedUser.avatar" :alt="selectedUser.name" />
              </q-avatar>
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.name') }}:</strong> {{ selectedUser.name }}
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.email') }}:</strong> {{ selectedUser.email }}
            </div>
            <div class="detail-item" v-if="selectedUser.phone">
              <strong>{{ $t('users.phone') }}:</strong> {{ selectedUser.phone }}
            </div>
            <div class="detail-item" v-if="selectedUser.timezone">
              <strong>{{ $t('users.timezone') }}:</strong> {{ selectedUser.timezone }}
            </div>
            <div class="detail-item" v-if="selectedUser.bio">
              <strong>{{ $t('users.bio') }}:</strong> {{ selectedUser.bio }}
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.status') }}:</strong>
              <q-chip :color="selectedUser.email_verified_at ? 'green' : 'orange'" text-color="white" size="sm">
                {{ selectedUser.email_verified_at ? $t('users.active') : $t('users.pending') }}
              </q-chip>
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.roles') }}:</strong>
              <div class="roles-list q-mt-xs">
                <q-chip v-for="role in selectedUser.roles" :key="role.id" :color="getRoleColor(role.name)"
                  text-color="white" size="sm">
                  {{ role.name }}
                </q-chip>
              </div>
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.created') }}:</strong> {{ formatDate(selectedUser.created_at) }}
            </div>
            <div class="detail-item">
              <strong>{{ $t('users.lastUpdated') }}:</strong> {{ formatDate(selectedUser.updated_at) }}
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Reset Password Dialog -->
    <q-dialog v-model="showResetDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center">
          <q-icon name="lock_reset" color="blue" size="md" class="q-mr-sm" />
          <div class="text-h6">{{ $t('users.resetPassword') }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeResetDialog" />
        </q-card-section>

        <q-card-section v-if="selectedUserForReset">
          <div class="text-body2 q-mb-md">
            {{ $t('users.resetPasswordDescription', { name: selectedUserForReset.name }) }}
          </div>

          <q-form @submit="handleResetPassword" class="q-gutter-md">
            <q-input v-model="resetPasswordForm.password" :label="$t('users.newPassword')"
              :type="showNewPassword ? 'text' : 'password'" outlined :rules="[
                val => !!val || $t('users.validation.passwordRequired'),
                val => val.length >= 8 || $t('users.validation.passwordMinLength')
              ]" :error="hasResetError('password')" :error-message="getResetError('password')">
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
              <template v-slot:append>
                <q-btn flat round dense :icon="showNewPassword ? 'visibility_off' : 'visibility'"
                  @click="showNewPassword = !showNewPassword" />
              </template>
            </q-input>

            <q-input v-model="resetPasswordForm.password_confirmation" :label="$t('users.confirmNewPassword')"
              :type="showConfirmPassword ? 'text' : 'password'" outlined :rules="[
                val => !!val || $t('users.validation.confirmPasswordRequired'),
                val => val === resetPasswordForm.password || $t('users.validation.passwordsMismatch')
              ]" :error="hasResetError('password_confirmation')"
              :error-message="getResetError('password_confirmation')">
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
              <template v-slot:append>
                <q-btn flat round dense :icon="showConfirmPassword ? 'visibility_off' : 'visibility'"
                  @click="showConfirmPassword = !showConfirmPassword" />
              </template>
            </q-input>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat :label="$t('common.cancel')" @click="closeResetDialog" />
          <q-btn color="blue" :label="$t('users.resetPassword')" @click="handleResetPassword" :loading="resetLoading"
            :disable="!isResetFormValid" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from 'src/stores/auth'
import { userService } from 'src/services/userService'
import { debounce } from 'quasar'

const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// State
const users = ref([])
const loading = ref(false)
const showDetailsDialog = ref(false)
const selectedUser = ref(null)

// Reset password state
const showResetDialog = ref(false)
const selectedUserForReset = ref(null)
const resetLoading = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const resetErrors = ref({})

const resetPasswordForm = reactive({
  password: '',
  password_confirmation: ''
})

const filters = reactive({
  search: '',
  role: null
})

const pagination = ref({
  page: 1,
  rowsPerPage: 15,
  rowsNumber: 0
})

// Computed
const canCreateUsers = computed(() => authStore.hasPermission('create_users'))
const canEditUsers = computed(() => authStore.hasPermission('edit_users'))
const canDeleteUsers = computed(() => authStore.hasPermission('delete_users'))

const isResetFormValid = computed(() => {
  return resetPasswordForm.password &&
    resetPasswordForm.password_confirmation &&
    resetPasswordForm.password === resetPasswordForm.password_confirmation &&
    resetPasswordForm.password.length >= 8
})

const roleOptions = ref([
  { label: 'Super Admin', value: 'Super Admin' },
  { label: 'Owner', value: 'Owner' },
  { label: 'User', value: 'User' }
])

const columns = [
  {
    name: 'name',
    label: 'User',
    field: 'name',
    align: 'left',
    sortable: true
  },
  {
    name: 'roles',
    label: 'Roles',
    field: 'roles',
    align: 'center'
  },
  {
    name: 'status',
    label: 'Status',
    field: 'email_verified_at',
    align: 'center'
  },
  {
    name: 'created_at',
    label: 'Created',
    field: 'created_at',
    align: 'center',
    format: (val) => formatDate(val),
    sortable: true
  },
  {
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center'
  }
]

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage,
      search: filters.search,
      role: filters.role
    }

    const response = await userService.getUsers(params)

    if (response.success) {
      users.value = response.data.data
      pagination.value.rowsNumber = response.data.total
      pagination.value.page = response.data.current_page
    }
  } catch (err) {
    console.error('Failed to fetch users:', err)
    $q.notify({
      type: 'negative',
      message: t('users.failedToLoadUsers'),
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  pagination.value.page = 1
  fetchUsers()
}, 500)

const onRequest = (props) => {
  const { page, rowsPerPage } = props.pagination
  pagination.value.page = page
  pagination.value.rowsPerPage = rowsPerPage
  fetchUsers()
}

const viewUser = (user) => {
  selectedUser.value = user
  showDetailsDialog.value = true
}

const confirmDelete = (user) => {
  $q.dialog({
    title: t('users.confirmDelete'),
    message: t('users.deleteWarning', { name: user.name }),
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      const response = await userService.deleteUser(user.id)
      if (response.success) {
        $q.notify({
          type: 'positive',
          message: t('users.userDeleted'),
          position: 'top'
        })
        fetchUsers()
      }
    } catch (error) {
      console.error('Delete user error:', error)
      const message = error.message || t('users.messages.failedToDeleteUser')
      $q.notify({
        type: 'negative',
        message,
        position: 'top'
      })
    }
  })
}

// Reset password methods
const showResetPasswordDialog = (user) => {
  selectedUserForReset.value = user
  resetPasswordForm.password = ''
  resetPasswordForm.password_confirmation = ''
  resetErrors.value = {}
  showResetDialog.value = true
}

const closeResetDialog = () => {
  showResetDialog.value = false
  selectedUserForReset.value = null
  resetPasswordForm.password = ''
  resetPasswordForm.password_confirmation = ''
  resetErrors.value = {}
  showNewPassword.value = false
  showConfirmPassword.value = false
}

const handleResetPassword = async () => {
  if (!isResetFormValid.value) return

  resetLoading.value = true
  resetErrors.value = {}

  try {
    const response = await userService.resetPassword(selectedUserForReset.value.id, {
      password: resetPasswordForm.password,
      password_confirmation: resetPasswordForm.password_confirmation
    })

    if (response.success) {
      $q.notify({
        type: 'positive',
        message: t('users.messages.passwordResetSuccess'),
        position: 'top'
      })
      closeResetDialog()
    }
  } catch (err) {
    console.error('Reset password error:', err)

    if (err.errors) {
      resetErrors.value = err.errors
    }

    const message = err.message || t('users.messages.failedToResetPassword')
    $q.notify({
      type: 'negative',
      message,
      position: 'top'
    })
  } finally {
    resetLoading.value = false
  }
}

const hasResetError = (field) => {
  return resetErrors.value[field] && resetErrors.value[field].length > 0
}

const getResetError = (field) => {
  return resetErrors.value[field] ? resetErrors.value[field][0] : ''
}

const getRoleColor = (role) => {
  const colors = {
    'Super Admin': 'red',
    'Owner': 'blue',
    'User': 'green'
  }
  return colors[role] || 'grey'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  fetchUsers()
})
</script>

<style lang="scss" scoped>
// Custom styles khusus untuk UserListPage
// Global styles sudah dihandle oleh global.scss

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;

  .q-avatar {
    border: 2px solid rgba(0, 0, 0, 0.1);

    .body--dark & {
      border-color: rgba(255, 255, 255, 0.2);
    }

    img {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }
  }

  .user-details {
    .user-name {
      font-weight: 500;
      color: #2d3748;

      .body--dark & {
        color: #ffffff;
      }
    }

    .user-email {
      font-size: 0.875rem;
      color: #718096;

      .body--dark & {
        color: #bbbbbb;
      }
    }
  }
}

.roles-list {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.user-details-content {
  .q-avatar {
    border: 3px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    .body--dark & {
      border-color: rgba(255, 255, 255, 0.2);
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
    }

    img {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }
  }

  .detail-item {
    margin-bottom: 1rem;
    text-align: left;

    strong {
      color: #2d3748;

      .body--dark & {
        color: #ffffff;
      }
    }
  }
}
</style>
