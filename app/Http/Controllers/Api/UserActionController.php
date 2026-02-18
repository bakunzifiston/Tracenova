<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserActionController extends Controller
{
    /**
     * Record a user action (button click, dashboard access, payment, form submission, etc.).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action_type' => 'required|string|max:80',
            'action_name' => 'nullable|string|max:255',
            'target' => 'nullable|string|max:255',
            'payload' => 'nullable|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $action = UserAction::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'action_type' => $request->input('action_type'),
            'action_name' => $request->input('action_name'),
            'target' => $request->input('target'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'url' => $request->input('url') ?? $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $action->id,
        ], 201);
    }
}
