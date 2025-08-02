<template>
  <q-page class="container padded">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ isEdit ? $t('users.editUser') : $t('users.createUser') }}</h4>
          <p class="page-subtitle">{{ isEdit ? $t('users.updateUserDescription') : $t('users.addUserDescription') }}</p>
        </div>
        <div class="header-actions">
          <q-btn flat icon="arrow_back" :label="$t('users.backToUsers')" :to="{ name: 'users.index' }"
            class="action-btn" />
        </div>
      </div>
    </div>

    <!-- User Form -->
    <q-card flat bordered class="content-card">
      <q-card-section class="card-header">
        <div class="section-title">
          <q-icon name="person" />
          {{ $t('users.userDetails') }}
        </div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="handleSubmit" class="standard-form">
          <!-- Personal Information -->
          <div class="form-row">
            <div class="form-group">
              <q-input v-model="form.name" :label="$t('users.fullNameRequired')" outlined :rules="[
                val => !!val || $t('users.validation.nameRequired'),
                val => val.length >= 2 || $t('users.validation.nameMinLength')
              ]" :error="hasError('name')" :error-message="getError('name')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="account_circle" />
                </template>
              </q-input>
            </div>

            <div class="form-group">
              <q-input v-model="form.email" :label="$t('users.emailAddressRequired')" type="email" outlined :rules="[
                val => !!val || $t('users.validation.emailRequired'),
                val => /.+@.+\..+/.test(val) || $t('users.validation.validEmailRequired')
              ]" :error="hasError('email')" :error-message="getError('email')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="form-row">
            <div class="form-group">
              <q-input v-model="form.phone" :label="$t('users.phone')" outlined mask="(###) ### - ####"
                :error="hasError('phone')" :error-message="getError('phone')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="phone" />
                </template>
              </q-input>
            </div>

            <div class="form-group">
              <q-input v-model="form.timezone" :label="$t('users.timezone')" outlined placeholder="e.g., Asia/Jakarta"
                :error="hasError('timezone')" :error-message="getError('timezone')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="schedule" />
                </template>
              </q-input>
            </div>
          </div>

          <!-- Bio Section -->
          <div class="form-row">
            <div class="form-group full-width">
              <q-input v-model="form.bio" :label="$t('users.bio')" type="textarea" outlined rows="3" counter
                maxlength="500" :error="hasError('bio')" :error-message="getError('bio')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="description" />
                </template>
              </q-input>
            </div>
          </div>

          <!-- Security - Only show for create mode -->
          <div v-if="!isEdit" class="form-row">
            <div class="form-group">
              <q-input v-model="form.password" :label="$t('users.passwordRequired')"
                :type="showPassword ? 'text' : 'password'" outlined :rules="[
                  val => !!val || $t('users.validation.passwordRequired'),
                  val => val.length >= 8 || $t('users.validation.passwordMinLength')
                ]" :error="hasError('password')" :error-message="getError('password')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-btn flat round dense :icon="showPassword ? 'visibility_off' : 'visibility'"
                    @click="showPassword = !showPassword" />
                </template>
              </q-input>
            </div>

            <div class="form-group">
              <q-input v-model="form.password_confirmation" :label="$t('users.confirmPassword')"
                :type="showPasswordConfirm ? 'text' : 'password'" outlined :rules="[
                  val => !!val || $t('users.validation.confirmPasswordRequired'),
                  val => val === form.password || $t('users.validation.passwordsMismatch')
                ]" :error="hasError('password_confirmation')"
                :error-message="getError('password_confirmation')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="lock" />
                </template>
                <template v-slot:append>
                  <q-btn flat round dense :icon="showPasswordConfirm ? 'visibility_off' : 'visibility'"
                    @click="showPasswordConfirm = !showPasswordConfirm" />
                </template>
              </q-input>
            </div>
          </div>

          <!-- Roles Section -->
          <div class="form-row">
            <div class="form-group full-width">
              <q-select v-model="form.roles" :options="roleOptions" :label="$t('users.assignRolesRequired')" outlined
                multiple emit-value map-options use-chips
                :rules="[val => val && val.length > 0 || $t('users.validation.roleRequired')]"
                :error="hasError('roles')" :error-message="getError('roles')" class="form-input">
                <template v-slot:prepend>
                  <q-icon name="group" />
                </template>
              </q-select>

              <!-- Selected Roles Preview -->
              <div v-if="form.roles.length > 0" class="selected-roles q-mt-md">
                <div class="preview-title">{{ $t('users.selectedRoles') }}:</div>
                <div class="roles-chips">
                  <q-chip v-for="role in form.roles" :key="role" :color="getRoleColor(role)" text-color="white"
                    :icon="getRoleIcon(role)" removable @remove="removeRole(role)">
                    {{ role }}
                  </q-chip>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <q-btn flat :label="$t('common.cancel')" :to="{ name: 'users.index' }" class="action-btn" />
            <q-btn type="submit" color="primary" :label="isEdit ? $t('users.updateUser') : $t('users.createUser')"
              :loading="loading" :disable="!isFormValid" class="action-btn" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { userService } from 'src/services/userService'

