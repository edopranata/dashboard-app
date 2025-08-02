<template>
  <q-page class="profile-page">
    <!-- Modern Profile Header -->
    <div class="profile-header">
      <div class="header-background">
        <div class="header-overlay"></div>
      </div>

      <div class="header-content">
        <div class="profile-info">
          <div class="avatar-container">
            <AvatarUpload :display-size="'140px'" @avatar-updated="handleAvatarUpdate"
              @avatar-deleted="handleAvatarDelete" @upload-progress="handleUploadProgress" />
          </div>

          <div class="user-details">
            <h3 class="user-name">{{ authStore.userName }}</h3>
            <p class="user-email">{{ authStore.userEmail }}</p>

            <div class="user-roles q-mt-sm">
              <q-chip v-for="role in authStore.userRoles" :key="role" :color="getRoleColor(role)" text-color="white"
                size="md" icon="admin_panel_settings">
                {{ role }}
              </q-chip>
            </div>

            <div class="user-meta q-mt-md">
              <div class="meta-item">
                <q-icon name="schedule" size="sm" />
                <span>{{ $t('profile.memberSince') }}: {{ formatDate(authStore.user?.created_at) }}</span>
              </div>
              <div class="meta-item">
                <q-icon name="login" size="sm" />
                <span>{{ $t('profile.lastLogin') }}: {{ formatDate(authStore.user?.last_login_at) }}</span>
              </div>
              <div class="meta-item">
                <q-icon name="verified" size="sm" />
                <q-badge :color="authStore.user?.email_verified_at ? 'positive' : 'warning'"
                  :label="authStore.user?.email_verified_at ? $t('profile.verified') : $t('profile.pending')" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="profile-tabs-container">
      <q-card flat class="tabs-card">
        <q-tabs v-model="activeTab" class="profile-tabs" active-color="primary" indicator-color="primary" align="left"
          narrow-indicator>
          <q-tab name="general" icon="person" :label="$t('profile.general')" />
          <q-tab name="security" icon="security" :label="$t('profile.security')" />
          <q-tab name="preferences" icon="tune" :label="$t('profile.preferences')" />
          <q-tab name="activity" icon="timeline" :label="$t('profile.activity')" />
        </q-tabs>
      </q-card>
    </div>

    <!-- Tab Panels -->
    <div class="tab-panels">
      <q-tab-panels v-model="activeTab" animated class="bg-transparent">

        <!-- General Information Tab -->
        <q-tab-panel name="general" class="q-pa-none">
          <q-card flat bordered class="content-card">
            <q-card-section class="card-header">
              <div class="section-title">
                <q-icon name="person" class="q-mr-sm" />
                {{ $t('profile.profileInformation') }}
              </div>
              <p class="section-subtitle">{{ $t('profile.updatePersonalInfo') }}</p>
            </q-card-section>

            <q-card-section>
              <q-form @submit="updateProfile" class="profile-form">
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
              <div class="section-title">
                <q-icon name="security" class="q-mr-sm" />
                {{ $t('profile.securitySettings') }}
              </div>
              <p class="section-subtitle">{{ $t('profile.updatePasswordDesc') }}</p>
            </q-card-section>

            <q-card-section>
              <q-form @submit="updatePassword" class="security-form">
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
              <div class="section-title">
                <q-icon name="tune" class="q-mr-sm" />
                {{ $t('profile.preferences') }}
              </div>
              <p class="section-subtitle">{{ $t('profile.customizeExperience') }}</p>
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
              <div class="section-title">
                <q-icon name="timeline" class="q-mr-sm" />
                {{ $t('profile.recentActivity') }}
              </div>
              <p class="section-subtitle">{{ $t('profile.activityDesc') }}</p>
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
import AvatarUpload from 'src/components/AvatarUpload.vue'

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
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
}

// Modern Profile Header
.profile-header {
  position: relative;
  margin-bottom: 2rem;

  .header-background {
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;

    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      opacity: 0.3;
    }

    .header-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 50px;
      background: linear-gradient(to top, rgba(255, 255, 255, 0.1), transparent);
    }
  }

  .header-content {
    position: absolute;
    bottom: -60px;
    left: 0;
    right: 0;
    padding: 0 2rem;

    .profile-info {
      display: flex;
      align-items: flex-end;
      gap: 2rem;
      background: white;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);

      .avatar-container {
        flex-shrink: 0;
        margin-bottom: -1rem;
      }

      .user-details {
        flex: 1;

        .user-name {
          margin: 0 0 0.5rem 0;
          font-size: 2rem;
          font-weight: 700;
          color: #2d3748;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
        }

        .user-email {
          margin: 0 0 1rem 0;
          color: #718096;
          font-size: 1.1rem;
        }

        .user-roles {
          display: flex;
          flex-wrap: wrap;
          gap: 0.5rem;
          margin-bottom: 1rem;
        }

        .user-meta {
          display: flex;
          flex-wrap: wrap;
          gap: 1.5rem;

          .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            font-size: 0.9rem;

            .q-icon {
              color: #667eea;
            }
          }
        }
      }
    }
  }
}

