<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ErrorController extends Controller
{
    /**
     * Record an error (message, stack trace, file/line, user & device info, severity).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:65535',
            'stack_trace' => 'nullable|string|max:65535',
            'file' => 'nullable|string|max:1024',
            'line' => 'nullable|integer|min:0',
            'severity' => 'nullable|string|in:debug,info,warning,error,critical',
            'user_info' => 'nullable|array',
            'device_info' => 'nullable|array',
            'context' => 'nullable|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $event = ErrorEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'message' => $request->input('message'),
            'stack_trace' => $request->input('stack_trace'),
            'file' => $request->input('file'),
            'line' => $request->input('line'),
            'severity' => $request->input('severity', 'error'),
            'user_info' => $request->input('user_info'),
            'device_info' => $request->input('device_info'),
            'context' => $request->input('context'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'url' => $request->input('url') ?? $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
