<?php

namespace Ragnarok\Strex;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ragnarok\Strex\Sinks\SinkStrex;
use Ragnarok\Sink\Facades\SinkRegistrar;

class RagnarokStrexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        SinkRegistrar::register(SinkStrex::class);
        // $this->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_strex.php', 'ragnarok_strex');
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return array
    */
    private function routeConfiguration(): array
    {
        return [
            'namespace'  => "Ragnarok\Strex\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ragnarok_strex.php' => config_path('ragnarok_strex.php'),
            ], ['config', 'ragnarok_strex', 'ragnarok_strex.config']);
        }
    }

}
