<?php

namespace Malico\MeSomb;

use Illuminate\Support\ServiceProvider;

class MeSombServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap app.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    /**
     * Publish assets and config.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes(
            [
                __DIR__.'/../config/mesomb.php' => config_path('mesomb.php'),
            ],
            'mesomb-configuration'
        );
    }

    /**
     * Register Package Configuration.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/mesomb.php',
            'mesomb'
        );
    }
}
