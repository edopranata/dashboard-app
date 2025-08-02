<template>
    <q-page class="role-form-page">
        <div class="page-header">
            <h4>Create Role</h4>
            <p>Add a new role with specific permissions</p>
        </div>

        <q-card flat bordered>
            <q-form @submit="saveRole" class="q-gutter-md">
                <q-card-section>
                    <!-- Role Name Input -->
                    <div class="row q-gutter-md">
                        <div class="col-12 col-md-6">
                            <q-input
                                v-model="roleForm.name"
                                label="Role Name *"
                                filled
                                :rules="[
                                    val => !!val || 'Role name is required',
                                    val => val.length >= 3 || 'Role name must be at least 3 characters'
                                ]"
                                lazy-rules
                                hint="Enter a unique role name"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="security" />
                                </template>
                            </q-input>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="q-mt-lg">
                        <h6 class="text-h6 q-mb-md">Permissions</h6>
                        <p class="text-body2 text-grey-6 q-mb-md">
                            Select the permissions that this role should have. You can select individual permissions or entire categories.
                        </p>

                        <div v-if="loading" class="text-center q-pa-md">
                            <q-spinner color="primary" size="2em" />
                            <p class="q-mt-sm text-grey-6">Loading permissions...</p>
                        </div>

                        <div v-else class="permissions-grid">
                            <!-- User Management -->
                            <q-card 
                                v-if="permissionGroups.user_management" 
                                flat bordered 
                                class="permission-category"
                            >
                                <q-card-section>
                                    <div class="row items-center q-mb-sm">
                                        <q-checkbox
                                            :model-value="isCategorySelected(permissionGroups.user_management.permissions)"
                                            :indeterminate="isCategoryIndeterminate(permissionGroups.user_management.permissions)"
                                            @update:model-value="(val) => toggleCategory(permissionGroups.user_management.permissions, val)"
                                            color="primary"
                                        />
                                        <div class="q-ml-sm">
                                            <div class="text-subtitle2">{{ permissionGroups.user_management.label }}</div>
                                            <div class="text-caption text-grey-6">{{ permissionGroups.user_management.description }}</div>
                                        </div>
                                    </div>
                                    <div class="permission-list">
                                        <q-checkbox
                                            v-for="permission in permissionGroups.user_management.permissions"
                                            :key="permission.name"
                                            v-model="roleForm.permissions"
                                            :val="permission.name"
                                            :label="formatPermissionName(permission.name)"
                                            color="primary"
                                            size="sm"
                                            class="permission-item"
                                        />
                                    </div>
                                </q-card-section>
                            </q-card>

                            <!-- Role Management -->
                            <q-card 
                                v-if="permissionGroups.role_management" 
                                flat bordered 
                                class="permission-category"
                            >
                                <q-card-section>
                                    <div class="row items-center q-mb-sm">
                                        <q-checkbox
                                            :model-value="isCategorySelected(permissionGroups.role_management.permissions)"
                                            :indeterminate="isCategoryIndeterminate(permissionGroups.role_management.permissions)"
                                            @update:model-value="(val) => toggleCategory(permissionGroups.role_management.permissions, val)"
                                            color="primary"
                                        />
                                        <div class="q-ml-sm">
                                            <div class="text-subtitle2">{{ permissionGroups.role_management.label }}</div>
                                            <div class="text-caption text-grey-6">{{ permissionGroups.role_management.description }}</div>
                                        </div>
                                    </div>
                                    <div class="permission-list">
                                        <q-checkbox
                                            v-for="permission in permissionGroups.role_management.permissions"
                                            :key="permission.name"
                                            v-model="roleForm.permissions"
                                            :val="permission.name"
                                            :label="formatPermissionName(permission.name)"
                                            color="primary"
                                            size="sm"
                                            class="permission-item"
                                        />
                                    </div>
                                </q-card-section>
                            </q-card>

                            <!-- System Access -->
                            <q-card 
                                v-if="permissionGroups.system_access" 
                                flat bordered 
                                class="permission-category"
                            >
                                <q-card-section>
                                    <div class="row items-center q-mb-sm">
                                        <q-checkbox
                                            :model-value="isCategorySelected(permissionGroups.system_access.permissions)"
                                            :indeterminate="isCategoryIndeterminate(permissionGroups.system_access.permissions)"
                                            @update:model-value="(val) => toggleCategory(permissionGroups.system_access.permissions, val)"
                                            color="primary"
                                        />
                                        <div class="q-ml-sm">
                                            <div class="text-subtitle2">{{ permissionGroups.system_access.label }}</div>
                                            <div class="text-caption text-grey-6">{{ permissionGroups.system_access.description }}</div>
                                        </div>
                                    </div>
                                    <div class="permission-list">
                                        <q-checkbox
                                            v-for="permission in permissionGroups.system_access.permissions"
                                            :key="permission.name"
                                            v-model="roleForm.permissions"
                                            :val="permission.name"
                                            :label="formatPermissionName(permission.name)"
                                            color="primary"
                                            size="sm"
                                            class="permission-item"
                                        />
                                    </div>
                                </q-card-section>
                            </q-card>

                            <!-- Administration -->
                            <q-card 
                                v-if="permissionGroups.administration" 
                                flat bordered 
                                class="permission-category"
                            >
                                <q-card-section>
                                    <div class="row items-center q-mb-sm">
                                        <q-checkbox
                                            :model-value="isCategorySelected(permissionGroups.administration.permissions)"
                                            :indeterminate="isCategoryIndeterminate(permissionGroups.administration.permissions)"
                                            @update:model-value="(val) => toggleCategory(permissionGroups.administration.permissions, val)"
                                            color="primary"
                                        />
                                        <div class="q-ml-sm">
                                            <div class="text-subtitle2">{{ permissionGroups.administration.label }}</div>
                                            <div class="text-caption text-grey-6">{{ permissionGroups.administration.description }}</div>
                                        </div>
                                    </div>
                                    <div class="permission-list">
                                        <q-checkbox
                                            v-for="permission in permissionGroups.administration.permissions"
                                            :key="permission.name"
                                            v-model="roleForm.permissions"
                                            :val="permission.name"
                                            :label="formatPermissionName(permission.name)"
                                            color="primary"
                                            size="sm"
                                            class="permission-item"
                                        />
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </q-card-section>

                <!-- Form Actions -->
                <q-card-actions align="right" class="q-pa-md">
                    <q-btn
                        flat
                        label="Cancel"
                        color="grey-7"
                        :to="{ name: 'roles.index' }"
                        :disable="saving"
                    />
                    <q-btn
                        type="submit"
                        label="Create Role"
                        color="primary"
                        :loading="saving"
                        :disable="!roleForm.name || roleForm.permissions.length === 0"
                    >
                        <template v-slot:loading>
                            <q-spinner-facebook />
                        </template>
                    </q-btn>
                </q-card-actions>
            </q-form>
        </q-card>
    </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const router = useRouter()
