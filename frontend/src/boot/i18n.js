import { defineBoot } from '#q-app/wrappers'
import { createI18n } from 'vue-i18n'
import { LocalStorage } from 'quasar'
import messages from 'src/i18n'

export default defineBoot(({ app }) => {
  // Get saved locale from localStorage or use default
  const savedLocale = LocalStorage.getItem('locale')
  const defaultLocale = 'en-US'
  const availableLocales = Object.keys(messages)
  
  // Validate saved locale
  const locale = savedLocale && availableLocales.includes(savedLocale) 
    ? savedLocale 
    : defaultLocale

  const i18n = createI18n({
    locale,
    fallbackLocale: defaultLocale,
    globalInjection: true,
    legacy: false, // Use Composition API mode
    messages
  })

  // Set i18n instance on app
  app.use(i18n)
  
  // Save initial locale to localStorage
  LocalStorage.set('locale', locale)
})
