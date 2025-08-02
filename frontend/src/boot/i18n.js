import { defineBoot } from '#q-app/wrappers'
import { createI18n } from 'vue-i18n'
import { LocalStorage } from 'quasar'
import messages from 'src/i18n'
import languageDetection from 'src/services/languageDetection'

export default defineBoot(async ({ app }) => {
  // Detect user's preferred language
  const detectedLocale = await languageDetection.detectLanguage()
  
  const defaultLocale = 'en-US'
  const availableLocales = Object.keys(messages)
  
  // Validate detected locale
  const locale = availableLocales.includes(detectedLocale) 
    ? detectedLocale 
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
  
  // Ensure locale is saved to localStorage
  LocalStorage.set('locale', locale)
  
  console.log('🌐 i18n initialized with locale:', locale)
})
