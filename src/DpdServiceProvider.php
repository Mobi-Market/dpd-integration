<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MobiMarket\DpdShipping\Entities\ApiAuth;

class DpdServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @deprecated
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/dpd-local.php' => config_path('dpd-local.php'),
            __DIR__ . '/../config/dpd.php'       => config_path('dpd.php'),
        ], 'dpd');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dpd-local.php', 'dpd-local');
        $this->mergeConfigFrom(__DIR__ . '/../config/dpd.php', 'dpd');

        $this->bindRestApi(DpdGlobalApi::class, 'dpd');
        $this->bindRestApi(DpdLocalApi::class, 'dpd-local');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [DpdGlobalApi::class, DpdLocalApi::class];
    }

    /**
     * Set up binding with prefixed config variables.
     */
    protected function bindRestApi(string $class, string $prefix): void
    {
        $this->app->singleton($class, function (Application $app) use ($prefix, $class) {
            $config = $app->make('config');

            return new $class(
                $config->get($prefix . '.api.url'),
                $config->get($prefix . '.api.timeout'),
                $config->get($prefix . '.api.should_log'),
                $config->get($prefix . '.api.token_cache_ttl'),
                ApiAuth::fromArray($config->get($prefix . '.auth'))
            );
        });
    }
}
