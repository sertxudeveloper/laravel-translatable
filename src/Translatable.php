<?php

namespace SertxuDeveloper\Translatable;

class Translatable {
  
  static public function getLocalizedURL($path = false, $lang = false) {
    if (!$path) $path = Request::path();
    if (!$lang) $lang = App::getLocale();
    
    dd($path, $lang);
    
  }
}
