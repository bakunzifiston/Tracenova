<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NavigationEvent;
use App\Models\ScreenView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NavigationController extends Controller
{
    /**
     * Record a navigation event (from screen -> to screen, with type).
     */
    public function storeNavigation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_screen' => 'required|string|max:255',
            'to_screen' => 'required|string|max:255',
            'navigation_type' => 'required|string|in:push,replace,back',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $event = NavigationEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'from_screen' => $request->input('from_screen'),
            'to_screen' => $request->input('to_screen'),
            'navigation_type' => $request->input('navigation_type'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'metadata' => $request->input('metadata'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }

    /**
     * Record a screen view (screen name, previous screen, timestamp, load time).
     */
    public function storeScreenView(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'screen_name' => 'required|string|max:255',
            'previous_screen' => 'nullable|string|max:255',
            'occurred_at' => 'nullable|date',
            'load_time_ms' => 'nullable|integer|min:0',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $view = ScreenView::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'screen_name' => $request->input('screen_name'),
            'previous_screen' => $request->input('previous_screen'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'load_time_ms' => $request->input('load_time_ms'),
            'metadata' => $request->input('metadata'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $view->id,
        ], 201);
    }
}
