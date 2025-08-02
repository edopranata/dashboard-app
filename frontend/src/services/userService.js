import { api } from 'boot/axios'

export const userService = {
  /**
   * Get all users with pagination and filters
   */
  async getUsers(params = {}) {
    try {
      const response = await api.get('/users', { params })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Get single user by ID
   */
  async getUser(id) {
    try {
      const response = await api.get(`/users/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Create new user
   */
  async createUser(userData) {
    try {
      const response = await api.post('/users', userData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Update user
   */
  async updateUser(id, userData) {
    try {
      const response = await api.put(`/users/${id}`, userData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Delete user
   */
  async deleteUser(id) {
    try {
      const response = await api.delete(`/users/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}
