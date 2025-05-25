<?php

namespace Tests;

use App\Models\ApiToken;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Test case for API token middleware functionality.
 */
class MiddlewareTest extends TestCase
{
    /**
     * Test that an invalid token returns a 401 Unauthorized response.
     *
     * @return void
     */
    public function test_using_an_invalid_token_returns_unauthorised(): void
    {
        // Create a valid token for the 'test' service
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a request with an invalid token
        $response = $this->call('GET', '/_test', ['api_token' => 'invalidtoken']);

        // Assert that the response is 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test that no token returns a 401 Unauthorized response.
     *
     * @return void
     */
    public function test_using_no_token_returns_unauthorised(): void
    {
        // Create a token (not used in the request)
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a request without a token
        $response = $this->call('GET', '/_test');

        // Assert that the response is 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test that a valid token in a URL parameter returns a 200 OK response.
     *
     * @return void
     */
    public function test_using_a_valid_token_as_a_url_param_returns_ok(): void
    {
        // Create a valid token for the 'test' service
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a request with the valid token as a URL parameter
        $response = $this->call('GET', '/_test', ['api_token' => $token]);

        // Assert that the response is 200 OK
        $response->assertStatus(200);
    }

    /**
     * Test that a valid token in a JSON payload returns a 200 OK response.
     *
     * @return void
     */
    public function test_using_a_valid_token_as_a_json_field_returns_ok(): void
    {
        // Create a valid token for the 'test' service
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a JSON request with the valid token
        $response = $this->json('GET', '/_test', ['api_token' => $token]);

        // Assert that the response is 200 OK
        $response->assertStatus(200);
    }

    /**
     * Test that a valid token in a form field returns a 200 OK response.
     *
     * @return void
     */
    public function test_using_a_valid_token_as_a_form_field_returns_ok(): void
    {
        // Create a valid token for the 'test' service
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a POST request with the valid token as a form field
        $response = $this->call('POST', '/_test', ['api_token' => $token]);

        // Assert that the response is 200 OK
        $response->assertStatus(200);
    }

    /**
     * Test that a valid token as a Bearer token returns a 200 OK response.
     *
     * @return void
     */
    public function test_using_a_valid_token_as_a_bearer_token_returns_ok(): void
    {
        // Create a valid token for the 'test' service
        $token = ApiToken::createNew('test');
        // Register a test route with the apitoken middleware
        Route::middleware('apitoken:test')->any('/_test', fn() => 'OK');

        // Send a request with the valid token as a Bearer token
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->get('/_test');

        // Assert that the response is 200 OK
        $response->assertStatus(200);
    }

    /**
     * Test that multiple service tokens can be used, and invalid ones are rejected.
     *
     * @return void
     */
    public function test_we_can_use_multiple_api_service_tokens(): void
    {
        // Create tokens for multiple services
        $token1 = ApiToken::createNew('test1');
        $token2 = ApiToken::createNew('test2');
        $token3 = ApiToken::createNew('test3');
        // Register a test route allowing test1 and test2 services
        Route::middleware('apitoken:test1,test2')->any('/_test', fn() => 'OK');

        // Test with token1 (valid)
        $response = $this->call('GET', '/_test', ['api_token' => $token1]);
        $response->assertStatus(200);

        // Test with token2 (valid)
        $response = $this->call('GET', '/_test', ['api_token' => $token2]);
        $response->assertStatus(200);

        // Test with token3 (invalid for this route)
        $response = $this->call('GET', '/_test', ['api_token' => $token3]);
        $response->assertStatus(401);
    }

    /**
     * Test that a non-existent service name returns a 401 Unauthorized response.
     *
     * @return void
     */
    public function test_using_a_non_existant_service_name_always_returns_unauthorised(): void
    {
        // Create a token for a valid service
        $token = ApiToken::createNew('test');
        // Register a test route with a non-existent service
        Route::middleware('apitoken:nottest')->any('/_test', fn() => 'OK');

        // Send a request with the token
        $response = $this->call('GET', '/_test', ['api_token' => $token]);

        // Assert that the response is 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test that no service name returns a 401 Unauthorized response.
     *
     * @return void
     */
    public function test_using_no_service_name_always_returns_unauthorised(): void
    {
        // Create a token for a valid service
        $token = ApiToken::createNew('test');
        // Register a test route with no service specified
        Route::middleware('apitoken')->any('/_test', fn() => 'OK');

        // Send a request with the token
        $response = $this->call('GET', '/_test', ['api_token' => $token]);

        // Assert that the response is 401 Unauthorized
        $response->assertStatus(401);
    }
}