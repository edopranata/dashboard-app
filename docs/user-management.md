# User Management System

## Overview

Sistem manajemen pengguna dengan role-based access control (RBAC) menggunakan Laravel Permission package. System ini memungkinkan Super Admin untuk mengelola users, roles, dan permissions dengan fleksibel.

## User Hierarchy

### 1. Super Admin (Default)

- **Access Level**: Full system access
- **Permissions**: All permissions (\*)
- **Can**: Manage users, roles, permissions, system settings
- **Default Account**:
  - Email: `admin@dashboard.com`
  - Password: `SuperAdmin123!`

### 2. Owner

- **Access Level**: High-level management
- **Permissions**: Manage users, view reports, manage content
- **Can**: Create/edit users (except Super Admin), view analytics
- **Cannot**: Modify system roles, delete Super Admin

### 3. User

- **Access Level**: Basic user access
- **Permissions**: View own profile, basic operations
- **Can**: Update own profile, view assigned content
- **Cannot**: Manage other users, access admin features

## Database Schema

### Users Table

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,  -- UUID
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Roles Table (Laravel Permission)

```sql
CREATE TABLE roles (
    id CHAR(36) PRIMARY KEY,  -- UUID
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Permissions Table (Laravel Permission)

```sql
CREATE TABLE permissions (
    id CHAR(36) PRIMARY KEY,  -- UUID
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Default Permissions

### User Management

- `view_users` - View users list
- `create_users` - Create new users
- `edit_users` - Edit existing users
- `delete_users` - Delete users
- `manage_users` - Full user management

### Role Management

- `view_roles` - View roles list
- `create_roles` - Create new roles
- `edit_roles` - Edit existing roles
- `delete_roles` - Delete roles
- `assign_roles` - Assign roles to users

### System Management

- `view_dashboard` - Access dashboard
- `view_reports` - View system reports
- `manage_settings` - Manage system settings
- `view_logs` - View system logs

## Backend Implementation

### User Model

```php
<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }
}
```

### User Controller

```php
<?php
// app/Http/Controllers/API/UserController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_users');

        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create_users');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load('roles')
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('edit_users');

        // Prevent editing Super Admin by non-Super Admin
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot edit Super Admin account');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user->update($request->only('name', 'email'));
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->load('roles')
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete_users');

        // Prevent deleting Super Admin
        if ($user->isSuperAdmin()) {
            abort(403, 'Cannot delete Super Admin account');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            abort(403, 'Cannot delete your own account');
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
```

### Policies

```php
<?php
// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_users') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_users');
    }

    public function update(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $user->can('edit_users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() || $user->id === $model->id) {
            return false;
        }

        return $user->can('delete_users');
    }
}
```

## Frontend Implementation

### User Store (Pinia)

```javascript
// stores/users.js
import { defineStore } from "pinia";
import { api } from "boot/axios";

export const useUsersStore = defineStore("users", {
  state: () => ({
    users: [],
    pagination: {
      page: 1,
      rowsPerPage: 15,
      rowsNumber: 0,
    },
    loading: false,
    filters: {
      search: "",
      role: "",
    },
  }),

  actions: {
    async fetchUsers(params = {}) {
      this.loading = true;
      try {
        const response = await api.get("/users", { params });
        this.users = response.data.data.data;
        this.pagination.rowsNumber = response.data.data.total;
        this.pagination.page = response.data.data.current_page;
      } catch (error) {
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createUser(userData) {
      const response = await api.post("/users", userData);
      await this.fetchUsers(); // Refresh list
      return response.data;
    },

    async updateUser(id, userData) {
      const response = await api.put(`/users/${id}`, userData);
      await this.fetchUsers(); // Refresh list
      return response.data;
    },

    async deleteUser(id) {
      await api.delete(`/users/${id}`);
      await this.fetchUsers(); // Refresh list
    },
  },
});
```

### User List Component

```vue
<!-- pages/users/UserListPage.vue -->
<template>
  <q-page padding>
    <div class="q-pa-lg">
      <div class="row items-center justify-between q-mb-lg">
        <h4 class="text-h4 q-my-none">{{ $t("users.title") }}</h4>
        <q-btn
          color="primary"
          icon="add"
          :label="$t('users.addUser')"
          @click="showCreateDialog = true"
          v-if="$can('create_users')"
        />
      </div>

      <q-card>
        <q-card-section>
          <div class="row q-gutter-md q-mb-md">
            <q-input
              v-model="filters.search"
              :placeholder="$t('common.search')"
              outlined
              dense
              clearable
              @update:model-value="debouncedSearch"
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>

            <q-select
              v-model="filters.role"
              :options="roleOptions"
              :placeholder="$t('users.filterByRole')"
              outlined
              dense
              clearable
              @update:model-value="fetchUsers"
            />
          </div>

          <q-table
            :rows="users"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            @request="onRequest"
            row-key="id"
          >
            <template v-slot:body-cell-roles="props">
              <q-td :props="props">
                <q-chip
                  v-for="role in props.row.roles"
                  :key="role.id"
                  :color="getRoleColor(role.name)"
                  text-color="white"
                  size="sm"
                >
                  {{ role.name }}
                </q-chip>
              </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
              <q-td :props="props">
                <q-btn
                  flat
                  round
                  icon="edit"
                  @click="editUser(props.row)"
                  v-if="$can('edit_users')"
                />
                <q-btn
                  flat
                  round
                  icon="delete"
                  @click="deleteUser(props.row)"
                  v-if="$can('delete_users') && !props.row.is_super_admin"
                />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </div>

    <!-- Create/Edit Dialog -->
    <UserFormDialog
      v-model="showCreateDialog"
      :user="selectedUser"
      @saved="onUserSaved"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useUsersStore } from "stores/users";
import { useRolesStore } from "stores/roles";
import { debounce } from "quasar";
import UserFormDialog from "components/users/UserFormDialog.vue";

const usersStore = useUsersStore();
const rolesStore = useRolesStore();

const showCreateDialog = ref(false);
const selectedUser = ref(null);

const users = computed(() => usersStore.users);
const loading = computed(() => usersStore.loading);
const pagination = computed(() => usersStore.pagination);
const filters = computed(() => usersStore.filters);

const roleOptions = computed(() =>
  rolesStore.roles.map((role) => ({
    label: role.name,
    value: role.name,
  }))
);

const columns = [
  {
    name: "name",
    label: "Name",
    field: "name",
    align: "left",
    sortable: true,
  },
  {
    name: "email",
    label: "Email",
    field: "email",
    align: "left",
    sortable: true,
  },
  {
    name: "roles",
    label: "Roles",
    field: "roles",
    align: "left",
  },
  {
    name: "created_at",
    label: "Created",
    field: "created_at",
    align: "left",
    sortable: true,
    format: (val) => new Date(val).toLocaleDateString(),
  },
  {
    name: "actions",
    label: "Actions",
    field: "actions",
    align: "center",
  },
];

const debouncedSearch = debounce(() => {
  fetchUsers();
}, 500);

const fetchUsers = async () => {
  await usersStore.fetchUsers({
    page: pagination.value.page,
    per_page: pagination.value.rowsPerPage,
    search: filters.value.search,
    role: filters.value.role,
  });
};

const onRequest = (props) => {
  const { page, rowsPerPage } = props.pagination;
  usersStore.pagination.page = page;
  usersStore.pagination.rowsPerPage = rowsPerPage;
  fetchUsers();
};

const editUser = (user) => {
  selectedUser.value = { ...user };
  showCreateDialog.value = true;
};

const deleteUser = async (user) => {
  // Show confirmation dialog
  const confirmed = await $q.dialog({
    title: "Confirm Delete",
    message: `Are you sure you want to delete ${user.name}?`,
    cancel: true,
    persistent: true,
  });

  if (confirmed) {
    await usersStore.deleteUser(user.id);
  }
};

const onUserSaved = () => {
  showCreateDialog.value = false;
  selectedUser.value = null;
  fetchUsers();
};

const getRoleColor = (roleName) => {
  const colors = {
    "Super Admin": "red",
    Owner: "purple",
    User: "blue",
  };
  return colors[roleName] || "grey";
};

onMounted(() => {
  fetchUsers();
  rolesStore.fetchRoles();
});
</script>
```

## Security Considerations

### 1. Prevention Rules

- Super Admin cannot be deleted
- Users cannot delete themselves
- Only Super Admin can edit Super Admin accounts
- Role assignments are validated against existing roles

### 2. Authentication & Authorization

- All API endpoints require valid Bearer token
- Policy-based authorization for each action
- Role-based route protection in frontend

### 3. Data Validation

- Server-side validation for all user inputs
- Email uniqueness validation
- Password complexity requirements
- XSS prevention with proper escaping

### 4. Audit Trail

```php
// Optional: Add user activity logging
class UserObserver
{
    public function created(User $user)
    {
        activity()
            ->performedOn($user)
            ->log('User created');
    }

    public function updated(User $user)
    {
        activity()
            ->performedOn($user)
            ->log('User updated');
    }

    public function deleted(User $user)
    {
        activity()
            ->performedOn($user)
            ->log('User deleted');
    }
}
```

## Testing

### Backend Tests

```php
// tests/Feature/UserManagementTest.php
class UserManagementTest extends TestCase
{
    public function test_super_admin_can_create_users()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['User']
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_cannot_delete_super_admin()
    {
        $user = User::factory()->create();
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/users/{$superAdmin->id}");

        $response->assertStatus(403);
    }
}
```
