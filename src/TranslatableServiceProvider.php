<?php

namespace SertxuDeveloper\Translatable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use SertxuDeveloper\Translatable\Facades\Translatable as TranslatableFacade;
use SertxuDeveloper\Translatable\Macros\TranslatableRoutesMacro;

class TranslatableServiceProvider extends ServiceProvider {

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    $this->registerPublishableFiles();
    $this->registerMacros();
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    $loader = AliasLoader::getInstance();
    $loader->alias('Translatable', TranslatableFacade::class);

    $this->app->singleton('translatable', function () {
      return new Translatable();
    });

    $this->mergeConfig();
  }

  /**
   * Register macros.
   *
   * @return void
   */
  protected function registerMacros() {
    TranslatableRoutesMacro::register();
  }

  /**
   * Register the publishable files.
   *
   * @return void
   */
  protected function registerPublishableFiles() {
    $packagePath = __DIR__ . '/..';

    $publishable = [
      "translatable-config" => [
        "{$packagePath}/publishable/config/translatable.php" => config_path('translatable.php')
      ],
    ];

    foreach ($publishable as $group => $paths) {
      $this->publishes($paths, $group);
    }
  }

  /**
   * Merge published configuration file with
   * the original package configuration file.
   *
   * @return void
   */
  protected function mergeConfig() {
    $this->mergeConfigFrom(dirname(__DIR__) . '/publishable/config/translatable.php', 'translatable');
  }
}
