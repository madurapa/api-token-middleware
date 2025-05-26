<?php

namespace UoGSoE\ApiTokenMiddleware\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

/**
 * Console command to create a new API token for a specified service.
 */
class CreateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apitoken:create {service : The service name for the API token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API token for a specified service';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int Exit code (0 for success, 1 for failure).
     */
    public function handle(): int
    {
        // Get the service name from the command argument
        $service = $this->argument('service');

        // Check if a token already exists for the service
        if (ApiToken::where('service', $service)->exists()) {
            $this->error("A token for service '$service' already exists.");
            return 1;
        }

        try {
            // Create a new token using the ApiToken model's createNew method
            $token = ApiToken::createNew($service);

            // Display the created token in a table
            $this->info('Token created successfully:');
            $this->table(
                ['Service', 'Token'],
                [[$service, $token]]
            );

            // Provide additional usage instructions
            $this->comment('Use this token in API requests via Authorization: Bearer <token>.');

            return 0; // Success
        } catch (\InvalidArgumentException $e) {
            // Handle validation errors from createNew (e.g., empty service)
            $this->error('Failed to create token: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            // Handle unexpected errors (e.g., database issues)
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
