<?php

namespace UoGSoE\ApiTokenMiddleware;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use UoGSoE\ApiTokenMiddleware\Commands\ListTokens;
use UoGSoE\ApiTokenMiddleware\Commands\CreateToken;
use UoGSoE\ApiTokenMiddleware\Commands\DeleteToken;
use UoGSoE\ApiTokenMiddleware\Commands\RegenerateToken;

/**
 * Service provider for the ApiTokenMiddleware package.
 */
class ApiTokenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param Router $router The Laravel router instance for registering middleware.
     * @return void
     */
    public function boot(Router $router): void
    {
        // Publish the ApiToken model to the app/Models directory to align with Laravel 12's default model namespace
        $this->publishes([
            __DIR__ . '/ApiToken.php' => app_path('Models/ApiToken.php'),
        ]);

        // Publish the migration file to create the api_tokens table
        $this->publishes([
            __DIR__ . '/../migrations/2018_04_18_090739_create_api_tokens_table.php' =>
                database_path('migrations/2018_04_18_090739_create_api_tokens_table.php'),
        ]);

        // Register the 'apitoken' middleware alias for use in routes
        $router->aliasMiddleware('apitoken', BasicApiTokenMiddleware::class);

        // Register console commands, but only if running in the console environment
        if ($this->app->runningInConsole()) {
            $this->commands([
                ListTokens::class,
                CreateToken::class,
                DeleteToken::class,
                RegenerateToken::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Empty for now, but can be used to bind services or configurations in the future
    }
}