// Tab System
.profile-tabs-container {
  margin: 4rem 2rem 2rem 2rem;

  .tabs-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }

  .profile-tabs {
    background: white;

    .q-tab {
      padding: 1rem 2rem;
      font-weight: 600;
      text-transform: none;
      font-size: 1rem;

      &.q-tab--active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;

        .q-icon {
          color: white;
        }
      }
    }
  }
}

// Tab Panels
.tab-panels {
  padding: 0 2rem 2rem 2rem;

  .content-card {
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;

    .card-header {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      border-bottom: 1px solid #e2e8f0;
      padding: 2rem;

      .section-title {
        display: flex;
        align-items: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;

        .q-icon {
          color: #667eea;
        }
      }

      .section-subtitle {
        margin: 0;
        color: #718096;
        font-size: 1rem;
      }
    }
  }
}

// Forms
.profile-form,
.security-form {
  .form-row {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;

    .form-group {
      flex: 1;

      &.full-width {
        flex-basis: 100%;
      }

      .form-input {
        .q-field__control {
          border-radius: 12px;
        }

        .q-field__prepend {
          .q-icon {
            color: #667eea;
          }
        }
      }
    }
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;

    .action-btn {
      border-radius: 12px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      text-transform: none;
    }
  }
}

// Security Tips
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

// Preferences Section
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

    &:hover {
      background: #f1f5f9;
      border-color: #cbd5e0;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .preference-content {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex: 1;

      .preference-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;

        .q-icon {
          color: white;
        }
      }

      .preference-info {
        .preference-label {
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 0.25rem;
        }

        .preference-desc {
          font-size: 0.9rem;
          color: #718096;
          line-height: 1.4;
        }
      }
    }
  }
}

// Activity Timeline
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

    &:hover {
      background: #f1f5f9;
      transform: translateX(4px);
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
      }

      .activity-desc {
        font-size: 0.9rem;
        color: #718096;
      }
    }
  }
}

// Dark mode styles
.body--dark {
  .profile-page {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
  }

  .profile-header {
    .header-content {
      .profile-info {
        background: #2d3748;
        border-color: rgba(255, 255, 255, 0.1);

        .user-details {
          .user-name {
            color: #f7fafc;
            -webkit-text-fill-color: transparent;
          }

          .user-email {
            color: #a0aec0;
          }

          .user-meta {
            .meta-item {
              color: #e2e8f0;
            }
          }
        }
      }
    }
  }

  .profile-tabs-container {
    .profile-tabs {
      background: #2d3748;

      .q-tab {
        color: #a0aec0;

        &.q-tab--active {
          color: white;
        }
      }
    }
  }

  .content-card {
    background: #2d3748;
    border-color: rgba(255, 255, 255, 0.1);

    .card-header {
      background: linear-gradient(135deg, #374151 0%, #4a5568 100%);
      border-bottom-color: #4a5568;

      .section-title {
        color: #f7fafc;
      }

      .section-subtitle {
        color: #a0aec0;
      }
    }
  }

  .preferences-section {
    .preference-category {
      color: #f7fafc;
    }

    .preference-item {
      background: #374151;
      border-color: #4a5568;

      &:hover {
        background: #4a5568;
        border-color: #718096;
      }

      .preference-content {
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

  .activity-timeline {
    .activity-item {
      background: #374151;
      border-left-color: #667eea;

      &:hover {
        background: #4a5568;
      }

      .activity-content {
        .activity-title {
          color: #f7fafc;
        }

        .activity-desc {
          color: #a0aec0;
        }
      }
    }
  }

  .form-actions {
    border-top-color: #4a5568;
  }
}

// Responsive Design
@media (max-width: 1023px) {
  .profile-header {
    .header-content {
      padding: 0 1rem;

      .profile-info {
        flex-direction: column;
        text-align: center;
        gap: 1rem;

        .user-details {
          .user-meta {
            justify-content: center;
          }
        }
      }
    }
  }

  .profile-tabs-container,
  .tab-panels {
    margin-left: 1rem;
    margin-right: 1rem;
  }
}

@media (max-width: 599px) {
  .profile-header {
    .header-content {
      padding: 0 0.75rem;

      .profile-info {
        padding: 1.5rem;

        .user-details {
          .user-name {
            font-size: 1.5rem;
          }

          .user-meta {
            flex-direction: column;
            gap: 0.75rem;
          }
        }
      }
    }
  }

  .profile-tabs-container,
  .tab-panels {
    margin: 3rem 0.75rem 1rem 0.75rem;
  }

  .profile-form,
  .security-form {
    .form-row {
      flex-direction: column;
      gap: 1rem;
    }

    .form-actions {
      flex-direction: column;

      .action-btn {
        width: 100%;
      }
    }
  }

  .preferences-section {
    .preference-item {
      .preference-content {
        gap: 0.75rem;

        .preference-icon {
          width: 36px;
          height: 36px;
        }
      }
    }
  }
}
</style>
