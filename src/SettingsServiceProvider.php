<?php

namespace Scriptoshi\Settings;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings and singletons.
     */
    public function register()
    {
        // Merge package configuration with app config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/settings.php',
            'settings'
        );
        // register the facade
        $this->app->singleton('settings', function () {
            return new \Scriptoshi\Settings\Settings();
        });
    }

    /**
     * Bootstrap services and publish resources.
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('settings.php'),
        ], 'config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
