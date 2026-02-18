<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NetworkMonitoringEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NetworkMonitoringController extends Controller
{
    /**
     * Record offline/network events: offline_start, offline_end, sync_retry, network_strength.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|in:offline_start,offline_end,sync_retry,network_strength',
            'occurred_at' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'retry_count' => 'nullable|integer|min:0',
            'success' => 'nullable|boolean',
            'network_strength' => 'nullable|string|max:20',
            'payload' => 'nullable|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $event = NetworkMonitoringEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'event_type' => $request->input('event_type'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'duration_seconds' => $request->input('duration_seconds'),
            'retry_count' => $request->input('retry_count'),
            'success' => $request->input('success'),
            'network_strength' => $request->input('network_strength'),
            'payload' => $request->input('payload'),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
