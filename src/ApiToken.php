<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Eloquent model for managing API tokens.
 */
class ApiToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['service', 'token'];

    /**
     * The attributes that should be hidden for arrays and JSON responses.
     *
     * @var array<string>
     */
    protected $hidden = ['token'];

    /**
     * Create a new API token for the given service.
     *
     * @param string $service The service name for the token.
     * @return string The generated raw token (not hashed).
     * @throws \InvalidArgumentException If the service name is empty.
     */
    public static function createNew(string $service): string
    {
        // Validate the service name
        if (empty($service)) {
            throw new \InvalidArgumentException('Service name cannot be empty');
        }

        // Generate a random 32-character token
        $newToken = Str::random(32);

        // Create and save the token record with a hashed token
        static::create([
            'service' => $service,
            'token' => Hash::make($newToken),
        ]);

        // Return the raw (unhashed) token
        return $newToken;
    }

    /**
     * Regenerate an API token for the given service.
     *
     * @param string $service The service name for the token.
     * @return string The regenerated raw token (not hashed).
     * @throws \InvalidArgumentException If the service name is empty or no token exists for the service.
     */
    public static function regenerate(string $service): string
    {
        // Validate the service name
        if (empty($service)) {
            throw new \InvalidArgumentException('Service name cannot be empty');
        }

        // Find the token for the given service
        $token = static::where('service', $service)->first();

        // Throw an exception if no token is found
        if (!$token) {
            throw new \InvalidArgumentException('No token found for service: ' . $service);
        }

        // Generate a new random 32-character token
        $newToken = Str::random(32);

        // Update the token with the new hashed value
        $token->token = Hash::make($newToken);
        $token->save();

        // Return the raw (unhashed) token
        return $newToken;
    }
}
