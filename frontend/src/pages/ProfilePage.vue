<template>
  <q-page class="profile-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h4 class="page-title">{{ $t('profile.title') }}</h4>
          <p class="page-subtitle">{{ $t('profile.subtitle') }}</p>
        </div>
      </div>
    </div>

    <div class="row q-gutter-lg">
      <!-- Profile Information -->
      <div class="col-12 col-md-8">
        <q-card class="profile-card" flat bordered>
          <q-card-section class="card-header">
            <div class="card-title">
              <q-icon name="person" class="q-mr-sm" />
              {{ $t('profile.profileInformation') }}
            </div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="updateProfile" class="q-gutter-md">
              <div class="row q-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input v-model="profileForm.name" :label="$t('profile.fullName')" outlined
                    :rules="[val => !!val || $t('profile.nameRequired')]" />
                </div>

                <div class="col-12 col-sm-6">
                  <q-input v-model="profileForm.email" :label="$t('profile.emailAddress')" type="email" outlined :rules="[
                    val => !!val || $t('profile.emailRequired'),
                    val => /.+@.+\..+/.test(val) || $t('profile.validEmailRequired')
                  ]" />
                </div>
              </div>

              <div class="row q-gutter-md">
                <div class="col-12 col-sm-6">
                  <q-input v-model="profileForm.phone" :label="$t('profile.phoneNumber')" outlined
                    mask="(###) ### - ####" unmasked-value />
                </div>

                <div class="col-12 col-sm-6">
                  <q-select v-model="profileForm.timezone" :options="timezoneOptions" :label="$t('profile.timezone')"
                    outlined emit-value map-options />
                </div>
              </div>

              <q-input v-model="profileForm.bio" :label="$t('profile.bio')" type="textarea" outlined rows="3" counter
                maxlength="500" />

              <div class="form-actions">
                <q-btn type="submit" color="primary" :label="$t('profile.updateProfile')" :loading="updatingProfile"
                  icon="save" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>

      <!-- User Avatar & Info -->
      <div class="col-12 col-md-4">
        <q-card class="avatar-card" flat bordered>
          <q-card-section class="text-center">
            <div class="avatar-section">
              <!-- Avatar Upload Component -->
              <AvatarUpload 
                :display-size="'120px'"
                @avatar-updated="handleAvatarUpdate"
                @avatar-deleted="handleAvatarDelete"
                @upload-progress="handleUploadProgress"
              />

              <h6 class="user-name q-mt-md">{{ authStore.userName }}</h6>
              <p class="user-email">{{ authStore.userEmail }}</p>

              <div class="user-roles">
                <q-chip v-for="role in authStore.userRoles" :key="role" :color="getRoleColor(role)" text-color="white"
                  size="sm">
                  {{ role }}
                </q-chip>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="user-stats">
              <div class="stat-item">
                <div class="stat-label">{{ $t('profile.memberSince') }}</div>
                <div class="stat-value">{{ formatDate(authStore.user?.created_at) }}</div>
              </div>

              <div class="stat-item">
                <div class="stat-label">{{ $t('profile.lastLogin') }}</div>
                <div class="stat-value">{{ formatDate(authStore.user?.last_login_at) }}</div>
              </div>

              <div class="stat-item">
                <div class="stat-label">{{ $t('profile.accountStatus') }}</div>
                <div class="stat-value">
                  <q-badge :color="authStore.user?.email_verified_at ? 'positive' : 'warning'"
                    :label="authStore.user?.email_verified_at ? $t('profile.verified') : $t('profile.pending')" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Security Settings -->
    <div class="row q-mt-lg">
      <div class="col-12">
        <q-card class="security-card" flat bordered>
          <q-card-section class="card-header">
            <div class="card-title">
              <q-icon name="security" class="q-mr-sm" />
              {{ $t('profile.securitySettings') }}
            </div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="updatePassword" class="q-gutter-md">
              <div class="row q-gutter-md">
                <div class="col-12 col-md-4">
                  <q-input v-model="passwordForm.current_password" :label="$t('profile.currentPassword')"
                    type="password" outlined :rules="[val => !!val || $t('profile.currentPasswordRequired')]" />
                </div>

                <div class="col-12 col-md-4">
                  <q-input v-model="passwordForm.password" :label="$t('profile.newPassword')" type="password" outlined
                    :rules="[
                      val => !!val || $t('profile.newPasswordRequired'),
                      val => val.length >= 8 || $t('profile.passwordMinLength')
                    ]" />
                </div>

                <div class="col-12 col-md-4">
                  <q-input v-model="passwordForm.password_confirmation" :label="$t('profile.confirmNewPassword')"
                    type="password" outlined :rules="[
                      val => !!val || $t('profile.passwordConfirmationRequired'),
                      val => val === passwordForm.password || $t('profile.passwordsDoNotMatch')
                    ]" />
                </div>
              </div>

              <div class="form-actions">
                <q-btn type="submit" color="warning" :label="$t('profile.updatePassword')" :loading="updatingPassword"
                  icon="lock" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Preferences -->
    <div class="row q-mt-lg">
      <div class="col-12">
        <q-card class="preferences-card" flat bordered>
          <q-card-section class="card-header">
            <div class="card-title">
              <q-icon name="tune" class="q-mr-sm" />
              {{ $t('profile.preferences') }}
            </div>
          </q-card-section>

          <q-card-section>
            <div class="preferences-grid">
              <div class="preference-item">
                <div class="preference-info">
                  <div class="preference-label">{{ $t('profile.darkMode') }}</div>
                  <div class="preference-desc">{{ $t('profile.darkModeDesc') }}</div>
                </div>
                <q-toggle v-model="preferences.darkMode" @update:model-value="toggleDarkMode" color="primary" />
              </div>

              <div class="preference-item">
                <div class="preference-info">
                  <div class="preference-label">{{ $t('profile.emailNotifications') }}</div>
                  <div class="preference-desc">{{ $t('profile.emailNotificationsDesc') }}</div>
                </div>
                <q-toggle v-model="preferences.emailNotifications" @update:model-value="updatePreferences"
                  color="primary" />
              </div>

              <div class="preference-item">
                <div class="preference-info">
                  <div class="preference-label">{{ $t('profile.browserNotifications') }}</div>
                  <div class="preference-desc">{{ $t('profile.browserNotificationsDesc') }}</div>
                </div>
                <q-toggle v-model="preferences.browserNotifications" @update:model-value="updatePreferences"
                  color="primary" />
              </div>

              <div class="preference-item">
                <div class="preference-info">
                  <div class="preference-label">{{ $t('profile.weeklyReports') }}</div>
                  <div class="preference-desc">{{ $t('profile.weeklyReportsDesc') }}</div>
                </div>
                <q-toggle v-model="preferences.weeklyReports" @update:model-value="updatePreferences" color="primary" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from 'src/stores/auth'
