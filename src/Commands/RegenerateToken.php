<?php

namespace UoGSoE\ApiTokenMiddleware\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

/**
 * Console command to regenerate an API token for a specified service.
 */
class RegenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apitoken:regenerate {service : The service name of the API token to regenerate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate an API token for a specified service';

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

        try {
            // Regenerate the token using the ApiToken model's regenerate method
            $token = ApiToken::regenerate($service);

            // Display the regenerated token in a table
            $this->info('Token regenerated successfully:');
            $this->table(
                ['Service', 'Token'],
                [[$service, $token]]
            );

            // Provide additional usage instructions
            $this->comment('Use this token in API requests via Authorization: Bearer <token>.');

            return 0; // Success
        } catch (\InvalidArgumentException $e) {
            // Handle validation errors from regenerate (e.g., empty or non-existent service)
            $this->error('Failed to regenerate token: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            // Handle unexpected errors (e.g., database issues)
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