const $q = useQuasar()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()

// State
const loading = ref(false)
const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const currentUser = ref(null)
const errors = ref({})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  timezone: '',
  bio: '',
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
    // Only validate password for create mode
    (isEdit.value || (form.password && form.password === form.password_confirmation))
})

// Methods
const fetchUser = async () => {
  if (!isEdit.value) return

  loading.value = true
  try {
    const response = await userService.getUser(route.params.id)
    if (response.success) {
      currentUser.value = response.data

      // Populate form with nextTick to ensure reactivity
      await nextTick()

      form.name = currentUser.value.name || ''
      form.email = currentUser.value.email || ''
      form.phone = currentUser.value.phone || ''
      form.timezone = currentUser.value.timezone || ''
      form.bio = currentUser.value.bio || ''
      form.roles = currentUser.value.roles?.map(role => role.name) || []
      form.password = ''
      form.password_confirmation = ''
    }
  } catch (err) {
    console.error('Failed to fetch user:', err)
    $q.notify({
      type: 'negative',
      message: t('users.messages.failedToLoadUser'),
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
      phone: form.phone,
      timezone: form.timezone,
      bio: form.bio,
      roles: form.roles
    }

    // Only include password for create mode
    if (!isEdit.value && form.password) {
      userData.password = form.password
      userData.password_confirmation = form.password_confirmation
    }

    let response
    if (isEdit.value) {
      // Update user
      response = await userService.updateUser(route.params.id, userData)
    } else {
      // Create user
      response = await userService.createUser(userData)
    }

    if (response.success) {
      $q.notify({
        type: 'positive',
        message: isEdit.value ? t('users.messages.userUpdated') : t('users.messages.userCreated'),
        position: 'top'
      })
      router.push({ name: 'users.index' })
    }
  } catch (err) {
    console.error('Failed to save user:', err)

    if (err.errors) {
      errors.value = err.errors
    }

    const message = err.message || t('users.messages.failedToSaveUser')
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
        phone: '',
        timezone: '',
        bio: '',
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
  .user-form-page {
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

  .form-section {
    .section-title {
      color: #ffffff !important;
      border-color: #444444 !important;
      font-weight: 600;

      .q-icon {
        color: #90caf9 !important;
      }
    }
  }

  .selected-roles {
    .preview-title {
      color: #ffffff !important;
      font-weight: 500;
    }
  }

  .form-actions {
    border-color: #333333 !important;
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

  // Fix icons in inputs
  .q-field__prepend .q-icon,
  .q-field__append .q-icon {
    color: #90caf9 !important;
  }

  // Fix placeholder text
  .q-field__input::placeholder {
    color: #888888 !important;
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
