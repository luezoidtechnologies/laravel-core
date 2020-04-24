<?php

namespace Luezoid\Laravelcore;

use Illuminate\Support\ServiceProvider;
use Luezoid\Laravelcore\Console\Commands\FilesInitCommand;
use Luezoid\Laravelcore\Facades\Laravelcore;


class LaravelcoreServiceProvider extends ServiceProvider
{
    protected $commands = [
        FilesInitCommand::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/lang/errors.php', 'errors');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/2019_10_03_101111_create_files_table.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/file.php', 'file'
        );


        $this->publishes([
            __DIR__ . '/config/file.php' => config_path('file.php'),
        ], 'luezoid-file-config');
        $this->app->alias(Laravelcore::class, 'luezoid-core');
        $this->app->alias(\Aws\Laravel\AwsFacade::class, 'AWS');
        $this->app->register(\Aws\Laravel\AwsServiceProvider::class);
        $this->commands($this->commands);
    }
}
