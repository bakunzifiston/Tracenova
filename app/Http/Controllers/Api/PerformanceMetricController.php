<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerformanceMetricController extends Controller
{
    /**
     * Record a performance metric (screen load time, API response time, session duration).
     * Set is_slow=true when the value exceeds your threshold.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'metric_type' => 'required|string|in:screen_load,api_response,session_duration,custom',
            'name' => 'nullable|string|max:255',
            'value' => 'required|integer|min:0',
            'value_unit' => 'nullable|string|in:ms,s',
            'is_slow' => 'nullable|boolean',
            'threshold' => 'nullable|integer|min:0',
            'metadata' => 'nullable|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
            'country_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $metric = PerformanceMetric::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'metric_type' => $request->input('metric_type'),
            'name' => $request->input('name'),
            'value' => $request->input('value'),
            'value_unit' => $request->input('value_unit', 'ms'),
            'is_slow' => $request->boolean('is_slow', false),
            'threshold' => $request->input('threshold'),
            'metadata' => $request->input('metadata'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'url' => $request->input('url') ?? $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'country_code' => $request->input('country_code'),
            'country' => $request->input('country'),
            'region' => $request->input('region'),
            'city' => $request->input('city'),
        ]);

        return response()->json([
            'success' => true,
            'id' => $metric->id,
        ], 201);
    }
}
