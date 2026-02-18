<?php

namespace App\Http\Middleware;

use App\Services\ApiKeyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTrackingApiKey
{
    public function __construct(
        protected ApiKeyService $apiKeyService
    ) {}

    /**
     * Handle an incoming request. Expects API key in header X-Api-Key or Authorization: Bearer <key>.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawKey = $request->header('X-Api-Key')
            ?? $request->bearerToken()
            ?? $request->input('api_key');

        if (!$rawKey) {
            return response()->json([
                'success' => false,
                'error' => 'Missing API key. Provide X-Api-Key header, Authorization Bearer token, or api_key parameter.',
            ], 401);
        }

        $apiKey = $this->apiKeyService->validateAndTouch($rawKey);

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or expired API key, or tracking is disabled for this app.',
            ], 403);
        }

        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('app', $apiKey->app);

        return $next($request);
    }
}
