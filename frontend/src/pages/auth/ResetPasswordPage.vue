<template>
    <div class="reset-password-page">
        <q-form @submit="handleResetPassword" class="reset-form">
            <!-- Header -->
            <div class="form-header">
                <h5>{{ $t('auth.resetPassword') }}</h5>
                <p>{{ $t('auth.enterNewPassword') }}</p>
            </div>

            <!-- New Password field -->
            <div class="form-group">
                <q-input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                    :label="$t('auth.newPassword')" outlined dense :loading="loading" :error="!!errors.password"
                    :error-message="errors.password" @update:model-value="clearError('password')" class="form-input">
                    <template v-slot:prepend>
                        <q-icon name="lock" color="grey-6" />
                    </template>
                    <template v-slot:append>
                        <q-icon :name="showPassword ? 'visibility_off' : 'visibility'" class="cursor-pointer"
                            color="grey-6" @click="showPassword = !showPassword" />
                    </template>
                </q-input>
            </div>

            <!-- Confirm Password field -->
            <div class="form-group">
                <q-input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                    :label="$t('auth.confirmNewPassword')" outlined dense :loading="loading"
                    :error="!!errors.password_confirmation" :error-message="errors.password_confirmation"
                    @update:model-value="clearError('password_confirmation')" class="form-input">
                    <template v-slot:prepend>
                        <q-icon name="lock" color="grey-6" />
                    </template>
                    <template v-slot:append>
                        <q-icon :name="showConfirmPassword ? 'visibility_off' : 'visibility'" class="cursor-pointer"
                            color="grey-6" @click="showConfirmPassword = !showConfirmPassword" />
                    </template>
                </q-input>
            </div>

            <!-- Submit button -->
            <q-btn type="submit" color="primary" :label="$t('auth.resetPassword')" :loading="loading"
                :disable="!isFormValid || loading" class="submit-btn" no-caps rounded>
                <template v-slot:loading>
                    <q-spinner-hourglass class="on-left" />
                    {{ $t('auth.resetting') }}
                </template>
            </q-btn>

            <!-- Back to login -->
            <div class="back-to-login">
                <router-link to="/auth/login" class="back-link">
                    <q-icon name="arrow_back" size="16px" />
                    {{ $t('auth.backToSignIn') }}
                </router-link>
            </div>
        </q-form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Notify } from 'quasar'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

const form = ref({
    password: '',
    password_confirmation: '',
    token: route.query.token || '',
    email: route.query.email || ''
})

const errors = ref({})
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const isFormValid = computed(() => {
    return form.value.password &&
        form.value.password_confirmation &&
        form.value.token &&
        form.value.email
})

const clearError = (field) => {
    if (errors.value[field]) {
        delete errors.value[field]
    }
}

const validateForm = () => {
    const newErrors = {}

    if (!form.value.password) {
        newErrors.password = t('auth.validation.passwordRequired')
    } else if (form.value.password.length < 8) {
        newErrors.password = t('auth.validation.passwordMinLength')
    }

    if (!form.value.password_confirmation) {
        newErrors.password_confirmation = t('auth.validation.confirmPasswordRequired')
    } else if (form.value.password !== form.value.password_confirmation) {
        newErrors.password_confirmation = t('auth.validation.passwordsMismatch')
    }

    errors.value = newErrors
    return Object.keys(newErrors).length === 0
}

const handleResetPassword = async () => {
    if (!validateForm()) {
        return
    }

    loading.value = true

    try {
        // This would call your API
        // const result = await api.post('/auth/reset-password', form.value)

        // Simulate API call for now
        await new Promise(resolve => setTimeout(resolve, 1000))

        Notify.create({
            type: 'positive',
            message: t('common.messages.passwordResetSuccess'),
            position: 'top'
        })

        // Redirect to login
        router.push('/auth/login')

    } catch (error) {
        console.error('Reset password error:', error)
        Notify.create({
            type: 'negative',
            message: t('common.messages.passwordResetError'),
            position: 'top'
        })
    } finally {
        loading.value = false
    }
}
</script>

<style lang="scss" scoped>
.reset-password-page {
    width: 100%;
}

.reset-form {
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
