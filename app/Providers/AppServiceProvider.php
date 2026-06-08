<?php

namespace App\Providers;

use App\Models\Accessory;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\License;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Setting;
use App\Models\SnipeSCIMConfig;
use App\Models\User;
use App\Observers\AccessoryObserver;
use App\Observers\AssetModelObserver;
use App\Observers\AssetObserver;
use App\Observers\ComponentObserver;
use App\Observers\ConsumableObserver;
use App\Observers\LicenseObserver;
use App\Observers\LocationObserver;
use App\Observers\MaintenanceObserver;
use App\Observers\SettingObserver;
use App\Observers\UserObserver;
use App\View\Composers\SidebarComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Rollbar\Laravel\RollbarServiceProvider;

/**
 * This service provider handles setting the observers on models
 *
 * PHP version 5.5.9
 *
 * @version    v3.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     *
     * @since [v3.0]
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        /**
         * This is a workaround for proxies/reverse proxies that don't always pass the proper headers.
         *
         * Here, we check if the APP_URL starts with https://, which we should always honor,
         * regardless of how well the proxy or network is configured.
         *
         * We'll force the https scheme if the APP_URL starts with https://, or if APP_FORCE_TLS is set to true.
         */
        if ((strpos(env('APP_URL'), 'https://') === 0) || (env('APP_FORCE_TLS'))) {
            $url->forceScheme('https');
        }

        // TODO - isn't it somehow 'gauche' to check the environment directly; shouldn't we be using config() somehow?
        if (! env('APP_ALLOW_INSECURE_HOSTS')) {  // unless you set APP_ALLOW_INSECURE_HOSTS, you should PROHIBIT forging domain parts of URL via Host: headers
            $url_parts = parse_url(config('app.url'));
            if ($url_parts && array_key_exists('scheme', $url_parts) && array_key_exists('host', $url_parts)) { // check for the *required* parts of a bare-minimum URL
                URL::forceRootUrl(config('app.url'));
            } else {
                Log::error('Your APP_URL in your .env is misconfigured - it is: '.config('app.url').'. Many things will work strangely unless you fix it.');
            }
        }

        Paginator::useBootstrap();

        View::composer('layouts.default', SidebarComposer::class);

        Schema::defaultStringLength(191);
        Accessory::observe(AccessoryObserver::class);
        Asset::observe(AssetObserver::class);
        AssetModel::observe(AssetModelObserver::class);
        Component::observe(ComponentObserver::class);
        Consumable::observe(ConsumableObserver::class);
        License::observe(LicenseObserver::class);
        Location::observe(LocationObserver::class);
        Maintenance::observe(MaintenanceObserver::class);
        Setting::observe(SettingObserver::class);
        User::observe(UserObserver::class);

        // Fallback version configuration if config/version.php is missing or empty
        if (!config()->has('version') || empty(config('version.app_version'))) {
            $cacheFile = storage_path('framework/version_cache.json');
            $versionData = null;

            $isLocal = app()->environment('local');
            if (!$isLocal && file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
                $versionData = json_decode(file_get_contents($cacheFile), true);
            }

            if (!$versionData) {
                $appVersion = 'v8.6.1-bast.1.0.1';
                $buildVersion = '23109';
                $hashVersion = 'unknown';
                $branch = 'feature/custom-bast-module';

                if (is_dir(base_path('.git'))) {
                    try {
                        $gitBranch = @shell_exec('git rev-parse --abbrev-ref HEAD 2>&1');
                        if ($gitBranch && !str_contains($gitBranch, 'not recognized') && !str_contains($gitBranch, 'fatal')) {
                            $branch = trim($gitBranch);
                            $gitHash = @shell_exec('git rev-parse --short HEAD 2>&1');
                            if ($gitHash && !str_contains($gitHash, 'fatal')) {
                                $hashVersion = trim($gitHash);
                            }
                            $gitBuild = @shell_exec('git rev-list --count HEAD 2>&1');
                            if ($gitBuild && !str_contains($gitBuild, 'fatal')) {
                                $buildVersion = trim($gitBuild);
                            }
                            // Try to get latest tag matching v*-bast.*
                            $gitTag = @shell_exec('git describe --tags --match "v*-bast.*" --abbrev=0 2>&1');
                            if ($gitTag && !str_contains($gitTag, 'fatal') && !str_contains($gitTag, 'not recognized')) {
                                $latestTag = trim($gitTag);
                                // Check commits since that tag
                                $commitsSince = @shell_exec("git rev-list --count {$latestTag}..HEAD 2>&1");
                                if ($commitsSince && !str_contains($commitsSince, 'fatal')) {
                                    $commitsCount = (int)trim($commitsSince);
                                    if ($commitsCount > 0) {
                                        $appVersion = "{$latestTag}-dev.{$commitsCount}";
                                    } else {
                                        $appVersion = $latestTag;
                                    }
                                } else {
                                    $appVersion = $latestTag;
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        // Fall back to hardcoded defaults
                    }
                }

                $versionData = [
                    'app_version' => $appVersion,
                    'full_app_version' => "{$appVersion} - build {$buildVersion}-{$hashVersion}",
                    'build_version' => $buildVersion,
                    'prerelease_version' => '',
                    'hash_version' => $hashVersion,
                    'full_hash' => "{$appVersion}-{$hashVersion}",
                    'branch' => $branch,
                ];

                @file_put_contents($cacheFile, json_encode($versionData));
            }

            config(['version' => $versionData]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        // Only load rollbar if there is a rollbar key and the app is in production
        if (($this->app->environment('production')) && (config('logging.channels.rollbar.access_token'))) {
            $this->app->register(RollbarServiceProvider::class);
        }

        $this->app->singleton('ArieTimmerman\Laravel\SCIMServer\SCIMConfig', SnipeSCIMConfig::class); // this overrides the default SCIM configuration with our own

    }
}
