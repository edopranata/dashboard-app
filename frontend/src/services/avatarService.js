import { api } from 'boot/axios'

class AvatarService {
  /**
   * Upload user avatar
   * @param {File} file - The avatar file to upload
   * @returns {Promise} API response
   */
  async uploadAvatar(file) {
    const formData = new FormData()
    formData.append('avatar', file)

    return api.post('/avatar/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  }

  /**
   * Delete user avatar
   * @returns {Promise} API response
   */
  async deleteAvatar() {
    return api.delete('/avatar/delete')
  }

  /**
   * Get avatar URL for current user
   * @param {string} size - Avatar size (original, thumbnail, small)
   * @returns {Promise} API response
   */
  async getAvatarUrl(size = 'original') {
    return api.get(`/avatar/show/${size}`)
  }

  /**
   * Get avatar URL for specific user (for admin)
   * @param {string} userId - User ID
   * @param {string} size - Avatar size
   * @returns {Promise} API response
   */
  async getUserAvatarUrl(userId, size = 'original') {
    return api.get(`/users/${userId}/avatar/${size}`)
  }
}

export default new AvatarService()
