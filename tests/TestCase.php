<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Base test case for package testing, setting up migrations and service providers.
 */
class TestCase extends OrchestraTestCase
{
    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Load migrations from the package's migrations directory
        $migrationPath = realpath(__DIR__ . '/../migrations');
        if ($migrationPath === false) {
            $this->fail('Migration directory not found at ' . __DIR__ . '/../migrations');
        }
        $this->loadMigrationsFrom($migrationPath);

        // Run migrations for the testing database
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    /**
     * Define package service providers for testing.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            \UoGSoE\ApiTokenMiddleware\ApiTokenServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Configure an in-memory SQLite database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}