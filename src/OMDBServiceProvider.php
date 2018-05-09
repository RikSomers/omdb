<?php

namespace RikSomers\OMDB;

use Illuminate\Support\ServiceProvider;

class OMDBServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/omdb.php' => config_path('omdb.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/omdb.php', 'omdb');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('omdb', function() {
            return new OMDBApi(new OMDBClient(app('config')));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['omdb'];
    }
}
