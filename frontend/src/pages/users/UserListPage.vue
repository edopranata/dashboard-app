<template>
  <q-page class="user-list-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div>
          <h4>{{ $t('users.userManagement') }}</h4>
          <p>{{ $t('users.userManagementDescription') }}</p>
        </div>
        <q-btn color="primary" icon="add" :label="$t('users.addUser')" @click="showCreateDialog = true"
          :disable="!canCreateUsers" />
      </div>
    </div>

    <!-- Filters Section -->
    <q-card flat bordered class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <q-input v-model="filters.search" :placeholder="$t('users.searchUsers')" outlined dense clearable
              @input="debouncedSearch">
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <q-select v-model="filters.role" :options="roleOptions" :label="$t('users.filterByRole')" outlined dense
              emit-value map-options clearable @update:model-value="fetchUsers" />
          </div>
          <div class="col-auto">
            <q-btn flat icon="refresh" @click="fetchUsers" :loading="loading" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Users Table -->
    <q-card flat bordered>
      <q-table :rows="users" :columns="columns" :loading="loading" :pagination="pagination" @request="onRequest"
        row-key="id" binary-state-sort :rows-per-page-options="[10, 25, 50]">
        <!-- Name column with avatar -->
        <template v-slot:body-cell-name="props">
          <q-td :props="props">
            <div class="user-info">
              <q-avatar size="32px">
                <img :src="`https://ui-avatars.com/api/?name=${props.row.name}&background=1976d2&color=fff`" />
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
              <q-btn flat dense icon="edit" color="orange" @click="editUser(props.row)" v-if="canEditUsers">
                <q-tooltip>{{ $t('users.editUser') }}</q-tooltip>
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

    <!-- Create/Edit User Dialog -->
    <q-dialog v-model="showCreateDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center">
          <div class="text-h6">{{ selectedUser?.id ? 'Edit' : 'Create' }} User</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveUser" class="q-gutter-md">
            <q-input v-model="userForm.name" label="Full Name" outlined :rules="[val => !!val || 'Name is required']" />
            <q-input v-model="userForm.email" label="Email" type="email" outlined
              :rules="[val => !!val || 'Email is required']" />
            <q-input v-model="userForm.password" label="Password" type="password" outlined
              :rules="selectedUser?.id ? [] : [val => !!val || 'Password is required']"
              :hint="selectedUser?.id ? 'Leave blank to keep current password' : ''" />
            <q-select v-model="userForm.roles" :options="roleOptions" label="Roles" outlined multiple emit-value
              map-options :rules="[val => val && val.length > 0 || 'At least one role is required']" />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn color="primary" label="Save" @click="saveUser" :loading="saving" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- User Details Dialog -->
    <q-dialog v-model="showDetailsDialog">
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center">
          <div class="text-h6">User Details</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedUser">
          <div class="user-details-content">
            <q-avatar size="64px" class="q-mb-md">
              <img
                :src="`https://ui-avatars.com/api/?name=${selectedUser.name}&background=1976d2&color=fff&size=128`" />
            </q-avatar>
            <div class="detail-item">
              <strong>Name:</strong> {{ selectedUser.name }}
            </div>
            <div class="detail-item">
              <strong>Email:</strong> {{ selectedUser.email }}
            </div>
            <div class="detail-item">
              <strong>Status:</strong>
              <q-chip :color="selectedUser.email_verified_at ? 'green' : 'orange'" text-color="white" size="sm">
                {{ selectedUser.email_verified_at ? 'Active' : 'Pending' }}
              </q-chip>
            </div>
            <div class="detail-item">
              <strong>Roles:</strong>
              <div class="roles-list q-mt-xs">
                <q-chip v-for="role in selectedUser.roles" :key="role.id" :color="getRoleColor(role.name)"
                  text-color="white" size="sm">
                  {{ role.name }}
                </q-chip>
              </div>
            </div>
            <div class="detail-item">
              <strong>Created:</strong> {{ formatDate(selectedUser.created_at) }}
            </div>
            <div class="detail-item">
              <strong>Updated:</strong> {{ formatDate(selectedUser.updated_at) }}
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { api } from 'src/boot/axios'
import { useAuthStore } from 'src/stores/auth'
import { debounce } from 'quasar'

const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// State
const users = ref([])
const loading = ref(false)
const saving = ref(false)
const showCreateDialog = ref(false)
const showDetailsDialog = ref(false)
const selectedUser = ref(null)

const filters = reactive({
  search: '',
  role: null
})

