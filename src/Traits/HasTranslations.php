<?php

namespace SertxuDeveloper\Translatable\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait HasTranslations {

  /**
   * Get the translated $attribute if not exist return the fallback translation
   *
   * @param $attribute
   * @param bool $lang
   * @return string
   */
  public function getTranslated($attribute, $lang = false) {
    if(!$lang) $lang = App::getLocale();
    if (config('translatable.fallback_locale') === $lang) {
      return $this[$attribute];
    } else {
      $translation = DB::table($this->table . config('translatable.table_sufix'))->where([['locale', $lang], [$this->getKeyName(), $this->getKey()]])->first();

      return (!isset($translation->$attribute)) ? $this[$attribute] : $translation->$attribute;
    }
  }

}