<?php

namespace BastModule\Providers;

use Illuminate\Support\ServiceProvider;

class BastModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge module config with application config
        $this->mergeConfigFrom(__DIR__ . '/../config/bast.php', 'bast');

        // Bind the App service for BAST if needed (we use the app service implementation)
        $this->app->singleton(\BastModule\Services\BastReportService::class, function ($app) {
            return new \BastModule\Services\BastReportService();
        });
    }

    public function boot()
    {
        // Only boot module if enabled in config
        if (!config('bast.enabled', true)) {
            return;
        }

        // Load migrations from module
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Load routes if configured
        if (config('bast.routes.load_routes', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../Http/Routes/routes.php');
        }

        // Load views under bast:: namespace
        $this->loadViewsFrom(__DIR__ . '/../Views', 'bast');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/bast.php' => config_path('bast.php'),
        ], 'bast-config');

        // Publish views optionally
        $this->publishes([
            __DIR__ . '/../Views' => resource_path('views/vendor/bast'),
        ], 'bast-views');
    }
}