const userForm = reactive({
  name: '',
  email: '',
  password: '',
  roles: []
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

    const response = await api.get('/users', { params })

    if (response.data.success) {
      users.value = response.data.data.data
      pagination.value.rowsNumber = response.data.data.total
      pagination.value.page = response.data.data.current_page
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

const editUser = (user) => {
  selectedUser.value = user
  userForm.name = user.name
  userForm.email = user.email
  userForm.password = ''
  userForm.roles = user.roles.map(role => role.name)
  showCreateDialog.value = true
}

const saveUser = async () => {
  saving.value = true
  try {
    const userData = {
      name: userForm.name,
      email: userForm.email,
      roles: userForm.roles
    }

    if (userForm.password) {
      userData.password = userForm.password
      userData.password_confirmation = userForm.password
    }

    if (selectedUser.value?.id) {
      // Update user
      await api.put(`/users/${selectedUser.value.id}`, userData)
      $q.notify({
        type: 'positive',
        message: t('users.userUpdated'),
        position: 'top'
      })
    } else {
      // Create user
      await api.post('/users', userData)
      $q.notify({
        type: 'positive',
        message: t('users.userCreated'),
        position: 'top'
      })
    }

    showCreateDialog.value = false
    resetForm()
    fetchUsers()
  } catch (error) {
    const message = error.response?.data?.message || t('users.messages.failedToSaveUser')
    $q.notify({
      type: 'negative',
      message,
      position: 'top'
    })
  } finally {
    saving.value = false
  }
}

const confirmDelete = (user) => {
  $q.dialog({
    title: t('users.confirmDelete'),
    message: t('users.deleteWarning', { name: user.name }),
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await api.delete(`/users/${user.id}`)
      $q.notify({
        type: 'positive',
        message: t('users.userDeleted'),
        position: 'top'
      })
      fetchUsers()
    } catch (error) {
      const message = error.response?.data?.message || t('users.messages.failedToSaveUser')
      $q.notify({
        type: 'negative',
        message,
        position: 'top'
      })
    }
  })
}

const resetForm = () => {
  selectedUser.value = null
  userForm.name = ''
  userForm.email = ''
  userForm.password = ''
  userForm.roles = []
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
.user-list-page {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;

  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;

    h4 {
      margin: 0 0 0.5rem;
      font-weight: 600;
      color: #2d3748;
    }

    p {
      margin: 0;
      color: #718096;
    }
  }
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;

  .user-details {
    .user-name {
      font-weight: 500;
      color: #2d3748;
    }

    .user-email {
      font-size: 0.875rem;
      color: #718096;
    }
  }
}

.roles-list {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.user-details-content {
  text-align: center;

  .detail-item {
    margin-bottom: 1rem;
    text-align: left;

    strong {
      color: #2d3748;
    }
  }
}

// Dark mode styles
.body--dark {
  .user-list-page {
    background: #121212;
  }

  .page-header {
    h4 {
      color: #ffffff !important;
      font-weight: 600;
    }

    p {
      color: #e0e0e0 !important;
    }
  }

  .q-card {
    background: #1e1e1e !important;
    border-color: #333333 !important;
  }

  .user-info {
    .user-details {
      .user-name {
        color: #ffffff !important;
        font-weight: 500;
      }

      .user-email {
        color: #bbbbbb !important;
      }
    }
  }

  .user-details-content {
    .detail-item {
      strong {
        color: #ffffff !important;
      }

      span {
        color: #e0e0e0 !important;
      }
    }
  }

  // Fix table headers and text
  .q-table {
    background: #1e1e1e !important;

    th {
      color: #ffffff !important;
      background: #2a2a2a !important;
      font-weight: 600;
    }

    td {
      color: #e0e0e0 !important;
      border-color: #333333 !important;
    }
  }

  // Fix input labels and text
  .q-field--outlined .q-field__control:before {
    border-color: #555555 !important;
  }

  .q-field--outlined.q-field--focused .q-field__control:before {
    border-color: #90caf9 !important;
  }

  .q-field__label {
    color: #e0e0e0 !important;
  }

  .q-field--focused .q-field__label {
    color: #90caf9 !important;
  }

  // Fix select dropdown text
  .q-select .q-field__native {
    color: #ffffff !important;
  }

  // Fix input text
  .q-input .q-field__native {
    color: #ffffff !important;
  }

  // Fix icons
  .q-field__prepend .q-icon,
  .q-field__append .q-icon {
    color: #90caf9 !important;
  }

  // Fix placeholder text
  .q-field__input::placeholder {
    color: #888888 !important;
  }
}
</style>
