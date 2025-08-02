<template>
  <q-page class="user-form-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div>
          <h4>{{ isEdit ? 'Edit User' : 'Create User' }}</h4>
          <p>{{ isEdit ? 'Update user information and roles' : 'Add a new user to the system' }}</p>
        </div>
        <q-btn
          flat
          icon="arrow_back"
          label="Back to Users"
          :to="{ name: 'users.index' }"
        />
      </div>
    </div>

    <!-- User Form -->
    <div class="form-container">
      <q-card flat bordered>
        <q-card-section>
          <q-form @submit="handleSubmit" class="user-form">
            <div class="form-grid">
              <!-- Personal Information Section -->
              <div class="form-section">
                <div class="section-title">
                  <q-icon name="person" />
                  Personal Information
                </div>
                
                <div class="form-fields">
                  <q-input
                    v-model="form.name"
                    label="Full Name *"
                    outlined
                    :rules="[
                      val => !!val || 'Name is required',
                      val => val.length >= 2 || 'Name must be at least 2 characters'
                    ]"
                    :error="hasError('name')"
                    :error-message="getError('name')"
                  >
                    <template v-slot:prepend>
                      <q-icon name="account_circle" />
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.email"
                    label="Email Address *"
                    type="email"
                    outlined
                    :rules="[
                      val => !!val || 'Email is required',
                      val => /.+@.+\..+/.test(val) || 'Please enter a valid email'
                    ]"
                    :error="hasError('email')"
                    :error-message="getError('email')"
                  >
                    <template v-slot:prepend>
                      <q-icon name="email" />
                    </template>
                  </q-input>
                </div>
              </div>

              <!-- Security Section -->
              <div class="form-section">
                <div class="section-title">
                  <q-icon name="security" />
                  Security Settings
                </div>
                
                <div class="form-fields">
                  <q-input
                    v-model="form.password"
                    :label="isEdit ? 'New Password (Leave blank to keep current)' : 'Password *'"
                    :type="showPassword ? 'text' : 'password'"
                    outlined
                    :rules="isEdit ? [] : [
                      val => !!val || 'Password is required',
                      val => val.length >= 8 || 'Password must be at least 8 characters'
                    ]"
                    :error="hasError('password')"
                    :error-message="getError('password')"
                  >
                    <template v-slot:prepend>
                      <q-icon name="lock" />
                    </template>
                    <template v-slot:append>
                      <q-btn
                        flat
                        round
                        dense
                        :icon="showPassword ? 'visibility_off' : 'visibility'"
                        @click="showPassword = !showPassword"
                      />
                    </template>
                  </q-input>

                  <q-input
                    v-model="form.password_confirmation"
                    label="Confirm Password"
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    outlined
                    :rules="form.password ? [
                      val => !!val || 'Password confirmation is required',
                      val => val === form.password || 'Passwords do not match'
                    ] : []"
                    :disable="!form.password"
                    :error="hasError('password_confirmation')"
                    :error-message="getError('password_confirmation')"
                  >
                    <template v-slot:prepend>
                      <q-icon name="lock" />
                    </template>
                    <template v-slot:append>
                      <q-btn
                        flat
                        round
                        dense
                        :icon="showPasswordConfirm ? 'visibility_off' : 'visibility'"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                        :disable="!form.password"
                      />
                    </template>
                  </q-input>
                </div>
              </div>

              <!-- Roles Section -->
              <div class="form-section full-width">
                <div class="section-title">
                  <q-icon name="admin_panel_settings" />
                  Roles & Permissions
                </div>
                
                <div class="roles-section">
                  <q-select
                    v-model="form.roles"
                    :options="roleOptions"
                    label="Assign Roles *"
                    outlined
                    multiple
                    emit-value
                    map-options
                    use-chips
                    :rules="[val => val && val.length > 0 || 'At least one role must be assigned']"
                    :error="hasError('roles')"
                    :error-message="getError('roles')"
                  >
                    <template v-slot:prepend>
                      <q-icon name="group" />
                    </template>
                  </q-select>

                  <!-- Selected Roles Preview -->
                  <div v-if="form.roles.length > 0" class="selected-roles">
                    <div class="preview-title">Selected Roles:</div>
                    <div class="roles-chips">
                      <q-chip
                        v-for="role in form.roles"
                        :key="role"
                        :color="getRoleColor(role)"
                        text-color="white"
                        :icon="getRoleIcon(role)"
                        removable
                        @remove="removeRole(role)"
                      >
                        {{ role }}
                      </q-chip>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
              <q-btn
                flat
                label="Cancel"
                :to="{ name: 'users.index' }"
                class="q-mr-sm"
              />
              <q-btn
                type="submit"
                color="primary"
                :label="isEdit ? 'Update User' : 'Create User'"
                :loading="loading"
                :disable="!isFormValid"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()

