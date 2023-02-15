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
    function is_route(string $name): bool {
        /** Check route without locales */
        if (request()->routeIs($name)) {
            return true;
        }

        /** Check route with locales */
        foreach (config('translatable.locales') as $locale) {
            if (request()->routeIs("$locale.$name")) {
                return true;
            }
        }

        return false;
    }
}
