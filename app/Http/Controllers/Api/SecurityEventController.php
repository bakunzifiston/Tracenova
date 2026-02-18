<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecurityEventController extends Controller
{
    /**
     * Record security events: failed logins, suspicious activity, token abuse.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|in:failed_login,suspicious_activity,token_abuse',
            'ip_address' => 'nullable|string|max:45',
            'user_identifier' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:512',
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

        $event = SecurityEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'event_type' => $request->input('event_type'),
            'ip_address' => $request->input('ip_address'),
            'user_identifier' => $request->input('user_identifier'),
            'reason' => $request->input('reason'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