// State
const loading = ref(false)
const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const currentUser = ref(null)
const errors = ref({})

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: []
})

const roleOptions = ref([
  { label: 'Super Admin', value: 'Super Admin' },
  { label: 'Owner', value: 'Owner' },
  { label: 'User', value: 'User' }
])

// Computed
const isEdit = computed(() => !!route.params.id)

const isFormValid = computed(() => {
  return form.name && 
         form.email && 
         form.roles.length > 0 && 
         (!form.password || form.password === form.password_confirmation)
})

// Methods
const fetchUser = async () => {
  if (!isEdit.value) return
  
  loading.value = true
  try {
    const response = await api.get(`/users/${route.params.id}`)
    if (response.data.success) {
      currentUser.value = response.data.data
      
      // Populate form
      form.name = currentUser.value.name
      form.email = currentUser.value.email
      form.roles = currentUser.value.roles || []
      form.password = ''
      form.password_confirmation = ''
    }
  } catch (err) {
    console.error('Failed to fetch user:', err)
    $q.notify({
      type: 'negative',
      message: 'Failed to load user data',
      position: 'top'
    })
    router.push({ name: 'users.index' })
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}
  
  try {
    const userData = {
      name: form.name,
      email: form.email,
      roles: form.roles
    }

    if (form.password) {
      userData.password = form.password
      userData.password_confirmation = form.password_confirmation
    }

    if (isEdit.value) {
      // Update user
      await api.put(`/users/${route.params.id}`, userData)
      $q.notify({
        type: 'positive',
        message: 'User updated successfully',
        position: 'top'
      })
    } else {
      // Create user
      await api.post('/users', userData)
      $q.notify({
        type: 'positive',
        message: 'User created successfully',
        position: 'top'
      })
    }

    router.push({ name: 'users.index' })
  } catch (err) {
    console.error('Failed to save user:', err)
    
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    }
    
    const message = err.response?.data?.message || 'Failed to save user'
    $q.notify({
      type: 'negative',
      message,
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

const removeRole = (role) => {
  const index = form.roles.indexOf(role)
  if (index > -1) {
    form.roles.splice(index, 1)
  }
}

const hasError = (field) => {
  return errors.value[field] && errors.value[field].length > 0
}

const getError = (field) => {
  return errors.value[field] ? errors.value[field][0] : ''
}

// Utility functions
const getRoleIcon = (role) => {
  const icons = {
    'Super Admin': 'admin_panel_settings',
    'Owner': 'business_center',
    'User': 'person'
  }
  return icons[role] || 'security'
}

const getRoleColor = (role) => {
  const colors = {
    'Super Admin': 'red',
    'Owner': 'blue',
    'User': 'green'
  }
  return colors[role] || 'grey'
}

// Watch for route changes
watch(
  () => route.params.id,
  () => {
    if (isEdit.value) {
      fetchUser()
    } else {
      // Reset form for create mode
      Object.assign(form, {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: []
      })
      currentUser.value = null
      errors.value = {}
    }
  },
  { immediate: true }
)

// Lifecycle
onMounted(() => {
  if (isEdit.value) {
    fetchUser()
  }
})
</script>

<style lang="scss" scoped>
.user-form-page {
  padding: 1.5rem;
  max-width: 1200px;
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

.form-container {
  margin-bottom: 2rem;
}

.user-form {
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
    
    .form-section {
      &.full-width {
        grid-column: 1 / -1;
      }
      
      .section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
      }
      
      .form-fields {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }
    }
  }
}

.roles-section {
  .selected-roles {
    margin-top: 1rem;
    
    .preview-title {
      font-weight: 500;
      color: #4a5568;
      margin-bottom: 0.5rem;
    }
    
    .roles-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
  }
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
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
  
  .form-section {
    .section-title {
      color: #f7fafc;
      border-color: #4a5568;
    }
  }
  
  .selected-roles {
    .preview-title {
      color: #cbd5e0;
    }
  }
}

// Responsive design
@media (max-width: 768px) {
  .user-form {
    .form-grid {
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
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
