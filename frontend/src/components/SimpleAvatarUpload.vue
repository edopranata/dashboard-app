<template>
    <div class="simple-avatar-upload">
        <!-- Avatar Display -->
        <div class="avatar-display">
            <q-avatar :size="displaySize" class="avatar-preview" :class="{ 'avatar-loading': uploading }">
                <img v-if="currentAvatarUrl" :src="currentAvatarUrl" :alt="$t('avatar.user_avatar')"
                    @error="handleImageError" />
                <q-icon v-else name="person" :size="iconSize" class="text-grey-6" />

                <!-- Loading overlay -->
                <div v-if="uploading" class="absolute-full flex flex-center bg-black-4">
                    <q-spinner color="white" size="24px" />
                </div>
            </q-avatar>
        </div>

        <!-- Upload Button -->
        <div class="upload-actions q-mt-md">
            <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp" style="display: none"
                @change="handleFileSelect" />

            <q-btn color="primary" outline :loading="uploading" @click="triggerFileInput"
                :label="$t('avatar.change_avatar')" icon="photo_camera" size="sm" :disable="disabled" />

            <q-btn v-if="currentAvatarUrl && !isDefault" color="negative" outline :loading="deleting"
                @click="confirmDelete" :label="$t('avatar.delete_avatar')" icon="delete" size="sm" class="q-ml-sm" />
        </div>

        <!-- File Info -->
        <div class="file-info q-mt-sm text-center">
            <div class="text-caption text-grey-6">
                {{ $t('avatar.supported_formats') }}
            </div>
            <div class="text-caption text-grey-5">
                {{ $t('avatar.max_file_size') }}
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <q-dialog v-model="showDeleteDialog">
            <q-card>
                <q-card-section>
                    <div class="text-h6">{{ $t('avatar.confirm_delete') }}</div>
                </q-card-section>

                <q-card-section class="q-pt-none">
                    {{ $t('avatar.delete_confirmation_message') }}
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat :label="$t('common.cancel')" color="grey-7" v-close-popup />
                    <q-btn flat :label="$t('common.delete')" color="negative" @click="deleteAvatar"
                        :loading="deleting" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from 'src/stores/auth'

