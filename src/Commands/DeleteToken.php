<?php

namespace UoGSoE\ApiTokenMiddleware\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

/**
 * Console command to delete an API token for a specified service.
 */
class DeleteToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apitoken:delete {service : The service name of the API token to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an API token for a specified service';

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

        // Validate the service name
        if (empty($service)) {
            $this->error('Service name cannot be empty.');
            return 1;
        }

        try {
            // Attempt to find and delete the token for the given service
            $deleted = ApiToken::where('service', $service)->delete();

            // Check if a token was deleted
            if ($deleted === 0) {
                $this->error("No token found for service '$service'.");
                return 1;
            }

            // Confirm successful deletion
            $this->info("Token for service '$service' deleted successfully.");
            return 0; // Success
        } catch (\Exception $e) {
            // Handle unexpected errors (e.g., database issues)
            $this->error('Failed to delete token: ' . $e->getMessage());
            return 1;
        }
    }
}
