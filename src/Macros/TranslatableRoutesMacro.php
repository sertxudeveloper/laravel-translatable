<?php

namespace SertxuDeveloper\Translatable\Macros;

use Closure;
use Illuminate\Support\Facades\Route;
use SertxuDeveloper\Translatable\Middleware\TranslatableRoutesHandler;

class TranslatableRoutesMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register() {
        if (!Route::hasMacro('localized')) {
            function registerRoutes($locale, Closure $closure) {
                $fallbackLocale = config('translatable.fallback_locale');
                $hideFallbackLocale = config('translatable.hide_fallback_locale');

                $attributes = [];
                $attributes['middleware'] = [TranslatableRoutesHandler::class];

                $attributes['as'] = "$locale.";
                $attributes['prefix'] = $locale;
                Route::group($attributes, $closure);

                if ($hideFallbackLocale && $locale === $fallbackLocale) {
                    $attributes = [];
                    $attributes['middleware'] = [TranslatableRoutesHandler::class];
                    Route::group($attributes, $closure);
                }
            }

            Route::macro('localized', function (Closure $closure) {
                $locales = config('translatable.locales');
                $fallbackLocale = config('translatable.fallback_locale');

                foreach ($locales as $locale) {
                    if ($fallbackLocale === $locale) {
                        continue;
                    }
                    registerRoutes($locale, $closure);
                }

                registerRoutes($fallbackLocale, $closure);
            });
        }
    }
}
