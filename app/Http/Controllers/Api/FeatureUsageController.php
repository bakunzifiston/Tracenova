<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureUsageEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeatureUsageController extends Controller
{
    /**
     * Record feature usage for analytics (most/least used features).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|string|max:128',
            'feature_category' => 'nullable|string|max:64',
            'payload' => 'nullable|array',
            'occurred_at' => 'nullable|date',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $event = FeatureUsageEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'feature_name' => $request->input('feature_name'),
            'feature_category' => $request->input('feature_category'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
