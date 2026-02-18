<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\App;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * Generate a new API key for an app. The raw key is returned only once.
     *
     * @return array{api_key: ApiKey, raw_key: string}
     */
    public function generate(App $app, string $name = 'Default', ?\DateTimeInterface $expiresAt = null): array
    {
        $rawKey = ApiKey::PREFIX . Str::random(40);
        $keyHash = ApiKey::hashKey($rawKey);
        $keyPrefix = ApiKey::extractPrefix($rawKey);

        $apiKey = $app->apiKeys()->create([
            'name' => $name,
            'key_hash' => $keyHash,
            'key_prefix' => $keyPrefix,
            'expires_at' => $expiresAt,
        ]);

        return ['api_key' => $apiKey, 'raw_key' => $rawKey];
    }

    /**
     * Find and validate an API key by raw value. Updates last_used_at.
     */
    public function validateAndTouch(string $rawKey): ?ApiKey
    {
        $prefix = ApiKey::extractPrefix($rawKey);
        $hash = ApiKey::hashKey($rawKey);

        $apiKey = ApiKey::where('key_prefix', $prefix)
            ->where('key_hash', $hash)
            ->with('app')
            ->first();

        if (!$apiKey || !$apiKey->isValid()) {
            return null;
        }

        if (!$apiKey->app->is_tracking_enabled) {
            return null;
        }

        $apiKey->update(['last_used_at' => now()]);

        return $apiKey;
    }

    /**
     * Revoke an API key (delete it).
     */
    public function revoke(ApiKey $apiKey): bool
    {
        return $apiKey->delete();
    }
}
