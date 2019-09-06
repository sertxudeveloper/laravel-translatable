<?php

namespace SertxuDeveloper\Translatable;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Translatable {

  public function checkLocaleInSupportedLocales($locale) {
    $locales = config('translatable.locales');
    return in_array($locale, $locales);
  }

  public function isFallbackLocaleHidden() {
    return config('translatable.hide_fallback_locale');
  }

  public function isFallbackLocale($locale) {
    return config('translatable.fallback_locale') === $locale;
  }

  public function switchToLocale($locale) {
    $request = request();
    $params = explode('/', $request->getPathInfo());
    // Dump the first element (empty string) as getPathInfo() always returns a leading slash
    array_shift($params);
    if (\count($params) > 0) {
      if ($this->checkLocaleInSupportedLocales($locale)) {
        if ($this->checkLocaleInSupportedLocales($params[0])) array_shift($params);
        $params = implode('/', $params);
        if ($this->isFallbackLocaleHidden() && $this->isFallbackLocale($locale)) {
          $url = $params;
        } else {
          $url = $locale . '/' . $params;
        }
      }
    } else {
      $url = '';
    }

    Session::reflash();
    $url = $url = $request->root() . "/${url}";
    return $url;
  }

  public function route($name, $parameters = [], $absolute = false, $locale = null) {
    $name = $this->stripLocaleFromRouteName($name);

    $currentLocale = $this->getLocaleFromRequest();
    $fallbackLocale = config('translatable.fallback_locale');
    $locale = $locale ?: $currentLocale;

    if (Route::has($name)) {
      if (!$this->isFallbackLocaleHidden() || $locale !== $fallbackLocale) {
        $name = "${locale}.${name}";
      }
      $url = URL::route($name, $parameters, $absolute);
    } else {
      $params = explode('/', $name);
      if ($params[0] == null) array_shift($params);
      if (\count($params) > 0) {
        if ($this->checkLocaleInSupportedLocales($params[0])) array_shift($params);
        $params = implode('/', $params);
        if ($this->isFallbackLocaleHidden() && $this->isFallbackLocale($locale)) {
          $url = $params;
        } else {
          $url = "/$locale/$params";
        }
      }
    }
    
    return $url;
  }

  public function getLocaleFromRequest() {
    $params = explode('/', request()->getPathInfo());
    // Dump the first element (empty string) as getPathInfo() always returns a leading slash
    array_shift($params);
    if (\count($params) > 0) {
      $locale = $params[0];
      if ($this->checkLocaleInSupportedLocales($locale)) return $locale;
    }
    return config('translatable.fallback_locale');
  }

  /**
   * Strip the locale from the beginning of a route name.
   *
   * @param string $name
   *
   * @return string
   */
  protected function stripLocaleFromRouteName($name) {
    $parts = explode('.', $name);

    // If there is no dot in the route name, couldn't be a locale in the name.
    if (count($parts) === 1) return $name;

    // Get the locales from the configuration file
    $locales = config('translatable.locales', []);

    // If the first part of the route name is a valid locale, then remove it from the array.
    if (in_array($parts[0], $locales)) array_shift($parts);

    // Rebuild the normalized route name.
    return join('.', $parts);
  }
}
