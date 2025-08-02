<template>
    <div class="login-page">
        <q-form @submit="handleLogin" class="login-form">
            <!-- Welcome text -->
            <div class="welcome-text">
                <h5>Welcome back!</h5>
                <p>Please sign in to your account</p>
            </div>

            <!-- Email field -->
            <div class="form-group">
                <q-input v-model="form.email" type="email" label="Email Address" outlined dense
                    :loading="authStore.loading" :error="!!errors.email" :error-message="errors.email"
                    @update:model-value="clearError('email')" class="form-input">
                    <template v-slot:prepend>
                        <q-icon name="mail" color="grey-6" />
                    </template>
                </q-input>
            </div>

            <!-- Password field -->
            <div class="form-group">
                <q-input v-model="form.password" :type="showPassword ? 'text' : 'password'" label="Password" outlined
                    dense :loading="authStore.loading" :error="!!errors.password" :error-message="errors.password"
                    @update:model-value="clearError('password')" class="form-input">
                    <template v-slot:prepend>
                        <q-icon name="lock" color="grey-6" />
                    </template>
                    <template v-slot:append>
                        <q-icon :name="showPassword ? 'visibility_off' : 'visibility'" class="cursor-pointer"
                            color="grey-6" @click="showPassword = !showPassword" />
                    </template>
                </q-input>
            </div>

            <!-- Remember me & Forgot password -->
            <div class="form-options">
                <q-checkbox v-model="form.remember" label="Remember me" color="primary" :disable="authStore.loading"
                    class="remember-checkbox" />

                <router-link to="/auth/forgot-password" class="forgot-password-link">
                    Forgot password?
                </router-link>
            </div>

            <!-- Login button -->
            <q-btn type="submit" color="primary" label="Sign In" :loading="authStore.loading" :disable="!isFormValid"
                class="login-btn" no-caps rounded>
                <template v-slot:loading>
                    <q-spinner-hourglass class="on-left" />
                    Signing in...
                </template>
            </q-btn>

            <!-- Demo credentials -->
            <div class="demo-credentials">
                <p class="demo-title">Demo Credentials:</p>
                <div class="demo-accounts">
                    <q-btn flat dense no-caps color="grey-7" @click="fillDemoCredentials('admin')"
                        :disable="authStore.loading" class="demo-btn">
                        Super Admin
                    </q-btn>
                    <q-btn flat dense no-caps color="grey-7" @click="fillDemoCredentials('owner')"
                        :disable="authStore.loading" class="demo-btn">
                        Owner
                    </q-btn>
                    <q-btn flat dense no-caps color="grey-7" @click="fillDemoCredentials('user')"
                        :disable="authStore.loading" class="demo-btn">
                        User
                    </q-btn>
                </div>
            </div>
        </q-form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from 'src/stores/auth'

// Composables
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// Reactive data
const form = ref({
    email: '',
    password: '',
    remember: false
})

const errors = ref({})
const showPassword = ref(false)

// Computed
const isFormValid = computed(() => {
    return form.value.email && form.value.password && !authStore.loading
})

// Methods
const clearError = (field) => {
    if (errors.value[field]) {
        delete errors.value[field]
    }
}

const clearAllErrors = () => {
    errors.value = {}
}

const validateForm = () => {
    const newErrors = {}

    if (!form.value.email) {
        newErrors.email = 'Email is required'
    } else if (!isValidEmail(form.value.email)) {
        newErrors.email = 'Please enter a valid email'
    }

    if (!form.value.password) {
        newErrors.password = 'Password is required'
    } else if (form.value.password.length < 6) {
        newErrors.password = 'Password must be at least 6 characters'
    }

    errors.value = newErrors
    return Object.keys(newErrors).length === 0
}

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
}

const handleLogin = async () => {
    clearAllErrors()

    if (!validateForm()) {
        return
    }

    try {
        const result = await authStore.login({
            email: form.value.email,
            password: form.value.password
        })

        if (result.success) {
            // Redirect to originally requested page or dashboard
            const redirectPath = route.query.redirect || '/'
            await router.push(redirectPath)
        } else {
            // Handle specific login errors
            if (result.message?.includes('credentials')) {
                errors.value.password = 'Invalid email or password'
            }
        }
    } catch (error) {
        console.error('Login error:', error)
    }
}

const fillDemoCredentials = (type) => {
    const credentials = {
        admin: {
            email: 'admin@dashboard.com',
            password: 'SuperAdmin123!'
        },
        owner: {
            email: 'owner@dashboard.com',
            password: 'Owner123!'
        },
        user: {
            email: 'user@dashboard.com',
            password: 'User123!'
        }
    }

    if (credentials[type]) {
        form.value.email = credentials[type].email
        form.value.password = credentials[type].password
        clearAllErrors()
    }
}
</script>

<style lang="scss" scoped>
.login-page {
    width: 100%;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.welcome-text {
    text-align: center;
    margin-bottom: 1rem;

    h5 {
        margin: 0 0 0.5rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 1.4rem;
    }

    p {
        margin: 0;
        color: #718096;
        font-size: 0.9rem;
    }
}

.form-group {
    .form-input {
        width: 100%;

        :deep(.q-field__control) {
            border-radius: 8px;
        }
    }
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: -0.5rem 0;

    .remember-checkbox {
        :deep(.q-checkbox__label) {
            font-size: 0.9rem;
            color: #4a5568;
        }
    }

    .forgot-password-link {
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;

        &:hover {
            text-decoration: underline;
        }
    }
}

.login-btn {
    width: 100%;
    height: 44px;
    font-weight: 600;
    font-size: 1rem;

    :deep(.q-btn__content) {
        gap: 8px;
    }
}

.demo-credentials {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    text-align: center;

    .demo-title {
        margin: 0 0 0.5rem;
        font-size: 0.8rem;
        color: #718096;
        font-weight: 500;
    }

    .demo-accounts {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;

        .demo-btn {
            font-size: 0.75rem;
            padding: 4px 8px;
            min-height: auto;

            &:hover {
                background-color: #f7fafc;
            }
        }
    }
}

// Dark mode support
.body--dark {
    .welcome-text {
        h5 {
            color: #f7fafc;
        }

        p {
            color: #a0aec0;
        }
    }

    .form-options {
        .remember-checkbox {
            :deep(.q-checkbox__label) {
                color: #e2e8f0;
            }
        }

        .forgot-password-link {
            color: #90cdf4;
        }
    }

    .demo-credentials {
        border-top-color: #4a5568;

        .demo-title {
            color: #a0aec0;
        }

        .demo-accounts {
            .demo-btn:hover {
                background-color: #4a5568;
            }
        }
    }
}

// Responsive design
@media (max-width: 480px) {
    .form-options {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }

    .demo-accounts {
        .demo-btn {
            font-size: 0.7rem;
            padding: 3px 6px;
        }
    }
}
</style>