const $q = useQuasar()

// Form state
const loading = ref(false)
const saving = ref(false)
const permissionGroups = ref({})

const roleForm = reactive({
  name: '',
  permissions: []
})

// Load permissions
const fetchPermissions = async () => {
  loading.value = true
  try {
    const response = await api.get('/permissions/grouped')
    permissionGroups.value = response.data.data || {}
  } catch (err) {
    console.error('Failed to fetch permissions:', err)
    $q.notify({
      type: 'negative',
      message: 'Failed to load permissions',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

// Save role
const saveRole = async () => {
  saving.value = true
  try {
    const roleData = {
      name: roleForm.name,
      permissions: roleForm.permissions
    }

    await api.post('/roles', roleData)
    
    $q.notify({
      type: 'positive',
      message: 'Role created successfully',
      position: 'top'
    })

    router.push({ name: 'roles.index' })
  } catch (err) {
    console.error('Failed to create role:', err)
    const message = err.response?.data?.message || 'Failed to create role'
    $q.notify({
      type: 'negative',
      message,
      position: 'top'
    })
  } finally {
    saving.value = false
  }
}

// Permission helpers
const isCategorySelected = (categoryPermissions) => {
  if (!categoryPermissions || categoryPermissions.length === 0) return false
  return categoryPermissions.every(p => roleForm.permissions.includes(p.name))
}

const isCategoryIndeterminate = (categoryPermissions) => {
  if (!categoryPermissions || categoryPermissions.length === 0) return false
  const selected = categoryPermissions.filter(p => roleForm.permissions.includes(p.name))
  return selected.length > 0 && selected.length < categoryPermissions.length
}

const toggleCategory = (categoryPermissions, value) => {
  if (!categoryPermissions) return
  
  if (value) {
    // Select all permissions in category
    categoryPermissions.forEach(p => {
      if (!roleForm.permissions.includes(p.name)) {
        roleForm.permissions.push(p.name)
      }
    })
  } else {
    // Deselect all permissions in category
    categoryPermissions.forEach(p => {
      const index = roleForm.permissions.indexOf(p.name)
      if (index > -1) {
        roleForm.permissions.splice(index, 1)
      }
    })
  }
}

const formatPermissionName = (permissionName) => {
  return permissionName
    .replace(/_/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
}

onMounted(() => {
  fetchPermissions()
})
</script>

<style lang="scss" scoped>
.role-form-page {
    padding: 1.5rem;
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;

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

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1rem;
}

.permission-category {
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;

    &:hover {
        border-color: #cbd5e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
}

.permission-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.permission-item {
    padding: 0.25rem 0;
    font-size: 0.875rem;
}

// Responsive adjustments
@media (max-width: 768px) {
    .role-form-page {
        padding: 1rem;
    }
    
    .permissions-grid {
        grid-template-columns: 1fr;
    }
    
    .permission-list {
        grid-template-columns: 1fr;
    }
}
</style>
