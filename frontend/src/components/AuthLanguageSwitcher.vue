<template>
  <q-btn-toggle v-model="currentLanguage" :options="languageOptions" @update:model-value="changeLanguage"
    class="language-toggle-auth" flat dense no-caps rounded color="white" text-color="white"
    toggle-color="rgba(255, 255, 255, 0.2)" />
</template>

<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useQuasar } from 'quasar'
import { LocalStorage } from 'quasar'

// Composables
const { locale, t } = useI18n()
const $q = useQuasar()

// Language options with initials only
const languageOptions = [
  {
    label: 'EN',
    value: 'en-US'
  },
  {
    label: 'ID',
    value: 'id-ID'
  }
]

// Current language state
const currentLanguage = ref(locale.value)

// Watch for locale changes and update current language
watch(() => locale.value, (newLocale) => {
  currentLanguage.value = newLocale
})

// Change language method
const changeLanguage = (newLanguage) => {
  if (newLanguage && newLanguage !== locale.value) {
    // Set the locale
    locale.value = newLanguage

    // Save to localStorage
    LocalStorage.set('language', newLanguage)

    // Set Quasar language
    const quasarLang = newLanguage === 'id-ID' ? 'id' : 'en-US'
    import(/* @vite-ignore */ `quasar/lang/${quasarLang}`).then(lang => {
      $q.lang.set(lang.default)
    })

    // Show notification
    $q.notify({
      type: 'positive',
      message: t('language.languageChanged'),
      icon: 'translate',
      position: 'top-right',
      timeout: 2000
    })
  }
}
</script>

<style lang="scss" scoped>
.language-toggle-auth {
  :deep(.q-btn-toggle) {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
  }

  :deep(.q-btn) {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    font-size: 0.75rem;
    min-width: 32px;
    padding: 6px 10px;
    transition: all 0.2s ease;

    &.q-btn--active {
      background: rgba(255, 255, 255, 0.25) !important;
      color: white !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    &:hover {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      transform: translateY(-1px);
    }
  }
}
</style>
