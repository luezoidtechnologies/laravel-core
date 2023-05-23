<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    public function defineEnvironment($app)
    {
        tap($app->make('config'), function (\Illuminate\Contracts\Config\Repository $config) {
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
        \Illuminate\Support\Facades\Route::resource('api/minions', 'MinionController', ['parameters' => ['minions' => 'id']]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }


}
