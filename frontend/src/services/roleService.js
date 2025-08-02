import { api } from 'boot/axios'

export const roleService = {
  // Get all roles for form select options
  async getAllRoles() {
    try {
      const response = await api.get('/roles/all')
      return response.data
    } catch (error) {
      console.error('Error fetching roles:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch roles',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Get paginated roles for list page
  async getRoles(params = {}) {
    try {
      const response = await api.get('/roles', { params })
      return response.data
    } catch (error) {
      console.error('Error fetching roles:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch roles',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Get single role
  async getRole(id) {
    try {
      const response = await api.get(`/roles/${id}`)
      return response.data
    } catch (error) {
      console.error('Error fetching role:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch role',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Create new role
  async createRole(roleData) {
    try {
      const response = await api.post('/roles', roleData)
      return response.data
    } catch (error) {
      console.error('Error creating role:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to create role',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Update role
  async updateRole(id, roleData) {
    try {
      const response = await api.put(`/roles/${id}`, roleData)
      return response.data
    } catch (error) {
      console.error('Error updating role:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to update role',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Delete role
  async deleteRole(id) {
    try {
      const response = await api.delete(`/roles/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting role:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to delete role',
        errors: error.response?.data?.errors || {},
      }
    }
  },

  // Get permissions
  async getPermissions() {
    try {
      const response = await api.get('/roles/permissions')
      return response.data
    } catch (error) {
      console.error('Error fetching permissions:', error)
      throw {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch permissions',
        errors: error.response?.data?.errors || {},
      }
    }
  },
}
