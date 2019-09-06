<?php

use SertxuDeveloper\Translatable\Translatable;

if (!function_exists('trans_route')) {
  function trans_route($name, $params = [], $absolute = false, $locale = false) {
    return Translatable::route($name, $params, $absolute, $locale);
  }
}