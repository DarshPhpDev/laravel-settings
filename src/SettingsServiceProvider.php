<?php

namespace DarshPhpDev\LaravelSettings;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use DarshPhpDev\LaravelSettings\Storage\DatabaseStorage;
use DarshPhpDev\LaravelSettings\Storage\FileStorage;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for Laravel Settings package.
 * Handles package registration, bootstrapping, and configuration publishing.
 */
class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register package services and bindings.
     * Sets up storage interface implementation and settings singleton.
     *
     * @return void
     */
    public function register()
    {
        // Bind the appropriate storage implementation based on configuration
        $this->app->singleton(StorageInterface::class, function ($app) {
            $driver = config('settings.driver', 'file');

            switch ($driver) {
                case 'database':
                    return $app->make(DatabaseStorage::class);
                default:
                    return $app->make(FileStorage::class);
            }
        });

        // Register the main Settings service
        $this->app->singleton(Settings::class, function ($app) {
            return new Settings($app->make(StorageInterface::class));
        });

        // Register package commands
        $this->commands([
            Commands\InstallSettingsCommand::class,
        ]);
    }

    /**
     * Bootstrap package services.
     * Publishes configuration files and migrations.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/settings.php' => config_path('settings.php'),
        ], 'settings-config');

        // Publish migration files
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }
}