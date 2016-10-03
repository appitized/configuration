<?php

namespace Appitized\Configuration;

use Illuminate\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->publishes([
          __DIR__ . '/config/settings.php' => config_path('configuration/settings.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__ . '/config/settings.php',
          'configuration.settings');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('configuration', function () {
            return new Configuration;
        });
    }
}
