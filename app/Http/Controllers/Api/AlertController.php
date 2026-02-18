<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    /**
     * Record alerts: revenue risk, error spikes, performance drops.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alert_type' => 'required|string|in:revenue_risk,error_spike,performance_drop',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:65535',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'payload' => 'nullable|array',
            'channel' => 'nullable|string|max:32',
            'occurred_at' => 'nullable|date',
            'environment' => 'nullable|string|in:production,development,staging,testing',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $alert = Alert::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'alert_type' => $request->input('alert_type'),
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'severity' => $request->input('severity') ?? 'medium',
            'payload' => $request->input('payload'),
            'channel' => $request->input('channel') ?? 'in_app',
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $alert->id,
        ], 201);
    }
}
