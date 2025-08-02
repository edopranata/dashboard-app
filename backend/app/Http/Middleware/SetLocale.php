<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from various sources with priority
        $locale = $this->detectLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Add locale to request for use in controllers
        $request->merge(['app_locale' => $locale]);

        return $next($request);
    }

    /**
     * Detect user's preferred locale
     * Priority: 1. Request header 2. Request parameter 3. User setting 4. Default
     */
    private function detectLocale(Request $request): string
    {
        $supportedLocales = ['en', 'id'];
        $defaultLocale = 'en';

        // 1. Check Accept-Language header
        $headerLocale = $request->header('Accept-Language');
        if ($headerLocale) {
            $locale = $this->parseAcceptLanguage($headerLocale, $supportedLocales);
            if ($locale) {
                return $locale;
            }
        }

        // 2. Check X-Locale header (custom header from frontend)
        $customLocale = $request->header('X-Locale');
        if ($customLocale && in_array($customLocale, $supportedLocales)) {
            return $customLocale;
        }

        // 3. Check locale parameter in request
        $paramLocale = $request->get('locale');
        if ($paramLocale && in_array($paramLocale, $supportedLocales)) {
            return $paramLocale;
        }

        // 4. Check authenticated user's locale preference
        if ($request->user() && isset($request->user()->locale)) {
            $userLocale = $request->user()->locale;
            if (in_array($userLocale, $supportedLocales)) {
                return $userLocale;
            }
        }

        // 5. Return default locale
        return $defaultLocale;
    }

    /**
     * Parse Accept-Language header and find best match
     */
    private function parseAcceptLanguage(string $acceptLanguage, array $supportedLocales): ?string
    {
        // Parse Accept-Language header
        $languages = [];
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            $part = trim($part);
            $pieces = explode(';', $part);
            $lang = trim($pieces[0]);
            $quality = 1.0;

            if (count($pieces) > 1) {
                $qPart = trim($pieces[1]);
                if (strpos($qPart, 'q=') === 0) {
                    $quality = floatval(substr($qPart, 2));
                }
            }

            $languages[$lang] = $quality;
        }

        // Sort by quality
        arsort($languages);

        // Find best match
        foreach ($languages as $lang => $quality) {
            // Try exact match first
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }

            // Try language code match (e.g., 'en' from 'en-US')
            $langCode = explode('-', $lang)[0];
            if (in_array($langCode, $supportedLocales)) {
                return $langCode;
            }
        }

        return null;
    }
}
