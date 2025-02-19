<?php

namespace DarshPhpDev\LaravelSettings;

use DarshPhpDev\LaravelSettings\Storage\Contracts\StorageInterface;
use DarshPhpDev\LaravelSettings\Storage\DatabaseStorage;
use DarshPhpDev\LaravelSettings\Storage\FileStorage;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(StorageInterface::class, function ($app) {
            $driver = config('settings.driver', 'file');

            switch ($driver) {
                case 'database':
                    return $app->make(DatabaseStorage::class);
                default:
                    return $app->make(FileStorage::class);
            }
        });

        $this->app->singleton(Settings::class, function ($app) {
            return new Settings($app->make(StorageInterface::class));
        });

        $this->commands([
            Commands\InstallSettingsCommand::class,
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/settings.php' => config_path('settings.php'),
        ], 'settings-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }
}