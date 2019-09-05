<?php

namespace SertxuDeveloper\Translatable\Macros;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use SertxuDeveloper\Translatable\Facades\Translatable;
use SertxuDeveloper\Translatable\Middleware\TranslatableRoutesHandler;
use \Closure;

class TranslatableRoutesMacro {

  /**
   * Register the macro.
   *
   * @return void
   */
  public static function register() {

    Route::macro('localized', function (Closure $closure) {
      $currentLocale = Translatable::getLocaleFromRequest();
      $locales = config('translatable.locales');
      $fallbackLocale = config('translatable.fallback_locale');
      $hideFallbackLocale = config('translatable.hide_fallback_locale');

      $attributes["middleware"] = [TranslatableRoutesHandler::class];

      if ($hideFallbackLocale && $currentLocale === $fallbackLocale) {
        Route::group($attributes, $closure);
      }

      $attributes["prefix"] = $currentLocale;
      Route::group($attributes, $closure);
    });
  }
}
