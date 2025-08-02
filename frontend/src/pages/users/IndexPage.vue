<template>
    <q-page class="users-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-info">
                    <h4 class="page-title">User Management</h4>
                    <p class="page-subtitle">Manage system users and their permissions</p>
                </div>
                <div class="header-actions">
                    <q-btn v-if="authStore.hasPermission('create_users')" color="primary" icon="person_add"
                        label="Add User" :to="{ name: 'users.create' }" no-caps />
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <q-card class="filter-card" flat bordered>
            <q-card-section>
                <div class="filter-content">
                    <div class="search-section">
                        <q-input v-model="searchQuery" outlined placeholder="Search users..." dense clearable
                            @input="handleSearch">
                            <template v-slot:prepend>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </div>

                    <div class="filter-section">
                        <q-select v-model="selectedRole" :options="roleOptions" outlined dense label="Filter by Role"
                            clearable emit-value map-options @update:model-value="handleFilter" />

                        <q-select v-model="selectedStatus" :options="statusOptions" outlined dense
                            label="Filter by Status" clearable emit-value map-options
                            @update:model-value="handleFilter" />
                    </div>
                </div>
            </q-card-section>
        </q-card>

        <!-- Users Table -->
        <q-card class="table-card" flat bordered>
            <q-card-section class="q-pa-none">
                <q-table :rows="filteredUsers" :columns="columns" row-key="id" :loading="loading"
                    :pagination="pagination" @request="onRequest" binary-state-sort>
                    <!-- Avatar Column -->
                    <template v-slot:body-cell-avatar="props">
                        <q-td :props="props">
                            <q-avatar size="40px" color="primary" text-color="white">
                                {{ getUserInitials(props.row.name) }}
                            </q-avatar>
                        </q-td>
                    </template>

                    <!-- Name Column -->
                    <template v-slot:body-cell-name="props">
                        <q-td :props="props">
                            <div class="user-name-cell">
                                <div class="user-name">{{ props.row.name }}</div>
                                <div class="user-email">{{ props.row.email }}</div>
                            </div>
                        </q-td>
                    </template>

                    <!-- Roles Column -->
                    <template v-slot:body-cell-roles="props">
                        <q-td :props="props">
                            <div class="roles-cell">
                                <q-chip v-for="role in props.row.roles" :key="role.id" :color="getRoleColor(role.name)"
                                    text-color="white" size="sm" dense>
                                    {{ role.name }}
                                </q-chip>
                            </div>
                        </q-td>
                    </template>

                    <!-- Status Column -->
                    <template v-slot:body-cell-status="props">
                        <q-td :props="props">
                            <q-badge :color="props.row.email_verified_at ? 'positive' : 'warning'"
                                :label="props.row.email_verified_at ? 'Active' : 'Pending'" />
                        </q-td>
                    </template>

                    <!-- Created At Column -->
                    <template v-slot:body-cell-created_at="props">
                        <q-td :props="props">
                            {{ formatDate(props.row.created_at) }}
                        </q-td>
                    </template>

                    <!-- Actions Column -->
                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props">
                            <div class="actions-cell">
                                <q-btn flat round icon="visibility" size="sm" color="primary"
                                    @click="viewUser(props.row)">
                                    <q-tooltip>View User</q-tooltip>
                                </q-btn>

                                <q-btn v-if="authStore.hasPermission('update_users')" flat round icon="edit" size="sm"
                                    color="secondary" :to="{ name: 'users.edit', params: { id: props.row.id } }">
                                    <q-tooltip>Edit User</q-tooltip>
                                </q-btn>

                                <q-btn
                                    v-if="authStore.hasPermission('delete_users') && props.row.id !== authStore.user?.id"
                                    flat round icon="delete" size="sm" color="negative"
                                    @click="confirmDeleteUser(props.row)">
                                    <q-tooltip>Delete User</q-tooltip>
                                </q-btn>
                            </div>
                        </q-td>
                    </template>

                    <!-- Loading Template -->
                    <template v-slot:loading>
                        <q-inner-loading showing color="primary" />
                    </template>

                    <!-- No Data Template -->
                    <template v-slot:no-data="{ message }">
                        <div class="full-width row flex-center q-gutter-sm">
                            <q-icon size="2em" name="sentiment_dissatisfied" />
                            <span>{{ message }}</span>
                        </div>
                    </template>
                </q-table>
            </q-card-section>
        </q-card>

        <!-- User Details Dialog -->
        <q-dialog v-model="showUserDialog" :maximized="$q.screen.lt.sm">
            <q-card style="min-width: 500px; max-width: 800px">
                <q-card-section class="row items-center q-pb-none">
                    <div class="text-h6">User Details</div>
                    <q-space />
                    <q-btn icon="close" flat round dense v-close-popup />
                </q-card-section>

                <q-card-section v-if="selectedUser">
                    <div class="user-details">
                        <div class="user-header">
                            <q-avatar size="80px" color="primary" text-color="white" class="q-mb-md">
                                {{ getUserInitials(selectedUser.name) }}
                            </q-avatar>
                            <div class="user-info">
                                <h6 class="user-name">{{ selectedUser.name }}</h6>
                                <p class="user-email">{{ selectedUser.email }}</p>
                                <q-badge :color="selectedUser.email_verified_at ? 'positive' : 'warning'"
                                    :label="selectedUser.email_verified_at ? 'Active' : 'Pending'" />
                            </div>
                        </div>

                        <q-separator class="q-my-md" />

                        <div class="user-meta">
                            <div class="meta-item">
                                <div class="meta-label">Roles:</div>
                                <div class="meta-value">
                                    <q-chip v-for="role in selectedUser.roles" :key="role.id"
                                        :color="getRoleColor(role.name)" text-color="white" size="sm">
                                        {{ role.name }}
                                    </q-chip>
                                </div>
                            </div>

                            <div class="meta-item">
                                <div class="meta-label">Created:</div>
                                <div class="meta-value">{{ formatDate(selectedUser.created_at) }}</div>
                            </div>

                            <div class="meta-item">
                                <div class="meta-label">Last Updated:</div>
                                <div class="meta-value">{{ formatDate(selectedUser.updated_at) }}</div>
                            </div>
                        </div>
                    </div>
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn v-if="authStore.hasPermission('update_users')" color="primary" label="Edit User"
                        :to="{ name: 'users.edit', params: { id: selectedUser?.id } }"
                        @click="showUserDialog = false" />
                    <q-btn flat label="Close" color="grey" v-close-popup />
                </q-card-actions>
            </q-card>
        </q-dialog>

        <!-- Delete Confirmation Dialog -->
        <q-dialog v-model="showDeleteDialog">
            <q-card>
                <q-card-section class="row items-center">
                    <q-avatar icon="warning" color="negative" text-color="white" />
                    <span class="q-ml-sm">Are you sure you want to delete this user?</span>
                </q-card-section>

                <q-card-section>
                    <p>This action cannot be undone. The user "{{ userToDelete?.name }}" will be permanently removed
                        from the
                        system.</p>
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" color="primary" v-close-popup />
                    <q-btn label="Delete" color="negative" @click="deleteUser" :loading="deleting" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth'
