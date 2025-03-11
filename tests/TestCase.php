<?php

namespace SmartCms\Sale\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use SmartCms\Sale\SaleServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/smart-cms/store/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/smart-cms/core/database/migrations');

        $this->artisan('migrate')->run();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'SmartCms\\Sale\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SaleServiceProvider::class,
            \SmartCms\Store\StoreServiceProvider::class,
            \SmartCms\Core\SmartCmsPanelManager::class,
            \SmartCms\Core\SmartCmsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        $app->singleton('_settings', function () {
            return new \SmartCms\Core\Services\Singletone\Settings;
        });
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
