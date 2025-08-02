<template>
  <q-page class="container padded">
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t('roles.createRole') }}</h4>
          <p class="page-subtitle">{{ $t('roles.createRoleDescription') }}</p>
        </div>
      </div>
    </div>

    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="security" />
          {{ $t('roles.roleDetails') }}
        </div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="saveRole" class="standard-form">
          <div class="form-row">
            <!-- Role Name Input -->
            <div class="form-group">
              <q-input v-model="roleForm.name" :label="$t('roles.roleNameRequired')" outlined :rules="[
                val => !!val || $t('roles.validation.roleNameRequired'),
                val => val.length >= 3 || $t('roles.validation.roleNameMinLength')
              ]" lazy-rules :hint="$t('roles.roleNameHint')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="security" />
                </template>
              </q-input>
            </div>
          </div>

          <!-- Permissions Section -->
          <div class="q-mt-lg">
            <h6 class="text-h6 q-mb-md">{{ $t('roles.permissions') }}</h6>
            <p class="text-body2 text-grey-6 q-mb-md">
              {{ $t('roles.permissionsDescription') }}
            </p>

            <div v-if="loading" class="text-center q-pa-md">
              <q-spinner color="primary" size="2em" />
              <p class="q-mt-sm text-grey-6">{{ $t('roles.loadingPermissions') }}</p>
            </div>

            <div v-else class="permissions-grid">
              <!-- User Management -->
              <q-card v-if="permissionGroups.user_management" flat bordered class="permission-category">
                <q-card-section>
                  <div class="row items-center q-mb-sm">
                    <q-checkbox :model-value="isCategorySelected(permissionGroups.user_management.permissions)"
                      :indeterminate="isCategoryIndeterminate(permissionGroups.user_management.permissions)"
                      @update:model-value="(val) => toggleCategory(permissionGroups.user_management.permissions, val)"
                      color="primary" />
                    <div class="q-ml-sm">
                      <div class="text-subtitle2">{{ permissionGroups.user_management.label }}</div>
                      <div class="text-caption text-grey-6">{{ permissionGroups.user_management.description }}</div>
                    </div>
                  </div>
                  <div class="permission-list">
                    <q-checkbox v-for="permission in permissionGroups.user_management.permissions"
                      :key="permission.name" v-model="roleForm.permissions" :val="permission.name"
                      :label="formatPermissionName(permission.name)" color="primary" size="sm"
                      class="permission-item" />
                  </div>
                </q-card-section>
              </q-card>

              <!-- Role Management -->
              <q-card v-if="permissionGroups.role_management" flat bordered class="permission-category">
                <q-card-section>
                  <div class="row items-center q-mb-sm">
                    <q-checkbox :model-value="isCategorySelected(permissionGroups.role_management.permissions)"
                      :indeterminate="isCategoryIndeterminate(permissionGroups.role_management.permissions)"
                      @update:model-value="(val) => toggleCategory(permissionGroups.role_management.permissions, val)"
                      color="primary" />
                    <div class="q-ml-sm">
                      <div class="text-subtitle2">{{ permissionGroups.role_management.label }}</div>
                      <div class="text-caption text-grey-6">{{ permissionGroups.role_management.description }}</div>
                    </div>
                  </div>
                  <div class="permission-list">
                    <q-checkbox v-for="permission in permissionGroups.role_management.permissions"
                      :key="permission.name" v-model="roleForm.permissions" :val="permission.name"
                      :label="formatPermissionName(permission.name)" color="primary" size="sm"
                      class="permission-item" />
                  </div>
                </q-card-section>
              </q-card>

              <!-- System Access -->
              <q-card v-if="permissionGroups.system_access" flat bordered class="permission-category">
                <q-card-section>
                  <div class="row items-center q-mb-sm">
                    <q-checkbox :model-value="isCategorySelected(permissionGroups.system_access.permissions)"
                      :indeterminate="isCategoryIndeterminate(permissionGroups.system_access.permissions)"
                      @update:model-value="(val) => toggleCategory(permissionGroups.system_access.permissions, val)"
                      color="primary" />
                    <div class="q-ml-sm">
                      <div class="text-subtitle2">{{ permissionGroups.system_access.label }}</div>
                      <div class="text-caption text-grey-6">{{ permissionGroups.system_access.description }}</div>
                    </div>
                  </div>
                  <div class="permission-list">
                    <q-checkbox v-for="permission in permissionGroups.system_access.permissions" :key="permission.name"
                      v-model="roleForm.permissions" :val="permission.name"
                      :label="formatPermissionName(permission.name)" color="primary" size="sm"
                      class="permission-item" />
                  </div>
                </q-card-section>
              </q-card>

              <!-- Administration -->
              <q-card v-if="permissionGroups.administration" flat bordered class="permission-category">
                <q-card-section>
                  <div class="row items-center q-mb-sm">
                    <q-checkbox :model-value="isCategorySelected(permissionGroups.administration.permissions)"
                      :indeterminate="isCategoryIndeterminate(permissionGroups.administration.permissions)"
                      @update:model-value="(val) => toggleCategory(permissionGroups.administration.permissions, val)"
                      color="primary" />
                    <div class="q-ml-sm">
                      <div class="text-subtitle2">{{ permissionGroups.administration.label }}</div>
                      <div class="text-caption text-grey-6">{{ permissionGroups.administration.description }}</div>
                    </div>
                  </div>
                  <div class="permission-list">
                    <q-checkbox v-for="permission in permissionGroups.administration.permissions" :key="permission.name"
                      v-model="roleForm.permissions" :val="permission.name"
                      :label="formatPermissionName(permission.name)" color="primary" size="sm"
                      class="permission-item" />
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>

          <div class="form-actions">
            <q-btn flat :label="$t('common.cancel')" color="grey-7" :to="{ name: 'roles.index' }" :disable="saving"
              class="action-btn" />
            <q-btn type="submit" :label="$t('roles.createRole')" color="primary" :loading="saving"
              :disable="!roleForm.name || roleForm.permissions.length === 0" class="action-btn">
              <template v-slot:loading>
                <q-spinner-facebook />
              </template>
            </q-btn>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { api } from 'src/boot/axios'

const router = useRouter()
const $q = useQuasar()
const { t } = useI18n()

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
      message: t('roles.loadingPermissions'),
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
      message: t('roles.messages.roleCreated'),
      position: 'top'
    })

    router.push({ name: 'roles.index' })
  } catch (err) {
    console.error('Failed to create role:', err)
    const message = err.response?.data?.message || t('roles.messages.failedToCreateRole')
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

// Dark mode styles
.body--dark {
  .role-form-page {
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

  .permission-category {
    background: #1e1e1e !important;
    border-color: #333333 !important;

    &:hover {
      border-color: #555555 !important;
      box-shadow: 0 2px 4px rgba(255, 255, 255, 0.05) !important;
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

  // Fix input text
  .q-input .q-field__native,
  .q-textarea .q-field__native {
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

  // Fix checkbox labels
  .q-checkbox__label {
    color: #e0e0e0 !important;
  }

  // Fix permission category headers
  .q-expansion-item__label {
    color: #ffffff !important;
    font-weight: 600;
  }
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
