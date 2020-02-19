<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MobiMarket\DpdShipping\Entities\ApiAuth;

class DpdServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/dpd.php' => config_path('dpd.php'),
        ], 'dpd');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dpd.php', 'dpd');

        $this->app->singleton(DpdRestApi::class, function (Application $app) {
            $config = $app->make('config');

            return new DpdRestApi(
                $config->get('dpd.api.url'),
                $config->get('dpd.api.timeout'),
                $config->get('dpd.api.should_log'),
                $config->get('dpd.api.token_cache_ttl'),
                ApiAuth::fromArray($config->get('dpd.auth'))
            );
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [DpdRestApi::class];
    }
}
