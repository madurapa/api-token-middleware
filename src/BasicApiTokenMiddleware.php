<?php

namespace UoGSoE\ApiTokenMiddleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Middleware to authenticate API requests using Bearer tokens.
 */
class BasicApiTokenMiddleware
{
    public const CODE = 401;
    public const MESSAGE = 'Unauthorized';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$services
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$services): mixed
    {
        if (!$this->authorized($request, $services)) {
            return response()->json(['message' => self::MESSAGE], self::CODE);
        }

        return $next($request);
    }

    /**
     * Check if the request is authorized based on the provided token and services.
     *
     * @param Request $request
     * @param array<string> $services
     * @return bool
     */
    protected function authorized(Request $request, array $services): bool
    {
        $passedToken = $this->extractToken($request);

        if (!$passedToken || empty($services)) {
            return false;
        }

        // Optimize query to fetch only the first matching token
        $apiToken = ApiToken::whereIn('service', $services)
            ->whereNotNull('token')
            ->first();

        if (!$apiToken) {
            return false;
        }

        return Hash::check($passedToken, $apiToken->token);
    }

    /**
     * Extract the API token from the request's Authorization header.
     *
     * @param Request $request
     * @return string|null
     */
    protected function extractToken(Request $request): ?string
    {
        return $request->bearerToken();
    }
}