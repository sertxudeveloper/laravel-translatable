<?php

namespace SertxuDeveloper\Translatable;
  
use Illuminate\Support\ServiceProvider;
  
class TranslatableServiceProvider extends ServiceProvider {

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    $this->registerPublishableFiles();
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    $this->mergeConfig();
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
