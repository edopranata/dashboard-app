<template>
  <q-page class="role-list-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div>
          <h4>Role Management</h4>
          <p>Manage roles and their permissions in the system</p>
        </div>
        <q-btn
          color="primary"
          icon="add"
          label="Add Role"
          @click="createRole"
          :disable="!canCreateRoles"
        />
      </div>
    </div>

    <!-- Roles Grid -->
    <div class="roles-grid">
      <q-card
        v-for="role in roles"
        :key="role.id"
        flat
        bordered
        class="role-card"
        :class="{ 'system-role': isSystemRole(role.name) }"
      >
        <q-card-section>
          <div class="role-header">
            <div class="role-info">
              <q-icon
                :name="getRoleIcon(role.name)"
                :color="getRoleColor(role.name)"
                size="md"
                class="q-mr-sm"
              />
              <div>
                <div class="role-name">{{ role.name }}</div>
                <div class="role-description">{{ getRoleDescription(role.name) }}</div>
              </div>
            </div>
            <q-btn-dropdown
              flat
              dense
              icon="more_vert"
              :disable="isSystemRole(role.name) && !authStore.isSuperAdmin"
            >
              <q-list>
                <q-item clickable v-close-popup @click="viewRole(role)">
                  <q-item-section avatar>
                    <q-icon name="visibility" />
                  </q-item-section>
                  <q-item-section>View Details</q-item-section>
                </q-item>
                <q-item
                  clickable
                  v-close-popup
                  @click="editRole(role)"
                  v-if="canEditRoles && (!isSystemRole(role.name) || authStore.isSuperAdmin)"
                >
                  <q-item-section avatar>
                    <q-icon name="edit" />
                  </q-item-section>
                  <q-item-section>Edit Role</q-item-section>
                </q-item>
                <q-separator v-if="canDeleteRoles && !isSystemRole(role.name)" />
                <q-item
                  clickable
                  v-close-popup
                  @click="confirmDelete(role)"
                  v-if="canDeleteRoles && !isSystemRole(role.name)"
                >
                  <q-item-section avatar>
                    <q-icon name="delete" color="red" />
                  </q-item-section>
                  <q-item-section>Delete Role</q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </div>

          <div class="role-stats">
            <div class="stat-item">
              <q-icon name="people" size="sm" />
              <span>{{ role.users_count || 0 }} users</span>
            </div>
            <div class="stat-item">
              <q-icon name="security" size="sm" />
              <span>{{ role.permissions?.length || 0 }} permissions</span>
            </div>
          </div>

          <div class="permissions-preview">
            <div class="permissions-label">Key Permissions:</div>
            <div class="permissions-chips">
              <q-chip
                v-for="permission in getKeyPermissions(role.permissions)"
                :key="permission.id || permission"
                size="sm"
                dense
                :color="getPermissionColor(permission.name || permission)"
                text-color="white"
              >
                {{ formatPermissionName(permission.name || permission) }}
              </q-chip>
              <q-chip
                v-if="role.permissions && role.permissions.length > 3"
                size="sm"
                dense
                color="grey"
                text-color="white"
              >
                +{{ (role.permissions?.length || 0) - 3 }} more
              </q-chip>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Create/Edit Role Dialog -->
    <q-dialog v-model="showRoleDialog" persistent>
      <q-card style="min-width: 600px; max-width: 800px">
        <q-card-section class="row items-center">
          <div class="text-h6">{{ selectedRole?.id ? 'Edit' : 'Create' }} Role</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveRole" class="q-gutter-md">
            <q-input
              v-model="roleForm.name"
              label="Role Name"
              outlined
              :rules="[val => !!val || 'Role name is required']"
              :disable="selectedRole?.id && isSystemRole(selectedRole?.name)"
            />

            <div class="permissions-section">
              <div class="section-title">Permissions</div>
              <div v-if="Object.keys(groupedPermissions).length === 0" class="text-center q-pa-md">
                <q-spinner color="primary" size="2em" />
                <p class="q-mt-sm text-grey-6">Loading permissions...</p>
              </div>
              <div v-else class="permissions-grid">
                <div
                  v-for="(categoryData, categoryKey) in groupedPermissions"
                  :key="categoryKey"
                  class="permission-category"
                >
                  <div class="category-header">
                    <q-checkbox
                      :model-value="isCategorySelected(categoryData.permissions)"
                      @update:model-value="toggleCategory(categoryData.permissions, $event)"
                      :indeterminate="isCategoryIndeterminate(categoryData.permissions)"
                    />
                    <div class="category-info">
                      <span class="category-name">{{ categoryData.label }}</span>
                      <span class="category-description">{{ categoryData.description }}</span>
                    </div>
                  </div>
                  <div class="category-permissions">
                    <q-checkbox
                      v-for="permission in categoryData.permissions"
                      :key="permission.name"
                      v-model="roleForm.permissions"
                      :val="permission.name"
                      :label="formatPermissionName(permission.name)"
                      class="permission-item"
                    />
                  </div>
                </div>
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn
            color="primary"
            label="Save"
            @click="saveRole"
            :loading="saving"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Role Details Dialog -->
    <q-dialog v-model="showDetailsDialog">
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center">
          <div class="text-h6">Role Details</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section v-if="selectedRole">
          <div class="role-details-content">
            <div class="role-header-detail">
              <q-icon
                :name="getRoleIcon(selectedRole.name)"
                :color="getRoleColor(selectedRole.name)"
                size="lg"
              />
              <div class="role-info-detail">
                <div class="role-name-detail">{{ selectedRole.name }}</div>
                <div class="role-description-detail">{{ getRoleDescription(selectedRole.name) }}</div>
              </div>
            </div>

            <div class="role-stats-detail">
              <div class="stat-item">
                <q-icon name="people" />
                <span>{{ selectedRole.users_count || 0 }} users assigned</span>
              </div>
              <div class="stat-item">
                <q-icon name="security" />
                <span>{{ selectedRole.permissions?.length || 0 }} permissions granted</span>
              </div>
            </div>

            <div class="permissions-detail">
              <div class="section-title">All Permissions</div>
              <div class="permissions-list">
                <div
                  v-for="(categoryData, categoryKey) in getFilteredGroupedPermissions(selectedRole.permissions)"
                  :key="categoryKey"
                  class="permission-group"
                  v-show="categoryData.permissions.length > 0"
                >
                  <div class="group-title">{{ categoryData.label }}</div>
                  <div class="group-permissions">
                    <q-chip
                      v-for="permission in categoryData.permissions"
                      :key="permission.name"
                      :color="getPermissionColor(permission.name)"
                      text-color="white"
                      size="sm"
                    >
                      {{ formatPermissionName(permission.name) }}
                    </q-chip>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Loading overlay -->
    <q-inner-loading :showing="loading">
      <q-spinner-gears size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { useAuthStore } from 'src/stores/auth'

