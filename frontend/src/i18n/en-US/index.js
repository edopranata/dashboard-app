export default {
  // Common Actions
  actions: {
    create: 'Create',
    edit: 'Edit',
    delete: 'Delete',
    save: 'Save',
    cancel: 'Cancel',
    close: 'Close',
    back: 'Back',
    next: 'Next',
    previous: 'Previous',
    submit: 'Submit',
    reset: 'Reset',
    clear: 'Clear',
    refresh: 'Refresh',
    search: 'Search',
    filter: 'Filter',
    export: 'Export',
    import: 'Import',
    view: 'View',
    manage: 'Manage',
    update: 'Update'
  },

  // Navigation
  nav: {
    dashboard: 'Dashboard',
    users: 'Users',
    roles: 'Roles',
    permissions: 'Permissions',
    profile: 'Profile',
    settings: 'Settings',
    logout: 'Logout'
  },

  // Authentication
  auth: {
    login: 'Login',
    register: 'Register',
    logout: 'Logout',
    email: 'Email',
    password: 'Password',
    confirmPassword: 'Confirm Password',
    rememberMe: 'Remember Me',
    forgotPassword: 'Forgot Password?',
    welcomeBack: 'Welcome Back!',
    loginSuccess: 'Login successful!',
    logoutSuccess: 'Logout successful!',
    invalidCredentials: 'Invalid email or password',
    loginRequired: 'Please login first',
    pleaseEnterEmailPassword: 'Please enter your email and password',
    signInToContinue: 'Sign in to continue',
    dontHaveAccount: "Don't have an account?",
    alreadyHaveAccount: 'Already have an account?',
    createAccount: 'Create Account',
    enterEmail: 'Enter your email',
    enterPassword: 'Enter your password',
    passwordMustBeAtLeast: 'Password must be at least 6 characters',
    emailIsRequired: 'Email is required',
    passwordIsRequired: 'Password is required',
    validEmailRequired: 'Please enter a valid email address'
  },

  // Dashboard
  dashboard: {
    title: 'Dashboard',
    welcome: 'Welcome back, {name}!',
    subtitle: 'Here\'s what\'s happening with your dashboard today.',
    stats: {
      totalUsers: 'Total Users',
      totalRoles: 'Total Roles',
      activeToday: 'Active Today',
      permissions: 'Permissions'
    },
    recentActivity: 'Recent Activity',
    quickActions: 'Quick Actions',
    systemStatus: 'System Status',
    testNotification: 'Test Notify',
    quickAction: 'Quick Action',
    database: 'Database',
    apiServer: 'API Server',
    cache: 'Cache',
    online: 'Online',
    warning: 'Warning',
    offline: 'Offline'
  },

  // Users
  users: {
    title: 'Users',
    createUser: 'Create User',
    editUser: 'Edit User',
    addUser: 'Add User',
    addNewUser: 'Add New User',
    manageUsers: 'Manage Users',
    userList: 'User List',
    userDetails: 'User Details',
    name: 'Name',
    email: 'Email',
    roles: 'Roles',
    status: 'Status',
    createdAt: 'Created At',
    updatedAt: 'Updated At',
    active: 'Active',
    inactive: 'Inactive',
    noUsers: 'No users found',
    deleteConfirm: 'Are you sure you want to delete this user?',
    userCreated: 'User created successfully',
    userUpdated: 'User updated successfully',
    userDeleted: 'User deleted successfully',
    selectRoles: 'Select Roles',
    assignRoles: 'Assign Roles'
  },

  // Roles
  roles: {
    title: 'Roles',
    createRole: 'Create Role',
    editRole: 'Edit Role',
    addRole: 'Add Role',
    roleList: 'Role List',
    roleDetails: 'Role Details',
    name: 'Role Name',
    description: 'Description',
    permissions: 'Permissions',
    users: 'Users',
    noRoles: 'No roles found',
    deleteConfirm: 'Are you sure you want to delete this role?',
    roleCreated: 'Role created successfully',
    roleUpdated: 'Role updated successfully',
    roleDeleted: 'Role deleted successfully',
    selectPermissions: 'Select Permissions',
    assignPermissions: 'Assign Permissions',
    permissionCategories: {
      userManagement: 'User Management',
      roleManagement: 'Role Management',
      systemAccess: 'System Access',
      administration: 'Administration'
    }
  },

  // Permissions
  permissions: {
    title: 'Permissions',
    name: 'Permission Name',
    description: 'Description',
    category: 'Category',
    // User Management
    view_users: 'View Users',
    create_users: 'Create Users',
    edit_users: 'Edit Users',
    delete_users: 'Delete Users',
    manage_users: 'Manage Users',
    // Role Management
    view_roles: 'View Roles',
    create_roles: 'Create Roles',
    edit_roles: 'Edit Roles',
    delete_roles: 'Delete Roles',
    assign_roles: 'Assign Roles',
    // System Access
    view_dashboard: 'View Dashboard',
    view_analytics: 'View Analytics',
    view_profile: 'View Profile',
    edit_profile: 'Edit Profile',
    // Administration
    manage_settings: 'Manage Settings',
    view_logs: 'View Logs',
    system_admin: 'System Admin',
    view_permissions: 'View Permissions',
    manage_permissions: 'Manage Permissions'
  },

  // Settings
  settings: {
    title: 'Settings',
    general: 'General',
    appearance: 'Appearance',
    language: 'Language',
    theme: 'Theme',
    lightMode: 'Light Mode',
    darkMode: 'Dark Mode',
    systemMode: 'Follow System',
    notifications: 'Notifications',
    privacy: 'Privacy',
    security: 'Security'
  },

  // Language Switcher
  language: {
    title: 'Language',
    english: 'English',
    indonesian: 'Bahasa Indonesia',
    changeLanguage: 'Change Language',
    languageChanged: 'Language changed successfully'
  },

  // Common Messages
  messages: {
    success: 'Success!',
    error: 'An error occurred!',
    warning: 'Warning!',
    info: 'Information',
    loading: 'Loading...',
    noData: 'No data available',
    confirmDelete: 'Are you sure you want to delete this item?',
    itemDeleted: 'Item deleted successfully',
    itemCreated: 'Item created successfully',
    itemUpdated: 'Item updated successfully',
    actionCompleted: 'Action completed successfully',
    actionFailed: 'Action failed',
    networkError: 'Network error',
    serverError: 'Server error',
    validationError: 'Validation error',
    accessDenied: 'Access denied',
    notFound: 'Not found',
    comingSoon: 'Coming Soon',
    pageNotFound: 'Oops. Nothing here...',
    goHome: 'Go Home'
  },

  // Validation
  validation: {
    required: 'This field is required',
    email: 'Invalid email format',
    minLength: 'Minimum {min} characters required',
    maxLength: 'Maximum {max} characters allowed',
    min: 'Minimum value is {min}',
    max: 'Maximum value is {max}',
    numeric: 'Only numbers are allowed',
    alphaNumeric: 'Only letters and numbers are allowed',
    confirmed: 'Confirmation does not match',
    unique: 'This value is already taken'
  },

  // Quick Actions
  quickActions: {
    title: 'Quick Actions',
    addUser: 'Add User',
    createRole: 'Create Role',
    viewAnalytics: 'View Analytics',
    systemSettings: 'System Settings',
    testDialog: 'Test Dialog'
  },

  // System Status
  system: {
    status: 'Status',
    responseTime: 'Response Time',
    usage: 'Usage',
    storage: 'Storage'
  },

  // Common
  common: {
    yes: 'Yes',
    no: 'No',
    ok: 'OK',
    confirm: 'Confirm',
    warning: 'Warning',
    error: 'Error',
    success: 'Success',
    info: 'Information',
    loading: 'Loading...',
    noData: 'No data available',
    selectAll: 'Select All',
    deselectAll: 'Deselect All',
    required: 'Required',
    optional: 'Optional',
    underDevelopment: 'Under Development',
    underDevelopmentDescription: 'This feature is currently being developed and will be available soon. Thank you for your patience!',
    backToDashboard: 'Back to Dashboard',
    developmentProgress: 'Development Progress',
    progressComplete: '{percent}% Complete'
  },

  // Time/Date
  time: {
    minutesAgo: '{count} minutes ago',
    hoursAgo: '{count} hours ago',
    daysAgo: '{count} days ago',
    weeksAgo: '{count} weeks ago',
    monthsAgo: '{count} months ago',
    yearsAgo: '{count} years ago',
    justNow: 'Just now'
  }
}
