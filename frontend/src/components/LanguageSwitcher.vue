<template>
    <q-btn-dropdown :label="currentLanguageLabel" :icon="currentLanguageIcon" class="language-switcher" flat dense
        no-caps dropdown-icon="keyboard_arrow_down">
        <q-list dense>
            <q-item v-for="language in availableLanguages" :key="language.value" clickable v-close-popup
                @click="changeLanguage(language.value)"
                :class="{ 'bg-primary text-white': currentLocale === language.value }">
                <q-item-section avatar>
                    <q-icon :name="language.icon" size="20px" />
                </q-item-section>
                <q-item-section>
                    <q-item-label>{{ language.label }}</q-item-label>
                    <q-item-label caption>{{ language.nativeLabel }}</q-item-label>
                </q-item-section>
                <q-item-section side v-if="currentLocale === language.value">
                    <q-icon name="check" color="positive" />
                </q-item-section>
            </q-item>
        </q-list>
    </q-btn-dropdown>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useQuasar } from 'quasar'
import { LocalStorage } from 'quasar'

// Composables
const { locale, t } = useI18n()
const $q = useQuasar()

// Available languages configuration
const availableLanguages = [
    {
        value: 'en-US',
        label: 'English',
        nativeLabel: 'English',
        icon: 'language'
    },
    {
        value: 'id-ID',
        label: 'Indonesian',
        nativeLabel: 'Bahasa Indonesia',
        icon: 'translate'
    }
]

// Computed properties
const currentLocale = computed(() => locale.value)

const currentLanguage = computed(() => {
    return availableLanguages.find(lang => lang.value === currentLocale.value) || availableLanguages[0]
})

const currentLanguageLabel = computed(() => {
    return currentLanguage.value.label
})

const currentLanguageIcon = computed(() => {
    return currentLanguage.value.icon
})

// Methods
const changeLanguage = (newLocale) => {
    if (newLocale === currentLocale.value) return

    // Update locale
    locale.value = newLocale

    // Save to localStorage
    LocalStorage.set('locale', newLocale)

    // Update Quasar language pack if needed
    // You can add Quasar language pack switching here if needed

    // Show success notification
    $q.notify({
        type: 'positive',
        message: t('language.languageChanged'),
        icon: 'check_circle',
        position: 'top-right',
        timeout: 2000
    })
}

// Initialize locale from localStorage on component mount
const initializeLocale = () => {
    const savedLocale = LocalStorage.getItem('locale')
    if (savedLocale && availableLanguages.some(lang => lang.value === savedLocale)) {
        locale.value = savedLocale
    }
}

// Initialize on mount
initializeLocale()
</script>

<style lang="scss" scoped>
.language-switcher {
    min-width: 140px;

    :deep(.q-btn__content) {
        justify-content: space-between;
    }
}

.q-item {
    min-height: 56px;

    &.bg-primary {
        border-radius: 8px;
        margin: 2px 4px;
    }
}
</style>