import AvatarUpload from 'src/components/AvatarUpload.vue'

// Composables
const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// Reactive data
const updatingProfile = ref(false)
const updatingPassword = ref(false)

const profileForm = ref({
  name: '',
  email: '',
  phone: '',
  timezone: '',
  bio: ''
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const preferences = ref({
  darkMode: false,
  emailNotifications: true,
  browserNotifications: true,
  weeklyReports: false
})

const timezoneOptions = [
  { label: 'UTC+07:00 - Jakarta', value: 'Asia/Jakarta' },
  { label: 'UTC+08:00 - Singapore', value: 'Asia/Singapore' },
  { label: 'UTC+09:00 - Tokyo', value: 'Asia/Tokyo' },
  { label: 'UTC+00:00 - London', value: 'Europe/London' },
  { label: 'UTC-05:00 - New York', value: 'America/New_York' },
  { label: 'UTC-08:00 - Los Angeles', value: 'America/Los_Angeles' }
]

// Computed
// (userInitials removed - now using AvatarUpload component)

// Methods
const initializeForm = () => {
  if (authStore.user) {
    profileForm.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      phone: authStore.user.phone || '',
      timezone: authStore.user.timezone || 'Asia/Jakarta',
      bio: authStore.user.bio || ''
    }
  }
}

const initializePreferences = () => {
  preferences.value.darkMode = $q.dark.isActive
  // Load other preferences from localStorage
  const savedPrefs = localStorage.getItem('userPreferences')
  if (savedPrefs) {
    const parsed = JSON.parse(savedPrefs)
    Object.assign(preferences.value, parsed)
  }
}

const getRoleColor = (roleName) => {
  const colors = {
    'Super Admin': 'red',
    'Admin': 'orange',
    'Manager': 'blue',
    'User': 'green'
  }
  return colors[roleName] || 'grey'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const updateProfile = async () => {
  updatingProfile.value = true
  try {
    // Mock update - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1000))

    $q.notify({
      type: 'positive',
      message: t('profile.profileUpdated'),
      position: 'top'
    })
  } catch (error) {
    console.error('Failed to update profile:', error)
    $q.notify({
      type: 'negative',
      message: t('profile.updateFailed'),
      position: 'top'
    })
  } finally {
    updatingProfile.value = false
  }
}

const updatePassword = async () => {
  updatingPassword.value = true
  try {
    // Mock update - replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1000))

    // Clear form
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }

    $q.notify({
      type: 'positive',
      message: t('profile.passwordUpdated'),
      position: 'top'
    })
  } catch (error) {
    console.error('Failed to update password:', error)
    $q.notify({
      type: 'negative',
      message: t('profile.updateFailed'),
      position: 'top'
    })
  } finally {
    updatingPassword.value = false
  }
}

const toggleDarkMode = (value) => {
  $q.dark.set(value)
  localStorage.setItem('darkMode', value)
  updatePreferences()
}

const updatePreferences = () => {
  // Save preferences to localStorage
  localStorage.setItem('userPreferences', JSON.stringify(preferences.value))
}

// Avatar handlers
const handleAvatarUpdate = () => {
  $q.notify({
    type: 'positive',
    message: t('avatar.upload_success'),
    position: 'top'
  })
  
  // Optionally refresh user data in auth store
  authStore.refreshUser()
}

const handleAvatarDelete = () => {
  $q.notify({
    type: 'positive',
    message: t('avatar.delete_success'),
    position: 'top'
  })
  
  // Optionally refresh user data in auth store
  authStore.refreshUser()
}

const handleUploadProgress = (progressData) => {
  // Handle upload progress if needed
  console.log('Avatar upload progress:', progressData)
}

// Lifecycle
onMounted(() => {
  initializeForm()
  initializePreferences()
})
</script>

<style lang="scss" scoped>
.profile-page {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

// Page Header
.page-header {
  margin-bottom: 2rem;

  .header-content {
    .header-info {
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
  }
}

// Cards
.profile-card,
.avatar-card,
.security-card,
.preferences-card {
  border: none;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);

  .card-header {
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

// Avatar Card
.avatar-card {
  .avatar-section {
    .user-name {
      margin: 0 0 0.5rem 0;
      font-weight: 600;
      color: #2d3748;
    }

    .user-email {
      margin: 0 0 1rem 0;
      color: #718096;
    }

    .user-roles {
      display: flex;
      flex-wrap: wrap;
      gap: 0.25rem;
      justify-content: center;
    }
  }

  .user-stats {
    .stat-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;

      &:last-child {
        margin-bottom: 0;
      }

      .stat-label {
        font-weight: 500;
        color: #4a5568;
      }

      .stat-value {
        font-size: 0.875rem;
        color: #718096;
      }
    }
  }
}

// Form Actions
.form-actions {
  display: flex;
  gap: 1rem;
  padding-top: 1rem;
}

// Preferences
.preferences-grid {
  .preference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;

    &:last-child {
      border-bottom: none;
    }

    .preference-info {
      flex: 1;

      .preference-label {
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 0.25rem;
      }

      .preference-desc {
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
      .header-info {
        .page-title {
          color: #f7fafc;
        }

        .page-subtitle {
          color: #a0aec0;
        }
      }
    }
  }

  .profile-card,
  .avatar-card,
  .security-card,
  .preferences-card {
    .card-header {
      .card-title {
        color: #f7fafc;
      }
    }
  }

  .avatar-card {
    .avatar-section {
      .user-name {
        color: #f7fafc;
      }

      .user-email {
        color: #a0aec0;
      }
    }

    .user-stats {
      .stat-item {
        .stat-label {
          color: #e2e8f0;
        }

        .stat-value {
          color: #a0aec0;
        }
      }
    }
  }

  .preferences-grid {
    .preference-item {
      border-bottom-color: #4a5568;

      .preference-info {
        .preference-label {
          color: #f7fafc;
        }

        .preference-desc {
          color: #a0aec0;
        }
      }
    }
  }
}

// Responsive styles
@media (max-width: 1023px) {
  .profile-page {
    padding: 1rem;
  }
}

@media (max-width: 599px) {
  .profile-page {
    padding: 0.75rem;
  }

  .form-actions {
    flex-direction: column;

    .q-btn {
      width: 100%;
    }
  }
}
</style>
