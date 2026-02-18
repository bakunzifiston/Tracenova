<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyApiEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThirdPartyApiController extends Controller
{
    /**
     * Record third-party API calls: payment, SMS, email providers.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_type' => 'required|string|in:payment,sms,email',
            'provider_name' => 'nullable|string|max:128',
            'operation' => 'nullable|string|max:64',
            'success' => 'required|boolean',
            'response_time_ms' => 'nullable|integer|min:0',
            'status_code' => 'nullable|integer|min:0|max:999',
            'error_message' => 'nullable|string|max:512',
            'request_id' => 'nullable|string|max:128',
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

        $event = ThirdPartyApiEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'provider_type' => $request->input('provider_type'),
            'provider_name' => $request->input('provider_name'),
            'operation' => $request->input('operation'),
            'success' => $request->input('success'),
            'response_time_ms' => $request->input('response_time_ms'),
            'status_code' => $request->input('status_code'),
            'error_message' => $request->input('error_message'),
            'request_id' => $request->input('request_id'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
