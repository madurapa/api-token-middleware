<?php

namespace Tests;

use App\Models\ApiToken;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Test case for API token Artisan commands and model functionality.
 */
class ArtisanTest extends TestCase
{
    /**
     * Test that creating a new token stores a hashed token in the database.
     *
     * @return void
     */
    public function test_creating_a_new_token_stores_a_hashed_token_in_the_db(): void
    {
        // Create a new token for the 'test' service
        $token = ApiToken::createNew('test');

        // Retrieve the first token from the database
        $dbToken = ApiToken::first();

        // Assert that the raw token is not stored directly (it's hashed)
        $this->assertNotEquals($token, $dbToken->token);
        // Assert that the raw token matches the hashed token in the database
        $this->assertTrue(Hash::check($token, $dbToken->token));
    }

    /**
     * Test that regenerating a token creates a new hashed token.
     *
     * @return void
     */
    public function test_we_can_generate_a_new_hashed_token_for_an_existing_token(): void
    {
        // Create an initial token
        $token = ApiToken::createNew('test');

        // Regenerate the token for the same service
        $newToken = ApiToken::regenerate('test');

        // Retrieve the updated token from the database
        $dbToken = ApiToken::first();

        // Assert that the new token is different from the original
        $this->assertNotEquals($token, $newToken);
        // Assert that the new token matches the hashed token in the database
        $this->assertTrue(Hash::check($newToken, $dbToken->token));
    }

    /**
     * Test that the Artisan command creates a new token.
     *
     * @return void
     */
    public function test_we_can_call_artisan_to_create_a_new_token(): void
    {
        // Assert that no tokens exist initially
        $this->assertCount(0, ApiToken::all());

        // Run the Artisan command to create a token
        $this->artisan('apitoken:create', ['service' => 'test']);

        // Assert that one token now exists
        $this->assertCount(1, ApiToken::all());
        // Assert that the database has the token for the 'test' service
        $this->assertDatabaseHas('api_tokens', ['service' => 'test']);
    }

    /**
     * Test that the Artisan command deletes a token.
     *
     * @return void
     */
    public function test_we_can_call_artisan_to_delete_a_token(): void
    {
        // Create two tokens
        ApiToken::createNew('test1');
        ApiToken::createNew('test2');

        // Run the Artisan command to delete one token
        $this->artisan('apitoken:delete', ['service' => 'test1']);

        // Assert that only one token remains
        $this->assertCount(1, ApiToken::all());
        // Assert that the remaining token is for 'test2'
        $this->assertDatabaseHas('api_tokens', ['service' => 'test2']);
    }

    /**
     * Test that the Artisan command lists all tokens.
     *
     * @return void
     */
    public function test_we_can_call_artisan_to_list_all_tokens(): void
    {
        // Create two tokens
        ApiToken::createNew('test1');
        ApiToken::createNew('test2');

        // Run the Artisan command to list tokens
        $this->artisan('apitoken:list');

        // Get the command output
        $output = Artisan::output();

        // Assert that the output contains the service names
        $this->assertStringContainsString('test1', $output);
        $this->assertStringContainsString('test2', $output);
    }

    /**
     * Test that the Artisan command regenerates a token.
     *
     * @return void
     */
    public function test_we_can_call_artisan_to_regenerate_a_token(): void
    {
        // Create an initial token
        $token = ApiToken::createNew('test');
        $dbToken = ApiToken::first();
        // Verify the initial token is valid
        $this->assertTrue(Hash::check($token, $dbToken->token));

        // Run the Artisan command to regenerate the token
        $this->artisan('apitoken:regenerate', ['service' => 'test']);

        // Retrieve the updated token from the database
        $dbToken = ApiToken::first();
        // Assert that the original token is no longer valid
        $this->assertFalse(Hash::check($token, $dbToken->token));
    }
}