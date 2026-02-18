<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JourneyEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JourneyController extends Controller
{
    /**
     * Record a custom journey step (for user journey mapping).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'step_name' => 'required|string|max:255',
            'step_type' => 'nullable|string|in:screen,action,custom|max:40',
            'payload' => 'nullable|array',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'required|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $event = JourneyEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'step_name' => $request->input('step_name'),
            'step_type' => $request->input('step_type', 'custom'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json(['success' => true, 'id' => $event->id], 201);
    }
}
