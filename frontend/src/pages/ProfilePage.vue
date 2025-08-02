<template>
  <q-page class="container padded">
    <!-- Profile Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="header-info">
          <h1 class="page-title">{{ $t('profile.myProfile') }}</h1>
          <p class="page-subtitle">{{ $t('profile.managePersonalInfo') }}</p>
        </div>
      </div>
    </div>

    <!-- Profile Overview Card -->
    <q-card flat bordered class="content-card q-mb-lg">
      <q-card-section class="card-header">
        <div>
          <div class="section-title">
            <q-icon name="account_circle" />
            {{ $t('profile.profileOverview') }}
          </div>
          <p class="section-subtitle">{{ $t('profile.basicInformation') }}</p>
        </div>
      </q-card-section>

      <q-card-section>
        <div class="profile-overview">
          <div class="avatar-section">
            <div class="avatar-container">
              <SimpleAvatarUpload :display-size="'120px'" @avatar-updated="handleAvatarUpdate"
                @avatar-deleted="handleAvatarDelete" @upload-progress="handleUploadProgress" />
            </div>
          </div>

          <div class="user-info">
            <div class="user-basic">
              <h3 class="user-name">{{ authStore.userName }}</h3>
              <p class="user-email">{{ authStore.userEmail }}</p>

              <div class="user-roles q-mt-sm">
                <q-chip v-for="role in authStore.userRoles" :key="role" :color="getRoleColor(role)" text-color="white"
                  size="md" icon="admin_panel_settings">
                  {{ role }}
                </q-chip>
              </div>
            </div>

            <div class="user-meta q-mt-lg">
              <div class="meta-row">
                <div class="meta-item">
                  <q-icon name="schedule" size="sm" />
                  <span>{{ $t('profile.memberSince') }}: {{ formatDate(authStore.user?.created_at) }}</span>
                </div>
                <div class="meta-item">
                  <q-icon name="login" size="sm" />
                  <span>{{ $t('profile.lastLogin') }}: {{ formatDate(authStore.user?.last_login_at) }}</span>
                </div>
              </div>
              <div class="meta-row q-mt-sm">
                <div class="meta-item">
                  <q-icon name="verified" size="sm" />
                  <span>{{ $t('profile.emailStatus') }}:</span>
                  <q-badge :color="authStore.user?.email_verified_at ? 'positive' : 'warning'"
                    :label="authStore.user?.email_verified_at ? $t('profile.verified') : $t('profile.pending')" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tab Navigation using modern-tabs -->
    <div class="modern-tabs">
      <q-card flat class="tabs-card">
        <q-tabs v-model="activeTab" :active-color="$q.screen.darkMode ? 'white' : 'white'" indicator-color="primary" align="left" narrow-indicator>
          <q-tab name="general" icon="person" :label="$t('profile.general')" />
          <q-tab name="security" icon="security" :label="$t('profile.security')" />
          <q-tab name="preferences" icon="tune" :label="$t('profile.preferences')" />
          <q-tab name="activity" icon="timeline" :label="$t('profile.activity')" />
        </q-tabs>
      </q-card>

      <!-- Tab Panels -->
      <q-tab-panels v-model="activeTab" animated class="bg-transparent">

        <!-- General Information Tab -->
        <q-tab-panel name="general" class="q-pa-none">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div>
                <div class="section-title">
                  <q-icon name="person" />
                  {{ $t('profile.profileInformation') }}
                </div>
                <p class="section-subtitle">{{ $t('profile.updatePersonalInfo') }}</p>
              </div>
            </q-card-section>

            <q-card-section>
              <q-form @submit="updateProfile" class="standard-form">
                <div class="form-row">
                  <div class="form-group">
                    <q-input v-model="profileForm.name" :label="$t('profile.fullName')" outlined
                      :rules="[val => !!val || $t('profile.nameRequired')]" class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="person" />
                      </template>
                    </q-input>
                  </div>

                  <div class="form-group">
                    <q-input v-model="profileForm.email" :label="$t('profile.emailAddress')" type="email" outlined
                      :rules="[
                        val => !!val || $t('profile.emailRequired'),
                        val => /.+@.+\..+/.test(val) || $t('profile.validEmailRequired')
                      ]" class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="email" />
                      </template>
                    </q-input>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <q-input v-model="profileForm.phone" :label="$t('profile.phoneNumber')" outlined
                      mask="(###) ### - ####" unmasked-value class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="phone" />
                      </template>
                    </q-input>
                  </div>

                  <div class="form-group">
                    <q-select v-model="profileForm.timezone" :options="timezoneOptions" :label="$t('profile.timezone')"
                      outlined emit-value map-options class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="schedule" />
                      </template>
                    </q-select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group full-width">
                    <q-input v-model="profileForm.bio" :label="$t('profile.bio')" type="textarea" outlined rows="4"
                      counter maxlength="500" class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="description" />
                      </template>
                    </q-input>
                  </div>
                </div>

                <div class="form-actions">
                  <q-btn type="submit" color="primary" :label="$t('profile.updateProfile')" :loading="updatingProfile"
                    icon="save" size="md" class="action-btn" />
                  <q-btn type="button" color="grey-7" :label="$t('actions.cancel')" flat @click="resetForm"
                    class="action-btn" />
                </div>
              </q-form>
            </q-card-section>
          </q-card>
        </q-tab-panel>

        <!-- Security Tab -->
        <q-tab-panel name="security" class="q-pa-none">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div>
                <div class="section-title">
                  <q-icon name="security" />
                  {{ $t('profile.securitySettings') }}
                </div>
                <p class="section-subtitle">{{ $t('profile.updatePasswordDesc') }}</p>
              </div>
            </q-card-section>

            <q-card-section>
              <q-form @submit="updatePassword" class="standard-form">
                <div class="form-row">
                  <div class="form-group">
                    <q-input v-model="passwordForm.current_password" :label="$t('profile.currentPassword')"
                      type="password" outlined :rules="[val => !!val || $t('profile.currentPasswordRequired')]"
                      class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="lock" />
                      </template>
                    </q-input>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <q-input v-model="passwordForm.password" :label="$t('profile.newPassword')" type="password" outlined
                      :rules="[
                        val => !!val || $t('profile.newPasswordRequired'),
                        val => val.length >= 8 || $t('profile.passwordMinLength')
                      ]" class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="lock_reset" />
                      </template>
                    </q-input>
                  </div>

                  <div class="form-group">
                    <q-input v-model="passwordForm.password_confirmation" :label="$t('profile.confirmNewPassword')"
                      type="password" outlined :rules="[
                        val => !!val || $t('profile.passwordConfirmationRequired'),
                        val => val === passwordForm.password || $t('profile.passwordsDoNotMatch')
                      ]" class="form-input">
                      <template v-slot:prepend>
                        <q-icon name="lock_outline" />
                      </template>
                    </q-input>
                  </div>
                </div>

                <div class="security-tips">
                  <q-banner class="bg-blue-1 text-blue-8">
                    <template v-slot:avatar>
                      <q-icon name="info" color="blue" />
                    </template>
                    <div class="text-subtitle2">{{ $t('profile.passwordTips') }}</div>
                    <ul class="q-mt-sm">
                      <li>{{ $t('profile.passwordTip1') }}</li>
                      <li>{{ $t('profile.passwordTip2') }}</li>
                      <li>{{ $t('profile.passwordTip3') }}</li>
                    </ul>
                  </q-banner>
                </div>

                <div class="form-actions">
                  <q-btn type="submit" color="warning" :label="$t('profile.updatePassword')" :loading="updatingPassword"
                    icon="lock" size="md" class="action-btn" />
                </div>
              </q-form>
            </q-card-section>
          </q-card>
        </q-tab-panel>

        <!-- Preferences Tab -->
        <q-tab-panel name="preferences" class="q-pa-none">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div>
                <div class="section-title">
                  <q-icon name="tune" />
                  {{ $t('profile.preferences') }}
                </div>
                <p class="section-subtitle">{{ $t('profile.customizeExperience') }}</p>
              </div>
            </q-card-section>

            <q-card-section>
              <div class="preferences-section">
                <h6 class="preference-category">{{ $t('profile.appearance') }}</h6>
                <div class="preference-item">
                  <div class="preference-content">
                    <div class="preference-icon">
                      <q-icon name="dark_mode" size="md" />
                    </div>
                    <div class="preference-info">
                      <div class="preference-label">{{ $t('profile.darkMode') }}</div>
                      <div class="preference-desc">{{ $t('profile.darkModeDesc') }}</div>
                    </div>
                  </div>
                  <q-toggle v-model="preferences.darkMode" @update:model-value="toggleDarkMode" color="primary"
                    size="lg" />
                </div>
              </div>

              <q-separator class="q-my-lg" />

              <div class="preferences-section">
                <h6 class="preference-category">{{ $t('profile.notifications') }}</h6>

                <div class="preference-item">
                  <div class="preference-content">
                    <div class="preference-icon">
                      <q-icon name="email" size="md" />
                    </div>
                    <div class="preference-info">
                      <div class="preference-label">{{ $t('profile.emailNotifications') }}</div>
                      <div class="preference-desc">{{ $t('profile.emailNotificationsDesc') }}</div>
                    </div>
                  </div>
                  <q-toggle v-model="preferences.emailNotifications" @update:model-value="updatePreferences"
                    color="primary" size="lg" />
                </div>

                <div class="preference-item">
                  <div class="preference-content">
                    <div class="preference-icon">
                      <q-icon name="notifications" size="md" />
                    </div>
                    <div class="preference-info">
                      <div class="preference-label">{{ $t('profile.browserNotifications') }}</div>
                      <div class="preference-desc">{{ $t('profile.browserNotificationsDesc') }}</div>
                    </div>
                  </div>
                  <q-toggle v-model="preferences.browserNotifications" @update:model-value="updatePreferences"
                    color="primary" size="lg" />
                </div>

                <div class="preference-item">
                  <div class="preference-content">
                    <div class="preference-icon">
                      <q-icon name="assessment" size="md" />
                    </div>
                    <div class="preference-info">
                      <div class="preference-label">{{ $t('profile.weeklyReports') }}</div>
                      <div class="preference-desc">{{ $t('profile.weeklyReportsDesc') }}</div>
                    </div>
                  </div>
                  <q-toggle v-model="preferences.weeklyReports" @update:model-value="updatePreferences" color="primary"
                    size="lg" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </q-tab-panel>

        <!-- Activity Tab -->
        <q-tab-panel name="activity" class="q-pa-none">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div>
                <div class="section-title">
                  <q-icon name="timeline" />
                  {{ $t('profile.recentActivity') }}
                </div>
                <p class="section-subtitle">{{ $t('profile.activityDesc') }}</p>
              </div>
            </q-card-section>

            <q-card-section>
              <div class="activity-timeline">
                <div class="activity-item">
                  <div class="activity-icon">
                    <q-icon name="login" color="positive" />
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">{{ $t('profile.loginActivity') }}</div>
                    <div class="activity-desc">{{ formatDate(authStore.user?.last_login_at) }}</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-icon">
                    <q-icon name="person" color="info" />
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">{{ $t('profile.profileCreated') }}</div>
                    <div class="activity-desc">{{ formatDate(authStore.user?.created_at) }}</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-icon">
                    <q-icon name="verified" color="positive" />
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">{{ $t('profile.emailVerified') }}</div>
                    <div class="activity-desc">
                      {{ authStore.user?.email_verified_at ? formatDate(authStore.user.email_verified_at) :
                        $t('profile.pending') }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-center q-mt-lg">
                <q-btn flat color="primary" :label="$t('profile.viewFullActivity')" icon="arrow_forward"
                  @click="viewFullActivity" />
              </div>
            </q-card-section>
          </q-card>
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from 'src/stores/auth'
import SimpleAvatarUpload from 'src/components/SimpleAvatarUpload.vue'

// Composables
const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// Reactive data
const activeTab = ref('general')
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

const resetForm = () => {
  initializeForm()
  $q.notify({
    type: 'info',
    message: t('profile.formReset'),
    position: 'top'
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

  $q.notify({
    type: 'positive',
    message: t('profile.preferencesUpdated'),
    position: 'top'
  })
}

const viewFullActivity = () => {
  $q.notify({
    type: 'info',
    message: t('profile.featureComingSoon'),
    position: 'top'
  })
}

// Avatar handlers
const handleAvatarUpdate = () => {
  $q.notify({
    type: 'positive',
    message: t('avatar.upload_success'),
    position: 'top'
  })

  // No need to call fetchUser since we're updating the store directly in the component
  // In a real app with API integration, you would call: authStore.fetchUser()
}

const handleAvatarDelete = () => {
  $q.notify({
    type: 'positive',
    message: t('avatar.delete_success'),
    position: 'top'
  })

  // No need to call fetchUser since we're updating the store directly in the component
  // In a real app with API integration, you would call: authStore.fetchUser()
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
// =============================================================================
// PROFILE PAGE SPECIFIC STYLES
// =============================================================================
// Minimal custom styles untuk elements yang tidak ada di global styles

// Enhanced tabs styling for better visibility
.modern-tabs {
  .tabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
    border-radius: 16px;
    overflow: hidden;

    .body--dark & {
      background: #2d3748;
      border-color: #4a5568;
      box-shadow: 0 2px 16px rgba(0, 0, 0, 0.3);
    }
  }

  .q-tabs {
    background: #f1f5f9;
    border-bottom: 1px solid #d1d5db;

    .body--dark & {
      background: #1f2937;
      border-bottom-color: #374151;
    }

    .q-tab {
      padding: 1.25rem 2rem;
      font-weight: 600;
      text-transform: none;
      font-size: 1rem;
      color: #1a202c;
      border-radius: 0;
      transition: all 0.3s ease;
      position: relative;

      .body--dark & {
        color: #f7fafc;
      }

      &:hover {
        background: rgba(102, 126, 234, 0.08);
        color: #4c51bf;

        .body--dark & {
          background: rgba(102, 126, 234, 0.15);
          color: #c3dafe;
        }

        .q-icon {
          transform: scale(1.1);
          color: #4c51bf;

          .body--dark & {
            color: #c3dafe;
          }
        }
      }

      &.q-tab--active {
        background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(76, 81, 191, 0.4);

        .body--dark & {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: #ffffff;
          box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
        }

        .q-icon {
          color: #ffffff;
          transform: scale(1.1);
        }

        // Active tab indicator
        &::after {
          content: '';
          position: absolute;
          bottom: 0;
          left: 50%;
          transform: translateX(-50%);
          width: 40px;
          height: 3px;
          background: #ffffff;
          border-radius: 2px 2px 0 0;
        }
      }

      .q-icon {
        margin-right: 0.75rem;
        font-size: 1.25rem;
        transition: all 0.3s ease;
      }

      // Focus styles for accessibility
      &:focus-visible {
        outline: 2px solid #4c51bf;
        outline-offset: 2px;

        .body--dark & {
          outline-color: #667eea;
        }
      }
    }

    // Tab indicator styling
    .q-tabs__arrow {
      color: #4c51bf;

      .body--dark & {
        color: #c3dafe;
      }
    }

    .q-tabs__content {
      .q-tab__indicator {
        background: transparent; // We use custom indicator
      }
    }
  }

  .q-tab-panels {
    background: transparent;
    margin-top: 1.5rem;

    .q-tab-panel {
      padding: 0;
    }
  }

  // Responsive adjustments
  @media (max-width: 1023px) {
    .q-tabs {
      .q-tab {
        padding: 1rem 1.5rem;
        font-size: 0.95rem;

        .q-icon {
          font-size: 1.1rem;
        }
      }
    }
  }

  @media (max-width: 599px) {
    .tabs-card {
      border-radius: 12px;
    }

    .q-tabs {
      .q-tab {
        padding: 0.875rem 1rem;
        font-size: 0.9rem;
        min-height: auto;

        .q-icon {
          margin-right: 0.5rem;
          font-size: 1rem;
        }

        // Stack icon and text on very small screens
        @media (max-width: 480px) {
          flex-direction: column;
          padding: 0.75rem 0.5rem;
          gap: 0.25rem;

          .q-icon {
            margin-right: 0;
            font-size: 1.1rem;
          }
        }
      }
    }
  }
}

// Profile overview layout
.profile-overview {
  display: flex;
  gap: 2rem;
  align-items: flex-start;

  @media (max-width: 1023px) {
    flex-direction: column;
    gap: 1.5rem;
  }

  .avatar-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 200px;

    @media (max-width: 1023px) {
      align-self: center;
    }

    .avatar-container {
      margin-bottom: 1rem;
    }

    .avatar-info {
      .avatar-help {
        margin: 0 0 0.5rem 0;
        font-size: 0.9rem;
        color: #4a5568;
        font-weight: 500;

        .body--dark & {
          color: #a0aec0;
        }
      }

      .avatar-formats {
        margin: 0;
        font-size: 0.8rem;
        color: #718096;

        .body--dark & {
          color: #718096;
        }
      }
    }
  }

  .user-info {
    flex: 1;

    .user-basic {
      .user-name {
        margin: 0 0 0.5rem 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;

        .body--dark & {
          color: #f7fafc;
        }

        @media (max-width: 599px) {
          font-size: 1.5rem;
        }
      }

      .user-email {
        margin: 0 0 1rem 0;
        color: #718096;
        font-size: 1.1rem;

        .body--dark & {
          color: #a0aec0;
        }
      }

      .user-roles {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
      }
    }

    .user-meta {
      .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;

        @media (max-width: 599px) {
          flex-direction: column;
          gap: 0.75rem;
        }

        .meta-item {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          color: #4a5568;
          font-size: 0.9rem;

          .body--dark & {
            color: #e2e8f0;
          }

          .q-icon {
            color: #667eea;
          }
        }
      }
    }
  }
}

// Security tips styling
.security-tips {
  margin: 2rem 0;

  .q-banner {
    border-radius: 12px;
    border-left: 4px solid #3182ce;

    ul {
      margin: 0;
      padding-left: 1.5rem;

      li {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
      }
    }
  }
}

// Preferences section styling
.preferences-section {
  margin-bottom: 2rem;

  .preference-category {
    margin: 0 0 1.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 0.5rem;

    .body--dark & {
      color: #f7fafc;
    }

    &::before {
      content: '';
      width: 4px;
      height: 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 2px;
    }
  }

  .preference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;

    .body--dark & {
      background: #374151;
      border-color: #4a5568;
    }

    &:hover {
      background: #f1f5f9;
      border-color: #cbd5e0;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

      .body--dark & {
        background: #4a5568;
        border-color: #718096;
      }
    }

    .preference-content {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex: 1;

      @media (max-width: 599px) {
        gap: 0.75rem;
      }

      .preference-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;

        @media (max-width: 599px) {
          width: 36px;
          height: 36px;
        }

        .q-icon {
          color: white;
        }
      }

      .preference-info {
        .preference-label {
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 0.25rem;

          .body--dark & {
            color: #f7fafc;
          }
        }

        .preference-desc {
          font-size: 0.9rem;
          color: #718096;
          line-height: 1.4;

          .body--dark & {
            color: #a0aec0;
          }
        }
      }
    }
  }
}

// Activity timeline styling
.activity-timeline {
  .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;

    .body--dark & {
      background: #374151;
      border-left-color: #667eea;
    }

    &:hover {
      background: #f1f5f9;
      transform: translateX(4px);

      .body--dark & {
        background: #4a5568;
      }
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .activity-content {
      flex: 1;

      .activity-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;

        .body--dark & {
          color: #f7fafc;
        }
      }

      .activity-desc {
        font-size: 0.9rem;
        color: #718096;

        .body--dark & {
          color: #a0aec0;
        }
      }
    }
  }
}
</style>
