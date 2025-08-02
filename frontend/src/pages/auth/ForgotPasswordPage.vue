<template>
    <div class="forgot-password-page">
        <q-form @submit="handleForgotPassword" class="forgot-form">
            <!-- Header -->
            <div class="form-header">
                <h5>Forgot Password?</h5>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>

            <!-- Email field -->
            <div class="form-group">
                <q-input v-model="email" type="email" label="Email Address" outlined dense :loading="loading"
                    :error="!!error" :error-message="error" @update:model-value="error = ''" class="form-input">
                    <template v-slot:prepend>
                        <q-icon name="mail" color="grey-6" />
                    </template>
                </q-input>
            </div>

            <!-- Submit button -->
            <q-btn type="submit" color="primary" label="Send Reset Link" :loading="loading" :disable="!email || loading"
                class="submit-btn" no-caps rounded>
                <template v-slot:loading>
                    <q-spinner-hourglass class="on-left" />
                    Sending...
                </template>
            </q-btn>

            <!-- Back to login -->
            <div class="back-to-login">
                <router-link to="/auth/login" class="back-link">
                    <q-icon name="arrow_back" size="16px" />
                    Back to Sign In
                </router-link>
            </div>
        </q-form>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from 'src/stores/auth'

const authStore = useAuthStore()

const email = ref('')
const error = ref('')
const loading = ref(false)

const handleForgotPassword = async () => {
    if (!email.value) {
        error.value = 'Email is required'
        return
    }

    loading.value = true
    error.value = ''

    try {
        const result = await authStore.forgotPassword(email.value)
        if (result.success) {
            // Success notification is handled in the store
        } else {
            error.value = result.message || 'Failed to send reset email'
        }
    } catch (error) {
        console.error('Forgot password error:', error)
        error.value = 'An error occurred. Please try again.'
    } finally {
        loading.value = false
    }
}
</script>

<style lang="scss" scoped>
.forgot-password-page {
    width: 100%;
}

.forgot-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-header {
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
        line-height: 1.4;
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

.submit-btn {
    width: 100%;
    height: 44px;
    font-weight: 600;
    font-size: 1rem;
}

.back-to-login {
    text-align: center;
    margin-top: 0.5rem;

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;

        &:hover {
            text-decoration: underline;
        }
    }
}

// Dark mode support
.body--dark {
    .form-header {
        h5 {
            color: #f7fafc;
        }

        p {
            color: #a0aec0;
        }
    }

    .back-to-login {
        .back-link {
            color: #90cdf4;
        }
    }
}
</style>
