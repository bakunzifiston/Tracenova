<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * Start a new session.
     */
    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:64',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'user_id' => 'nullable|string|max:64',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');
        $sessionId = $request->input('session_id');

        $session = TrackingSession::firstOrCreate(
            [
                'app_id' => $app->id,
                'session_id' => $sessionId,
            ],
            [
                'environment' => $request->input('environment', 'production'),
                'user_id' => $request->input('user_id'),
                'started_at' => now(),
                'last_activity_at' => now(),
                'metadata' => $request->input('metadata'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]
        );

        if (!$session->wasRecentlyCreated) {
            return response()->json([
                'success' => true,
                'id' => $session->id,
                'message' => 'Session already exists; use heartbeat or end.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'id' => $session->id,
            'session_id' => $session->session_id,
            'started_at' => $session->started_at->toIso8601String(),
        ], 201);
    }

    /**
     * End a session and optionally send final duration / foreground / background.
     */
    public function end(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:64',
            'duration_seconds' => 'nullable|integer|min:0',
            'foreground_seconds' => 'nullable|integer|min:0',
            'background_seconds' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');
        $session = TrackingSession::where('app_id', $app->id)
            ->where('session_id', $request->input('session_id'))
            ->whereNull('ended_at')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or already ended.',
            ], 404);
        }

        $endedAt = now();
        $durationSeconds = $request->input('duration_seconds');
        if ($durationSeconds === null) {
            $durationSeconds = (int) $session->started_at->diffInSeconds($endedAt);
        }

        $session->update([
            'ended_at' => $endedAt,
            'duration_seconds' => $durationSeconds,
            'foreground_seconds' => $request->input('foreground_seconds', $session->foreground_seconds),
            'background_seconds' => $request->input('background_seconds', $session->background_seconds),
            'last_activity_at' => $endedAt,
        ]);

        return response()->json([
            'success' => true,
            'id' => $session->id,
            'session_id' => $session->session_id,
            'ended_at' => $session->ended_at->toIso8601String(),
            'duration_seconds' => $session->duration_seconds,
            'foreground_seconds' => $session->foreground_seconds,
            'background_seconds' => $session->background_seconds,
        ], 200);
    }

    /**
     * Heartbeat: update last activity and optional cumulative foreground/background time.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:64',
            'foreground_seconds' => 'nullable|integer|min:0',
            'background_seconds' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');
        $session = TrackingSession::where('app_id', $app->id)
            ->where('session_id', $request->input('session_id'))
            ->whereNull('ended_at')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found or already ended. Call session/start first.',
            ], 404);
        }

        $data = ['last_activity_at' => now()];
        if ($request->has('foreground_seconds')) {
            $data['foreground_seconds'] = $request->input('foreground_seconds');
        }
        if ($request->has('background_seconds')) {
            $data['background_seconds'] = $request->input('background_seconds');
        }

        $session->update($data);

        return response()->json([
            'success' => true,
            'session_id' => $session->session_id,
            'last_activity_at' => $session->last_activity_at->toIso8601String(),
        ], 200);
    }
}
