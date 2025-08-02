<template>
  <q-avatar 
    :size="size" 
    :class="avatarClass"
    :style="avatarStyle"
  >
    <img 
      v-if="avatarUrl" 
      :src="avatarUrl" 
      :alt="altText"
      @error="handleImageError"
      @load="handleImageLoad"
    />
    <q-icon 
      v-else-if="!loading" 
      name="person" 
      :size="iconSize"
      class="text-grey-6"
    />
    <q-spinner 
      v-if="loading" 
      color="grey-5" 
      :size="spinnerSize"
    />
  </q-avatar>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import avatarService from 'src/services/avatarService'

export default {
  name: 'AvatarDisplay',
  props: {
    // User props
    user: {
      type: Object,
      default: null
    },
    userName: {
      type: String,
      default: ''
    },
    
    // Avatar props
    size: {
      type: String,
      default: '48px'
    },
    avatarSize: {
      type: String,
      default: 'thumbnail',
      validator: (value) => ['original', 'thumbnail', 'small'].includes(value)
    },
    
    // Style props
    rounded: {
      type: Boolean,
      default: true
    },
    bordered: {
      type: Boolean,
      default: false
    },
    borderColor: {
      type: String,
      default: 'grey-3'
    },
    
    // Behavior props
    clickable: {
      type: Boolean,
      default: false
    },
    tooltip: {
      type: Boolean,
      default: false
    },
    
    // Manual avatar URL (overrides user avatar)
    src: {
      type: String,
      default: null
    }
  },
  emits: ['click', 'avatar-loaded', 'avatar-error'],
  setup(props, { emit }) {
    const { t } = useI18n()

    // Reactive data
    const avatarUrl = ref(null)
    const loading = ref(false)
    const imageError = ref(false)

    // Computed properties
    const displayName = computed(() => {
      return props.user?.name || props.userName || t('common.user')
    })

    const altText = computed(() => {
      return t('avatar.user_avatar_alt', { name: displayName.value })
    })

    const iconSize = computed(() => {
      const size = parseInt(props.size)
      return Math.floor(size * 0.5) + 'px'
    })

    const spinnerSize = computed(() => {
      const size = parseInt(props.size)
      return Math.floor(size * 0.4) + 'px'
    })

    const avatarClass = computed(() => {
      return {
        'cursor-pointer': props.clickable,
        'avatar-bordered': props.bordered,
        [`border-${props.borderColor}`]: props.bordered,
        'avatar-rounded': props.rounded
      }
    })

    const avatarStyle = computed(() => {
      const styles = {}
      
      if (props.bordered) {
        styles.border = `2px solid var(--q-${props.borderColor})`
      }
      
      return styles
    })

    // Methods
    const loadAvatar = async () => {
      // If manual src is provided, use it
      if (props.src) {
        avatarUrl.value = props.src
        return
      }

      // If user has avatar field, construct the URL
      if (props.user?.avatar) {
        // Construct avatar URL based on backend storage
        const baseUrl = process.env.VUE_APP_API_URL || 'http://localhost:8001'
        const sizePath = props.avatarSize === 'original' ? '' : `${props.avatarSize}/`
        avatarUrl.value = `${baseUrl}/storage/avatars/${sizePath}${props.user.avatar}`
        return
      }

      // For current user, fetch from API
      if (!props.user && !props.userName) {
        try {
          loading.value = true
          const response = await avatarService.getAvatarUrl(props.avatarSize)
          
          if (response.data.status === 'success') {
            avatarUrl.value = response.data.data.avatar_url
          }
        } catch (error) {
          console.error('Failed to load avatar:', error)
          imageError.value = true
        } finally {
          loading.value = false
        }
        return
      }

      // Generate default avatar for other users
      if (displayName.value) {
        generateDefaultAvatar()
      }
    }

    const generateDefaultAvatar = () => {
      // Generate default avatar URL using ui-avatars.com
      const name = encodeURIComponent(displayName.value)
      const size = parseInt(props.size) * 2 // Higher resolution for retina
      avatarUrl.value = `https://ui-avatars.com/api/?name=${name}&size=${size}&background=random&color=fff&format=png`
    }

    const handleImageError = () => {
      imageError.value = true
      
      // Generate fallback default avatar
      if (displayName.value) {
        generateDefaultAvatar()
      } else {
        avatarUrl.value = null
      }
      
      emit('avatar-error', {
        user: props.user,
        userName: props.userName
      })
    }

    const handleImageLoad = () => {
      imageError.value = false
      emit('avatar-loaded', {
        user: props.user,
        userName: props.userName,
        url: avatarUrl.value
      })
    }

    const handleClick = () => {
      if (props.clickable) {
        emit('click', {
          user: props.user,
          userName: props.userName,
          avatarUrl: avatarUrl.value
        })
      }
    }

    // Lifecycle
    onMounted(() => {
      loadAvatar()
    })

    // Watchers
    watch(() => props.user, () => {
      loadAvatar()
    }, { deep: true })

    watch(() => props.userName, () => {
      loadAvatar()
    })

    watch(() => props.src, () => {
      loadAvatar()
    })

    watch(() => props.avatarSize, () => {
      loadAvatar()
    })

    return {
      // Refs
      avatarUrl,
      loading,
      imageError,
      
      // Computed
      displayName,
      altText,
      iconSize,
      spinnerSize,
      avatarClass,
      avatarStyle,
      
      // Methods
      handleImageError,
      handleImageLoad,
      handleClick
    }
  }
}
</script>

<style lang="scss" scoped>
.q-avatar {
  transition: all 0.3s ease;
  
  &.cursor-pointer:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  &.avatar-bordered {
    border-width: 2px;
    border-style: solid;
  }
  
  &.avatar-rounded {
    border-radius: 50%;
  }
  
  img {
    object-fit: cover;
    width: 100%;
    height: 100%;
  }
}

// Dark mode adjustments
.body--dark {
  .q-avatar {
    &.cursor-pointer:hover {
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
    }
  }
}
</style>
