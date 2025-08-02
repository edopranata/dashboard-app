import { LocalStorage } from 'quasar'

/**
 * Language Detection Service
 * Detects user language based on IP geolocation with fallbacks
 */
class LanguageDetectionService {
  constructor() {
    this.defaultLocale = 'en-US'
    this.supportedLocales = ['en-US', 'id-ID']
    this.cache = new Map()
  }

  /**
   * Get user's preferred language
   * Priority: 1. Saved preference 2. IP-based detection 3. Browser language 4. Default
   */
  async detectLanguage() {
    try {
      // 1. Check saved preference first
      let savedLocale = LocalStorage.getItem('locale')
      if (savedLocale && this.supportedLocales.includes(savedLocale)) {
        console.log('🌐 Using saved locale:', savedLocale)
        return savedLocale
      }

      // 2. Try IP-based detection
      const ipBasedLocale = await this.detectLanguageByIP()
      if (ipBasedLocale) {
        console.log('🌍 Detected locale by IP:', ipBasedLocale)
        LocalStorage.set('locale', ipBasedLocale)
        return ipBasedLocale
      }

      // 3. Try browser language detection
      const browserLocale = this.detectBrowserLanguage()
      if (browserLocale) {
        console.log('🌏 Using browser locale:', browserLocale)
        LocalStorage.set('locale', browserLocale)
        return browserLocale
      }

      // 4. Use default
      console.log('🌐 Using default locale:', this.defaultLocale)
      LocalStorage.set('locale', this.defaultLocale)
      return this.defaultLocale
    } catch (error) {
      console.warn('Language detection failed, using default:', error)
      return this.defaultLocale
    }
  }

  /**
   * Detect language based on user's IP geolocation
   */
  async detectLanguageByIP() {
    try {
      // Check cache first
      const cachedResult = this.cache.get('ip-detection')
      if (cachedResult && Date.now() - cachedResult.timestamp < 24 * 60 * 60 * 1000) {
        return cachedResult.locale
      }

      // Use multiple IP geolocation services with fallbacks
      const services = [
        {
          url: 'https://ipapi.co/json/',
          parseResponse: (data) => data.country_code,
        },
        {
          url: 'https://freegeoip.app/json/',
          parseResponse: (data) => data.country_code,
        },
        {
          url: 'https://ip-api.com/json/',
          parseResponse: (data) => data.countryCode,
        },
      ]

      for (const service of services) {
        try {
          const response = await fetch(service.url, {
            method: 'GET',
            headers: {
              Accept: 'application/json',
            },
            timeout: 3000,
          })

          if (!response.ok) continue

          const data = await response.json()
          const countryCode = service.parseResponse(data)

          if (countryCode) {
            const locale = this.mapCountryToLocale(countryCode)

            // Cache the result
            this.cache.set('ip-detection', {
              locale,
              timestamp: Date.now(),
              countryCode,
            })

            return locale
          }
        } catch (serviceError) {
          console.warn(`IP service ${service.url} failed:`, serviceError)
          continue
        }
      }

      return null
    } catch (error) {
      console.warn('IP-based language detection failed:', error)
      return null
    }
  }

  /**
   * Detect language from browser settings
   */
  detectBrowserLanguage() {
    try {
      const browserLang = navigator.language || navigator.languages?.[0]
      if (!browserLang) return null

      // Try exact match first
      if (this.supportedLocales.includes(browserLang)) {
        return browserLang
      }

      // Try language code match (e.g., 'en' from 'en-GB')
      const langCode = browserLang.split('-')[0]
      const matchingLocale = this.supportedLocales.find((locale) => locale.startsWith(langCode))

      return matchingLocale || null
    } catch (error) {
      console.warn('Browser language detection failed:', error)
      return null
    }
  }

  /**
   * Map country code to supported locale
   */
  mapCountryToLocale(countryCode) {
    const countryToLocaleMap = {
      // Indonesia
      ID: 'id-ID',

      // English-speaking countries
      US: 'en-US',
      GB: 'en-US',
      AU: 'en-US',
      CA: 'en-US',
      NZ: 'en-US',
      IE: 'en-US',
      ZA: 'en-US',
      IN: 'en-US',
      PH: 'en-US',
      SG: 'en-US',
      MY: 'en-US',

      // Default to English for other countries
      // Add more mappings as needed
    }

    return countryToLocaleMap[countryCode?.toUpperCase()] || this.defaultLocale
  }

  /**
   * Force refresh language detection (bypass cache)
   */
  async forceRefreshDetection() {
    this.cache.clear()
    LocalStorage.remove('locale')
    return await this.detectLanguage()
  }

  /**
   * Get cached detection info for debugging
   */
  getDetectionInfo() {
    return {
      cache: Object.fromEntries(this.cache),
      savedLocale: LocalStorage.getItem('locale'),
      browserLanguage: navigator.language,
      supportedLocales: this.supportedLocales,
      defaultLocale: this.defaultLocale,
    }
  }
}

// Export singleton instance
export const languageDetection = new LanguageDetectionService()
export default languageDetection