const $q = useQuasar()
const authStore = useAuthStore()

// State
const roles = ref([])
const loading = ref(false)
const saving = ref(false)
const showRoleDialog = ref(false)
const showDetailsDialog = ref(false)
const selectedRole = ref(null)

const roleForm = reactive({
  name: '',
  permissions: []
})

// Computed
const canCreateRoles = computed(() => authStore.hasPermission('create_roles'))
const canEditRoles = computed(() => authStore.hasPermission('edit_roles'))
const canDeleteRoles = computed(() => authStore.hasPermission('delete_roles'))

const groupedPermissions = ref({})

// Methods
const fetchRoles = async () => {
  loading.value = true
  try {
    const response = await api.get('/roles')
    if (response.data.success) {
      // Handle paginated response structure
      roles.value = response.data.data.data || response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch roles:', err)
    $q.notify({
      type: 'negative',
      message: 'Failed to fetch roles',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

const fetchPermissions = async () => {
  try {
    const response = await api.get('/permissions/grouped')
    if (response.data.success) {
      // Use the grouped permissions from API
      groupedPermissions.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch permissions:', err)
  }
}

const createRole = () => {
  selectedRole.value = null
  roleForm.name = ''
  roleForm.permissions = []
  showRoleDialog.value = true
}

const editRole = (role) => {
  selectedRole.value = role
  roleForm.name = role.name
  // Convert permission objects to permission name strings
  roleForm.permissions = role.permissions ? role.permissions.map(p => p.name || p) : []
  showRoleDialog.value = true
}

const viewRole = (role) => {
  selectedRole.value = role
  showDetailsDialog.value = true
}

const saveRole = async () => {
  saving.value = true
  try {
    const roleData = {
      name: roleForm.name,
      permissions: roleForm.permissions
    }

    if (selectedRole.value?.id) {
      // Update role
      await api.put(`/roles/${selectedRole.value.id}`, roleData)
      $q.notify({
        type: 'positive',
        message: 'Role updated successfully',
        position: 'top'
      })
    } else {
      // Create role
      await api.post('/roles', roleData)
      $q.notify({
        type: 'positive',
        message: 'Role created successfully',
        position: 'top'
      })
    }

    showRoleDialog.value = false
    fetchRoles()
  } catch (err) {
    console.error('Failed to save role:', err)
    const message = err.response?.data?.message || 'Failed to save role'
    $q.notify({
      type: 'negative',
      message,
      position: 'top'
    })
  } finally {
    saving.value = false
  }
}

const confirmDelete = (role) => {
  $q.dialog({
    title: 'Confirm Delete',
    message: `Are you sure you want to delete role "${role.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await api.delete(`/roles/${role.id}`)
      $q.notify({
        type: 'positive',
        message: 'Role deleted successfully',
        position: 'top'
      })
      fetchRoles()
    } catch (err) {
      console.error('Failed to delete role:', err)
      const message = err.response?.data?.message || 'Failed to delete role'
      $q.notify({
        type: 'negative',
        message,
        position: 'top'
      })
    }
  })
}

// Permission helpers
const isCategorySelected = (permissions) => {
  return permissions.every(p => roleForm.permissions.includes(p.name))
}

const isCategoryIndeterminate = (permissions) => {
  const selected = permissions.filter(p => roleForm.permissions.includes(p.name))
  return selected.length > 0 && selected.length < permissions.length
}

const toggleCategory = (permissions, value) => {
  if (value) {
    // Select all permissions in category
    permissions.forEach(p => {
      if (!roleForm.permissions.includes(p.name)) {
        roleForm.permissions.push(p.name)
      }
    })
  } else {
    // Deselect all permissions in category
    permissions.forEach(p => {
      const index = roleForm.permissions.indexOf(p.name)
      if (index > -1) {
        roleForm.permissions.splice(index, 1)
      }
    })
  }
}

// Utility functions
const isSystemRole = (roleName) => {
  if (!roleName) return false
  return ['Super Admin', 'Owner', 'User'].includes(roleName)
}

const getRoleIcon = (roleName) => {
  const icons = {
    'Super Admin': 'admin_panel_settings',
    'Owner': 'business_center',
    'User': 'person'
  }
  return icons[roleName] || 'security'
}

const getRoleColor = (roleName) => {
  const colors = {
    'Super Admin': 'red',
    'Owner': 'blue',
    'User': 'green'
  }
  return colors[roleName] || 'grey'
}

const getRoleDescription = (roleName) => {
  const descriptions = {
    'Super Admin': 'Full system access with all permissions',
    'Owner': 'Business owner with user management capabilities',
    'User': 'Standard user with basic access rights'
  }
  return descriptions[roleName] || 'Custom role with assigned permissions'
}

const getKeyPermissions = (permissions) => {
  if (!permissions || permissions.length === 0) return []
  return permissions.slice(0, 3)
}

const getPermissionColor = (permission) => {
  const colors = {
    'view_': 'blue',
    'create_': 'green',
    'edit_': 'orange',
    'delete_': 'red',
    'manage_': 'purple'
  }
  
  for (const prefix in colors) {
    if (permission.startsWith(prefix)) {
      return colors[prefix]
    }
  }
  return 'grey'
}

const formatPermissionName = (permission) => {
  return permission
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const getFilteredGroupedPermissions = (rolePermissions) => {
  if (!rolePermissions || !groupedPermissions.value) return {}
  
  const result = {}
  const rolePermissionNames = rolePermissions.map(p => p.name || p)
  
  Object.keys(groupedPermissions.value).forEach(categoryKey => {
    const categoryData = groupedPermissions.value[categoryKey]
    const filteredPermissions = categoryData.permissions.filter(p => 
      rolePermissionNames.includes(p.name)
    )
    
    if (filteredPermissions.length > 0) {
      result[categoryKey] = {
        label: categoryData.label,
        permissions: filteredPermissions
      }
    }
  })
  
  return result
}

// Lifecycle
onMounted(async () => {
  await Promise.all([fetchRoles(), fetchPermissions()])
})
</script>

<style lang="scss" scoped>
.role-list-page {
  padding: 1.5rem;
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

.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

.role-card {
  transition: all 0.2s ease;
  
  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  &.system-role {
    border-left: 4px solid #f56565;
  }
}

.role-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
  
  .role-info {
    display: flex;
    align-items: center;
    
    .role-name {
      font-weight: 600;
      font-size: 1.1rem;
      color: #2d3748;
      margin-bottom: 0.25rem;
    }
    
    .role-description {
      font-size: 0.875rem;
      color: #718096;
    }
  }
}

.role-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #718096;
  }
}

.permissions-preview {
  .permissions-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
  }
  
  .permissions-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
  }
}

.permissions-section {
  .section-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2d3748;
  }
}

.permissions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.permission-category {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  
  .category-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
    
    .category-info {
      display: flex;
      flex-direction: column;
      
      .category-name {
        font-weight: 500;
        color: #2d3748;
        font-size: 0.9rem;
      }
      
      .category-description {
        font-size: 0.75rem;
        color: #718096;
        margin-top: 0.125rem;
      }
    }
  }
  
  .category-permissions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    
    .permission-item {
      font-size: 0.875rem;
    }
  }
}

.role-details-content {
  .role-header-detail {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    
    .role-info-detail {
      .role-name-detail {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
      }
      
      .role-description-detail {
        color: #718096;
      }
    }
  }
  
  .role-stats-detail {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    
    .stat-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #718096;
    }
  }
  
  .permissions-detail {
    .section-title {
      font-weight: 600;
      margin-bottom: 1rem;
      color: #2d3748;
    }
    
    .permissions-list {
      .permission-group {
        margin-bottom: 1rem;
        
        .group-title {
          font-weight: 500;
          color: #4a5568;
          margin-bottom: 0.5rem;
        }
        
        .group-permissions {
          display: flex;
          flex-wrap: wrap;
          gap: 0.25rem;
        }
      }
    }
  }
}

// Dark mode styles
.body--dark {
  .page-header {
    h4 {
      color: #f7fafc;
    }
    
    p {
      color: #a0aec0;
    }
  }
  
  .role-header {
    .role-info {
      .role-name {
        color: #f7fafc;
      }
      
      .role-description {
        color: #a0aec0;
      }
    }
  }
  
  .role-stats {
    .stat-item {
      color: #a0aec0;
    }
  }
  
  .permissions-preview {
    .permissions-label {
      color: #cbd5e0;
    }
  }
  
  .permissions-section {
    .section-title {
      color: #f7fafc;
    }
  }
  
  .permission-category {
    background-color: #2d3748;
    border-color: #4a5568;
    
    .category-header {
      border-color: #4a5568;
      
      .category-name {
        color: #f7fafc;
      }
    }
  }
  
  .role-details-content {
    .role-header-detail {
      .role-info-detail {
        .role-name-detail {
          color: #f7fafc;
        }
        
        .role-description-detail {
          color: #a0aec0;
        }
      }
    }
    
    .role-stats-detail {
      .stat-item {
        color: #a0aec0;
      }
    }
    
    .permissions-detail {
      .section-title {
        color: #f7fafc;
      }
      
      .permissions-list {
        .permission-group {
          .group-title {
            color: #cbd5e0;
          }
        }
      }
    }
  }
}

// Responsive design
@media (max-width: 768px) {
  .roles-grid {
    grid-template-columns: 1fr;
  }
  
  .permissions-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header {
    .header-content {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
  }
}
</style>