import { api } from 'src/boot/axios'

// Composables
const $q = useQuasar()
const authStore = useAuthStore()

// Reactive data
const loading = ref(false)
const deleting = ref(false)
const users = ref([])
const searchQuery = ref('')
const selectedRole = ref(null)
const selectedStatus = ref(null)
const showUserDialog = ref(false)
const showDeleteDialog = ref(false)
const selectedUser = ref(null)
const userToDelete = ref(null)

const pagination = ref({
    sortBy: 'created_at',
    descending: true,
    page: 1,
    rowsPerPage: 10
})

// Table columns
const columns = [
    {
        name: 'avatar',
        label: '',
        field: 'avatar',
        align: 'center',
        sortable: false,
        style: 'width: 60px'
    },
    {
        name: 'name',
        label: 'User',
        field: 'name',
        align: 'left',
        sortable: true
    },
    {
        name: 'roles',
        label: 'Roles',
        field: 'roles',
        align: 'left',
        sortable: false
    },
    {
        name: 'status',
        label: 'Status',
        field: 'email_verified_at',
        align: 'center',
        sortable: true
    },
    {
        name: 'created_at',
        label: 'Created',
        field: 'created_at',
        align: 'left',
        sortable: true
    },
    {
        name: 'actions',
        label: 'Actions',
        field: 'actions',
        align: 'center',
        sortable: false,
        style: 'width: 120px'
    }
]

// Filter options
const roleOptions = ref([])
const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Pending', value: 'pending' }
]

