<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    /**
     * Ingest a single tracking event from external apps.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:80',
            'payload' => 'required|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'url' => 'nullable|string|max:2048',
            'occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $app = $request->attributes->get('app');

        $event = TrackingEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'type' => $request->input('type'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'payload' => $request->input('payload'),
            'url' => $request->input('url') ?? $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }

    /**
     * Ingest multiple events in one request.
     */
    public function storeBatch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.type' => 'required|string|max:80',
            'events.*.payload' => 'required|array',
            'events.*.environment' => 'nullable|string|in:production,development,staging,testing',
            'events.*.session_id' => 'nullable|string|max:64',
            'events.*.user_id' => 'nullable|string|max:64',
            'events.*.url' => 'nullable|string|max:2048',
            'events.*.occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $app = $request->attributes->get('app');
        $ids = [];

        foreach ($request->input('events') as $item) {
            $event = TrackingEvent::create([
                'app_id' => $app->id,
                'environment' => $item['environment'] ?? 'production',
                'type' => $item['type'],
                'session_id' => $item['session_id'] ?? null,
                'user_id' => $item['user_id'] ?? null,
                'payload' => $item['payload'],
                'url' => $item['url'] ?? $request->header('Referer'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'occurred_at' => isset($item['occurred_at']) ? now()->parse($item['occurred_at']) : now(),
            ]);
            $ids[] = $event->id;
        }

        return response()->json([
            'success' => true,
            'count' => count($ids),
            'ids' => $ids,
        ], 201);
    }
}
