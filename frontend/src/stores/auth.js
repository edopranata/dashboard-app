import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { Notify } from 'quasar'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: false,
    loading: false,
    permissions: [],
    roles: [],
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || '',
    userRoles: (state) => state.user?.roles || [],
    userPermissions: (state) => state.user?.permissions || [],

    // Permission checker
    hasPermission: (state) => (permission) => {
      return state.user?.permissions?.includes(permission) || false
    },

    // Role checker
    hasRole: (state) => (role) => {
      return state.user?.roles?.includes(role) || false
    },

    // Super admin checker
    isSuperAdmin: (state) => {
      return state.user?.roles?.includes('Super Admin') || false
    },
  },

  actions: {
    // Initialize auth state
    async initAuth() {
      if (this.token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        await this.fetchUser()
      }
    },

    // Login action
    async login(credentials) {
      this.loading = true
      try {
        const response = await api.post('/auth/login', credentials)

        if (response.data.success) {
          const { user, token } = response.data.data

          this.token = token
          this.user = user
          this.isAuthenticated = true

          // Store token in localStorage
          localStorage.setItem('token', token)

          // Set default authorization header
          api.defaults.headers.common['Authorization'] = `Bearer ${token}`

          Notify.create({
            type: 'positive',
            message: 'Login successful!',
            position: 'top',
          })

          return { success: true }
        }
      } catch (error) {
        const message = error.response?.data?.message || 'Login failed'
        Notify.create({
          type: 'negative',
          message,
          position: 'top',
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    // Fetch current user
    async fetchUser() {
      try {
        const response = await api.get('/auth/me')
        if (response.data.success) {
          this.user = response.data.data
          this.isAuthenticated = true
        }
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.logout()
      }
    },

    // Logout action
    async logout() {
      this.loading = true
      try {
        // Call logout API if token exists
        if (this.token) {
          await api.post('/auth/logout')
        }
      } catch (error) {
        console.error('Logout API failed:', error)
      } finally {
        // Always clear local state
        this.clearAuthData()
        this.loading = false

        Notify.create({
          type: 'info',
          message: 'Logged out successfully',
          position: 'top',
        })
      }
    },

    // Clear authentication data
    clearAuthData() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      this.permissions = []
      this.roles = []

      // Remove from localStorage
      localStorage.removeItem('token')

      // Clear authorization header
      delete api.defaults.headers.common['Authorization']
    },

    // Update profile
    async updateProfile(profileData) {
      this.loading = true
      try {
        const response = await api.put('/profile', profileData)

        if (response.data.success) {
          this.user = { ...this.user, ...response.data.data }

          Notify.create({
            type: 'positive',
            message: 'Profile updated successfully!',
            position: 'top',
          })

          return { success: true }
        }
      } catch (error) {
        const message = error.response?.data?.message || 'Profile update failed'
        Notify.create({
          type: 'negative',
          message,
          position: 'top',
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    // Change password
    async changePassword(passwordData) {
      this.loading = true
      try {
        const response = await api.put('/profile/password', passwordData)

        if (response.data.success) {
          Notify.create({
            type: 'positive',
            message: 'Password changed successfully!',
            position: 'top',
          })

          return { success: true }
        }
      } catch (error) {
        const message = error.response?.data?.message || 'Password change failed'
        Notify.create({
          type: 'negative',
          message,
          position: 'top',
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },

    // Forgot password
    async forgotPassword(email) {
      this.loading = true
      try {
        const response = await api.post('/auth/forgot-password', { email })

        if (response.data.success) {
          Notify.create({
            type: 'positive',
            message: 'Password reset email sent!',
            position: 'top',
          })

          return { success: true }
        }
      } catch (error) {
        const message = error.response?.data?.message || 'Failed to send reset email'
        Notify.create({
          type: 'negative',
          message,
          position: 'top',
        })
        return { success: false, message }
      } finally {
        this.loading = false
      }
    },
  },
})