// Computed
const filteredUsers = computed(() => {
    let filtered = users.value

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(user =>
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query)
        )
    }

    // Role filter
    if (selectedRole.value) {
        filtered = filtered.filter(user =>
            user.roles.some(role => role.name === selectedRole.value)
        )
    }

    // Status filter
    if (selectedStatus.value) {
        filtered = filtered.filter(user => {
            if (selectedStatus.value === 'active') {
                return user.email_verified_at
            } else {
                return !user.email_verified_at
            }
        })
    }

    return filtered
})

// Methods
const fetchUsers = async () => {
    loading.value = true
    try {
        const response = await api.get('/users')
        users.value = response.data.data || response.data
    } catch (error) {
        console.error('Failed to fetch users:', error)
        $q.notify({
            type: 'negative',
            message: 'Failed to load users'
        })
    } finally {
        loading.value = false
    }
}

const fetchRoles = async () => {
    try {
        const response = await api.get('/roles')
        const roles = response.data.data || response.data
        roleOptions.value = roles.map(role => ({
            label: role.name,
            value: role.name
        }))
    } catch (error) {
        console.error('Failed to fetch roles:', error)
    }
}

const onRequest = (props) => {
    pagination.value = props.pagination
    // Implement server-side pagination if needed
}

const handleSearch = () => {
    // Search is handled by computed property
}

const handleFilter = () => {
    // Filter is handled by computed property
}

const getUserInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map(n => n.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
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
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const viewUser = (user) => {
    selectedUser.value = user
    showUserDialog.value = true
}

const confirmDeleteUser = (user) => {
    userToDelete.value = user
    showDeleteDialog.value = true
}

const deleteUser = async () => {
    if (!userToDelete.value) return

    deleting.value = true
    try {
        await api.delete(`/users/${userToDelete.value.id}`)

        // Remove user from local list
        const index = users.value.findIndex(u => u.id === userToDelete.value.id)
        if (index > -1) {
            users.value.splice(index, 1)
        }

        $q.notify({
            type: 'positive',
            message: 'User deleted successfully'
        })

        showDeleteDialog.value = false
        userToDelete.value = null
    } catch (error) {
        console.error('Failed to delete user:', error)
        $q.notify({
            type: 'negative',
            message: 'Failed to delete user'
        })
    } finally {
        deleting.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchUsers()
    fetchRoles()
})
</script>

<style lang="scss" scoped>
.users-page {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

// Page Header
.page-header {
    margin-bottom: 2rem;

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;

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

        .header-actions {
            flex-shrink: 0;
        }
    }
}

// Filter Card
.filter-card {
    margin-bottom: 1.5rem;

    .filter-content {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;

        .search-section {
            flex: 1;
            min-width: 250px;
        }

        .filter-section {
            display: flex;
            gap: 1rem;

            .q-select {
                min-width: 150px;
            }
        }
    }
}

// Table Card
.table-card {
    .user-name-cell {
        .user-name {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .user-email {
            font-size: 0.875rem;
            color: #718096;
        }
    }

    .roles-cell {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .actions-cell {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }
}

// User Details Dialog
.user-details {
    .user-header {
        display: flex;
        align-items: center;
        gap: 1rem;

        .user-info {
            .user-name {
                margin: 0 0 0.5rem 0;
                font-weight: 600;
                color: #2d3748;
            }

            .user-email {
                margin: 0 0 0.5rem 0;
                color: #718096;
            }
        }
    }

    .user-meta {
        .meta-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;

            .meta-label {
                font-weight: 500;
                color: #4a5568;
                min-width: 100px;
            }

            .meta-value {
                flex: 1;
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

    .table-card {
        .user-name-cell {
            .user-name {
                color: #f7fafc;
            }

            .user-email {
                color: #a0aec0;
            }
        }
    }

    .user-details {
        .user-header {
            .user-info {
                .user-name {
                    color: #f7fafc;
                }

                .user-email {
                    color: #a0aec0;
                }
            }
        }

        .user-meta {
            .meta-item {
                .meta-label {
                    color: #e2e8f0;
                }
            }
        }
    }
}

// Responsive styles
@media (max-width: 1023px) {
    .users-page {
        padding: 1rem;
    }

    .page-header {
        .header-content {
            flex-direction: column;
            align-items: stretch;

            .header-actions {
                align-self: flex-start;
            }
        }
    }

    .filter-card {
        .filter-content {
            flex-direction: column;
            align-items: stretch;

            .filter-section {
                flex-wrap: wrap;
            }
        }
    }
}

@media (max-width: 599px) {
    .users-page {
        padding: 0.75rem;
    }

    .user-details {
        .user-header {
            flex-direction: column;
            text-align: center;
        }
    }
}
</style>
