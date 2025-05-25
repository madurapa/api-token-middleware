<?php

namespace UoGSoE\ApiTokenMiddleware\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

/**
 * Console command to list all API tokens.
 */
class ListTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apitoken:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all current API tokens';

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
        try {
            // Retrieve all API tokens, selecting only the service and created_at fields
            $tokens = ApiToken::select('service', 'created_at')->get();

            // Check if any tokens exist
            if ($tokens->isEmpty()) {
                $this->info('No API tokens found.');
                return 0; // Success, but no data
            }

            // Display the tokens in a table (excluding token field due to $hidden)
            $this->info('Current API tokens:');
            $this->table(
                ['Service', 'Created At'],
                $tokens->map(function ($token) {
                    return [
                        $token->service,
                        $token->created_at->toDateTimeString(),
                    ];
                })
            );

            return 0; // Success
        } catch (\Exception $e) {
            // Handle unexpected errors (e.g., database issues)
            $this->error('Failed to list tokens: ' . $e->getMessage());
            return 1; // Failure
        }
    }
}
