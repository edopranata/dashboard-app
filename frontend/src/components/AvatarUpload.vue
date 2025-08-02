<template>
  <div class="avatar-upload">
    <!-- Avatar Display -->
    <div class="avatar-display q-mb-md">
      <q-avatar 
        :size="displaySize" 
        class="avatar-preview"
        :class="{ 'avatar-loading': uploading }"
      >
        <img 
          v-if="currentAvatarUrl" 
          :src="currentAvatarUrl" 
          :alt="$t('avatar.user_avatar')"
          @error="handleImageError"
        />
        <q-icon 
          v-else 
          name="person" 
          :size="iconSize"
          class="text-grey-6"
        />
        
        <!-- Loading overlay -->
        <div 
          v-if="uploading" 
          class="absolute-full flex flex-center bg-black-4"
        >
          <q-spinner color="white" size="24px" />
        </div>
      </q-avatar>
    </div>

    <!-- Upload Area -->
    <div 
      class="upload-area"
      :class="{ 
        'upload-dragover': isDragOver,
        'upload-disabled': uploading 
      }"
      @click="triggerFileInput"
      @drop.prevent="handleDrop"
      @dragover.prevent="isDragOver = true"
      @dragleave.prevent="isDragOver = false"
    >
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        style="display: none"
        @change="handleFileSelect"
      />
      
      <div class="upload-content text-center">
        <q-icon 
          name="cloud_upload" 
          size="48px" 
          class="text-primary q-mb-md"
        />
        <div class="text-h6 q-mb-sm">
          {{ $t('avatar.drag_drop_or_click') }}
        </div>
        <div class="text-body2 text-grey-6">
          {{ $t('avatar.supported_formats') }}
        </div>
        <div class="text-caption text-grey-5 q-mt-xs">
          {{ $t('avatar.max_file_size') }}
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="avatar-actions q-mt-md" v-if="currentAvatarUrl && !isDefault">
      <q-btn
        color="negative"
        outline
        :loading="deleting"
        @click="confirmDelete"
        :label="$t('avatar.delete_avatar')"
        icon="delete"
        size="sm"
      />
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
          <q-btn 
            flat 
            :label="$t('common.cancel')" 
            color="grey-7" 
            v-close-popup 
          />
          <q-btn 
            flat 
            :label="$t('common.delete')" 
            color="negative" 
            @click="deleteAvatar"
            :loading="deleting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import avatarService from 'src/services/avatarService'

export default {
  name: 'AvatarUpload',
  props: {
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
  },
  emits: ['avatar-updated', 'avatar-deleted', 'upload-progress'],
  setup(props, { emit }) {
    const $q = useQuasar()
    const { t } = useI18n()

    // Reactive data
    const fileInput = ref(null)
    const currentAvatarUrl = ref(null)
    const isDefault = ref(true)
    const uploading = ref(false)
    const deleting = ref(false)
    const isDragOver = ref(false)
    const showDeleteDialog = ref(false)

    // Computed
    const iconSize = computed(() => {
      const size = parseInt(props.displaySize)
      return Math.floor(size * 0.4) + 'px'
    })

    // Methods
    const loadCurrentAvatar = async () => {
      try {
        const response = await avatarService.getAvatarUrl(props.size)
        if (response.data.status === 'success') {
          currentAvatarUrl.value = response.data.data.avatar_url
          isDefault.value = response.data.data.is_default || false
        }
      } catch (error) {
        console.error('Failed to load avatar:', error)
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

      // Check file size (2MB max)
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

    const uploadFile = async (file) => {
      if (!validateFile(file)) return

      uploading.value = true
      emit('upload-progress', { uploading: true, progress: 0 })

      try {
        const response = await avatarService.uploadAvatar(file)
        
        if (response.data.status === 'success') {
          currentAvatarUrl.value = response.data.data.avatar_url
          isDefault.value = false
          
          $q.notify({
            type: 'positive',
            message: t('avatar.upload_success'),
            position: 'top'
          })

          emit('avatar-updated', response.data.data)
        }
      } catch (error) {
        console.error('Upload failed:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || t('avatar.upload_failed'),
          position: 'top'
        })
      } finally {
        uploading.value = false
        emit('upload-progress', { uploading: false, progress: 100 })
      }
    }

    const triggerFileInput = () => {
      if (!props.disabled && !uploading.value) {
        fileInput.value?.click()
      }
    }

    const handleFileSelect = (event) => {
      const file = event.target.files?.[0]
      if (file) {
        uploadFile(file)
      }
      // Reset input value to allow selecting same file again
      event.target.value = ''
    }

    const handleDrop = (event) => {
      isDragOver.value = false
      
      if (props.disabled || uploading.value) return

      const files = event.dataTransfer.files
      if (files.length > 0) {
        uploadFile(files[0])
      }
    }

    const confirmDelete = () => {
      showDeleteDialog.value = true
    }

    const deleteAvatar = async () => {
      deleting.value = true

      try {
        const response = await avatarService.deleteAvatar()
        
        if (response.data.status === 'success') {
          // Reload default avatar
          await loadCurrentAvatar()
          
          $q.notify({
            type: 'positive',
            message: t('avatar.delete_success'),
            position: 'top'
          })

          emit('avatar-deleted')
        }
      } catch (error) {
        console.error('Delete failed:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || t('avatar.delete_failed'),
          position: 'top'
        })
      } finally {
        deleting.value = false
        showDeleteDialog.value = false
      }
    }

    const handleImageError = () => {
      // Fallback to loading default avatar if image fails to load
      loadCurrentAvatar()
    }

    // Lifecycle
    onMounted(() => {
      loadCurrentAvatar()
    })

    // Watch for size changes
    watch(() => props.size, () => {
      loadCurrentAvatar()
    })

    return {
      // Refs
      fileInput,
      currentAvatarUrl,
      isDefault,
      uploading,
      deleting,
      isDragOver,
      showDeleteDialog,
      
      // Computed
      iconSize,
      
      // Methods
      triggerFileInput,
      handleFileSelect,
      handleDrop,
      confirmDelete,
      deleteAvatar,
      handleImageError
    }
  }
}
</script>

<style lang="scss" scoped>
.avatar-upload {
  .avatar-display {
    display: flex;
    justify-content: center;
    
    .avatar-preview {
      border: 3px solid $grey-3;
      transition: all 0.3s ease;
      
      &.avatar-loading {
        opacity: 0.7;
      }
    }
  }

  .upload-area {
    border: 2px dashed $grey-4;
    border-radius: 12px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: $grey-1;

    &:hover:not(.upload-disabled) {
      border-color: $primary;
      background: lighten($primary, 45%);
    }

    &.upload-dragover {
      border-color: $primary;
      background: lighten($primary, 40%);
      transform: scale(1.02);
    }

    &.upload-disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .upload-content {
      .q-icon {
        transition: transform 0.3s ease;
      }
    }

    &:hover:not(.upload-disabled) .upload-content .q-icon {
      transform: scale(1.1);
    }
  }

  .avatar-actions {
    display: flex;
    justify-content: center;
  }
}

// Dark mode styles
.body--dark {
  .avatar-upload {
    .upload-area {
      background: $grey-9;
      border-color: $grey-7;

      &:hover:not(.upload-disabled) {
        border-color: $primary;
        background: darken($primary, 35%);
      }

      &.upload-dragover {
        background: darken($primary, 30%);
      }
    }
  }
}
</style>
