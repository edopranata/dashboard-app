import { api } from 'boot/axios'

export const profileService = {
  /**
   * Update user profile
   */
  async updateProfile(profileData) {
    try {
      const response = await api.put('/profile', profileData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Change user password
   */
  async changePassword(passwordData) {
    try {
      const response = await api.put('/profile/password', passwordData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Upload user avatar
   */
  async uploadAvatar(file) {
    try {
      const formData = new FormData()
      formData.append('avatar', file)
      
      const response = await api.post('/profile/avatar', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  /**
   * Delete user avatar
   */
  async deleteAvatar() {
    try {
      const response = await api.delete('/profile/avatar')
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}
