<?php

namespace SertxuDeveloper\Translatable;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

use function count;

class Translatable
{
    /**
     * Check if the provided locale is within the supported locales.
     *
     * @param  string  $locale The locale to check.
     */
    public function checkLocaleInSupportedLocales(string $locale): bool {
        $locales = config('translatable.locales');

        return in_array($locale, $locales);
    }

    /**
     * Get the locale from the URL.
     */
    public function getLocaleFromRequest(): string {
        $params = explode('/', request()->getPathInfo());

        // Dump the first element (empty string) as getPathInfo() always returns a leading slash
        array_shift($params);

        if (count($params) > 0) {
            $locale = $params[0];

            if ($this->checkLocaleInSupportedLocales($locale)) {
                return $locale;
            }
        }

        return app()->getLocale() ?? config('translatable.fallback_locale');
    }

    /**
     * Check if the provided locale is the fallback locale.
     *
     * @param  string  $locale The locale to check.
     */
    public function isFallbackLocale(string $locale): bool {
        return config('translatable.fallback_locale') === $locale;
    }

    /**
     * Get if the fallback locale should be hidden from the URL.
     */
    public function isFallbackLocaleHidden(): bool {
        return config('translatable.hide_fallback_locale');
    }

    /**
     * Given a route name or path, return it localized if possible.
     *
     * @param  string  $name The route name or path to localize.
     * @param  mixed  $parameters The route parameters.
     * @param  bool  $absolute Whether to return an absolute URL.
     * @param  string|null  $locale The locale to use.
     * @return string The localized route.
     */
    public function route(string $name, $parameters = [], bool $absolute = false, string $locale = null): string {
        $name = $this->stripLocaleFromRouteName($name);

        $currentLocale = $this->getLocaleFromRequest();
        $fallbackLocale = config('translatable.fallback_locale');
        $locale = $locale ?: $currentLocale;

        /** The provided $name is a route name */
        if (Route::has($name)) {
            if (!$this->isFallbackLocaleHidden() || $locale !== $fallbackLocale) {
                $name = "$locale.$name";
            }

            return URL::route($name, $parameters, $absolute);
        }

        /** The provided $name is a route path */
        $params = explode('/', $name);
        if ($params[0] == null) {
            array_shift($params);
        }

        if (count($params) > 0) {
            if ($this->checkLocaleInSupportedLocales($params[0])) {
                array_shift($params);
            }

            $params = implode('/', $params);

            if (!$this->isFallbackLocaleHidden() || !$this->isFallbackLocale($locale)) {
                $params = "$locale/$params";
            }

            return URL::to($params);
        }

        /** Fallback */
        return URL::route($name, $parameters, $absolute);
    }

    /**
     * Get the current path localized with the provided locale.
     *
     * @param  string  $locale The locale to use.
     * @return string The localized path.
     */
    public function switchToLocale(string $locale): string {
        $root = request()->root();
        $params = explode('/', request()->getPathInfo());

        // Dump the first element (empty string) as getPathInfo() always returns a leading slash
        array_shift($params);

        if (count($params) === 0) {
            return "$root";
        }

        if (!$this->checkLocaleInSupportedLocales($locale)) {
            return "$root";
        }

        if ($this->checkLocaleInSupportedLocales($params[0])) {
            array_shift($params);
        }

        $params = implode('/', $params);

        $url = ($this->isFallbackLocaleHidden() && $this->isFallbackLocale($locale))
          ? $params : "$locale/$params";

        return "$root/$url";
    }

    /**
     * Strip the locale from the beginning of a route name.
     *
     * @param  string  $name The route name to strip.
     * @return string The stripped route name.
     */
    protected function stripLocaleFromRouteName(string $name): string {
        $parts = explode('.', $name);

        /** If there is no dot in the route name, couldn't be a locale in the name. */
        if (count($parts) === 1) {
            return $name;
        }

        /** Get the locales from the configuration file */
        $locales = config('translatable.locales', []);

        /** If the first part of the route name is a valid locale, then remove it from the array. */
        if (in_array($parts[0], $locales)) {
            array_shift($parts);
        }

        /** Rebuild the normalized route name. */
        return implode('.', $parts);
    }
}
