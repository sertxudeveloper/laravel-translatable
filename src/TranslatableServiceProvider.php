<?php

namespace SertxuDeveloper\Translatable;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use SertxuDeveloper\Translatable\Facades\Translatable as TranslatableFacade;
use SertxuDeveloper\Translatable\Macros\TranslatableRoutesMacro;

class TranslatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPublishableFiles();
        $this->registerMacros();
        $this->mergeConfig();
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
            return new Translatable;
        });

        $this->loadHelpers();
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
        $packagePath = dirname(__DIR__);

        $publishable = [
            'trans-config' => [
                "$packagePath/publishable/config/translatable.php" => config_path('translatable.php'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Merge published configuration file with
     * the original package configuration file.
     */
    protected function mergeConfig() {
        $this->mergeConfigFrom(dirname(__DIR__).'/publishable/config/translatable.php', 'translatable');
    }

    /**
     * Get dynamically the Helpers from the /src/Helpers directory and require_once each file.
     */
    protected function loadHelpers() {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }
}
