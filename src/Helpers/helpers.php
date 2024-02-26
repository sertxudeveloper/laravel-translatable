<?php

use SertxuDeveloper\Translatable\Facades\Translatable;

if (!function_exists('trans_route')) {
    function trans_route($name, $params = [], $absolute = false, $locale = null) {
        return Translatable::route($name, $params, $absolute, $locale);
    }
}

if (!function_exists('switch_to_locale')) {
    function switch_to_locale($locale) {
        return Translatable::switchToLocale($locale);
    }
}

if (!function_exists('is_route')) {
    function is_route($patterns): bool {
        $patterns = Arr::wrap($patterns);

        /** Check route without locales */
        if (request()->routeIs($patterns)) {
            return true;
        }

        /** Check route with locales */
        foreach (config('translatable.locales') as $locale) {
            foreach ($patterns as $pattern) {
                if (request()->routeIs("$locale.$pattern")) {
                    return true;
                }
            }
        }

        return false;
    }
}
