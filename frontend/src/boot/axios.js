import { defineBoot } from '#q-app/wrappers'
import axios from 'axios'
import { Notify } from 'quasar'

// Create API instance with base configuration
const api = axios.create({
  baseURL:
    process.env.NODE_ENV === 'production'
      ? 'https://your-production-api.com/api'
      : 'http://127.0.0.1:8001/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Handle different error status codes
    if (error.response) {
      const { status, data } = error.response

      switch (status) {
        case 401:
          // Unauthorized - clear auth and redirect to login
          localStorage.removeItem('token')
          delete api.defaults.headers.common['Authorization']

          // Only show notification if not already on login page
          if (!window.location.pathname.includes('/login')) {
            Notify.create({
              type: 'negative',
              message: 'Session expired. Please login again.',
              position: 'top',
            })

            // Redirect to login page
            window.location.href = '/login'
          }
          break

        case 403:
          Notify.create({
            type: 'negative',
            message: data.message || 'Access denied',
            position: 'top',
          })
          break

        case 404:
          Notify.create({
            type: 'negative',
            message: 'Resource not found',
            position: 'top',
          })
          break

        case 422:
          // Validation errors - let the component handle these
          break

        case 500:
          Notify.create({
            type: 'negative',
            message: 'Server error. Please try again later.',
            position: 'top',
          })
          break

        default:
          Notify.create({
            type: 'negative',
            message: data.message || 'An error occurred',
            position: 'top',
          })
      }
    } else if (error.request) {
      // Network error
      Notify.create({
        type: 'negative',
        message: 'Network error. Please check your connection.',
        position: 'top',
      })
    }

    return Promise.reject(error)
  },
)

export default defineBoot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { api }
