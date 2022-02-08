<?php

namespace SertxuDeveloper\Translatable\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait HasTranslations {

  /**
   * Get the translated $attribute if not exist return the fallback translation
   *
   * @param string $attribute
   * @param string|null $lang
   * @return string
   */
  public function getTranslated(string $attribute, ?string $lang = null): string {
    if (!$lang) $lang = App::getLocale();

    if (config('translatable.fallback_locale') === $lang)
      return $this[$attribute];

    $translation = DB::table($this->table . config('translatable.table_suffix'))
      ->where([
        ['locale', $lang], [
          $this->getKeyName(), $this->getKey(),
        ],
      ])->first();

    return (!isset($translation->$attribute)) ? $this[$attribute] : $translation->$attribute;
  }
}