// Props
const props = defineProps({
    size: {
        type: String,
        default: 'original',
        validator: (value) => ['original', 'thumbnail', 'small'].includes(value)
    },
    displaySize: {
        type: String,
        default: '120px'
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

// Emits
const emit = defineEmits(['avatar-updated', 'avatar-deleted', 'upload-progress'])

// Composables
const $q = useQuasar()
const { t } = useI18n()
const authStore = useAuthStore()

// Reactive data
const fileInput = ref(null)
const currentAvatarUrl = ref(null)
const isDefault = ref(true)
const uploading = ref(false)
const deleting = ref(false)
const showDeleteDialog = ref(false)

// Computed
const iconSize = computed(() => {
    const size = parseInt(props.displaySize.replace('px', ''))
    return `${Math.floor(size * 0.4)}px`
})

// Methods
const loadCurrentAvatar = async () => {
    try {
        // First check localStorage for demo avatar
        const savedAvatar = localStorage.getItem('userAvatar')

        if (savedAvatar) {
            currentAvatarUrl.value = savedAvatar
            isDefault.value = false
        } else if (authStore.user?.avatar) {
            currentAvatarUrl.value = authStore.user.avatar
            isDefault.value = false
        } else {
            currentAvatarUrl.value = null
            isDefault.value = true
        }
    } catch (error) {
        console.error('Failed to load avatar:', error)
        currentAvatarUrl.value = null
        isDefault.value = true
    }
}

const triggerFileInput = () => {
    if (!props.disabled && !uploading.value) {
        fileInput.value?.click()
    }
}

const handleFileSelect = (event) => {
    const file = event.target.files[0]
    if (file) {
        uploadAvatar(file)
    }
}

const uploadAvatar = async (file) => {
    // Validate file
    if (!validateFile(file)) {
        return
    }

    uploading.value = true

    try {
        // Create FormData
        const formData = new FormData()
        formData.append('avatar', file)

        // Emit upload progress
        emit('upload-progress', { progress: 0 })

        // TODO: Replace with actual API call when backend is ready
        // const response = await api.post('/profile/avatar', formData, {
        //   headers: { 'Content-Type': 'multipart/form-data' },
        //   onUploadProgress: (progressEvent) => {
        //     const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        //     emit('upload-progress', { progress })
        //   }
        // })

        // For demo purposes, simulate upload progress and create preview
        for (let i = 10; i <= 90; i += 10) {
            await new Promise(resolve => setTimeout(resolve, 100))
            emit('upload-progress', { progress: i })
        }

        // Create preview URL for demo
        const reader = new FileReader()
        reader.onload = async (e) => {
            const newAvatarUrl = e.target.result
            currentAvatarUrl.value = newAvatarUrl
            isDefault.value = false

            // Update user in auth store with new avatar
            if (authStore.user) {
                authStore.user.avatar = newAvatarUrl

                // Persist to localStorage for demo
                localStorage.setItem('userAvatar', newAvatarUrl)
            }

            emit('upload-progress', { progress: 100 })
            emit('avatar-updated', { url: newAvatarUrl })

            $q.notify({
                type: 'positive',
                message: t('avatar.upload_success'),
                position: 'top'
            })
        }
        reader.readAsDataURL(file)

        // In production, use this instead:
        // currentAvatarUrl.value = response.data.data.avatar_url
        // await authStore.fetchUser() // Refresh user data from server

    } catch (error) {
        console.error('Failed to upload avatar:', error)
        $q.notify({
            type: 'negative',
            message: t('avatar.upload_failed'),
            position: 'top'
        })
    } finally {
        uploading.value = false
        // Clear file input
        if (fileInput.value) {
            fileInput.value.value = ''
        }
    }
}

const validateFile = (file) => {
    // Check file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp']
    if (!allowedTypes.includes(file.type)) {
        $q.notify({
            type: 'negative',
            message: t('avatar.invalid_file_type'),
            position: 'top'
        })
        return false
    }

    // Check file size (2MB limit)
    const maxSize = 2 * 1024 * 1024 // 2MB in bytes
    if (file.size > maxSize) {
        $q.notify({
            type: 'negative',
            message: t('avatar.file_too_large'),
            position: 'top'
        })
        return false
    }

    return true
}

const confirmDelete = () => {
    showDeleteDialog.value = true
}

const deleteAvatar = async () => {
    deleting.value = true

    try {
        // TODO: Replace with actual API call when backend is ready
        // await api.delete('/profile/avatar')

        // Mock delete request for demo
        await new Promise(resolve => setTimeout(resolve, 800))

        currentAvatarUrl.value = null
        isDefault.value = true
        showDeleteDialog.value = false

        // Update auth store
        if (authStore.user) {
            authStore.user.avatar = null
            // Remove from localStorage
            localStorage.removeItem('userAvatar')
        }

        emit('avatar-deleted')

        $q.notify({
            type: 'positive',
            message: t('avatar.delete_success'),
            position: 'top'
        })

        // In production, refresh user data:
        // await authStore.fetchUser()

    } catch (error) {
        console.error('Failed to delete avatar:', error)
        $q.notify({
            type: 'negative',
            message: t('avatar.delete_failed'),
            position: 'top'
        })
    } finally {
        deleting.value = false
    }
}

const handleImageError = () => {
    currentAvatarUrl.value = null
    isDefault.value = true
}

// Lifecycle
onMounted(() => {
    loadCurrentAvatar()
})

// Watch for auth store changes
watch(() => authStore.user?.avatar, (newAvatar) => {
    // Check localStorage first for demo avatar
    const savedAvatar = localStorage.getItem('userAvatar')

    if (savedAvatar) {
        currentAvatarUrl.value = savedAvatar
        isDefault.value = false
    } else if (newAvatar) {
        currentAvatarUrl.value = newAvatar
        isDefault.value = false
    } else {
        currentAvatarUrl.value = null
        isDefault.value = true
    }
})
</script>

<style lang="scss" scoped>
.simple-avatar-upload {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;

    .avatar-display {
        position: relative;

        .avatar-preview {
            border: 3px solid #e2e8f0;
            transition: all 0.3s ease;

            .body--dark & {
                border-color: #4a5568;
            }

            &.avatar-loading {
                opacity: 0.7;
            }

            img {
                object-fit: cover;
                width: 100%;
                height: 100%;
            }
        }
    }

    .upload-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;

        @media (max-width: 599px) {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }
    }

    .file-info {
        max-width: 200px;

        .text-caption:first-child {
            font-weight: 500;
        }
    }
}
</style>
