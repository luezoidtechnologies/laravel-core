<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Luezoid\Http\Controllers\MinionController;
use Luezoid\Laravelcore\Http\Controllers\FileController;

require_once __DIR__.'/../Controllers/MinionController.php';
require_once __DIR__.'/../Repositories/MinionRepository.php';
require_once __DIR__.'/../Requests/MinionCreateRequest.php';

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            'Luezoid\Laravelcore\CoreServiceProvider',
        ];
    }

    public function defineEnvironment($app)
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
        });
    }

    protected function defineRoutes($router)
    {
        Route::resource('api/minions', MinionController::class, ['parameters' => ['minions' => 'id']]);
        Route::post('api/files',FileController::class.'@store');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }


}
