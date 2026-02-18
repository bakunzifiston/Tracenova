<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataIntegrityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataIntegrityController extends Controller
{
    /**
     * Record data integrity issues: negative stock, duplicate orders, missing transactions.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|in:negative_stock,duplicate_order,missing_transaction',
            'reference_id' => 'nullable|string|max:128',
            'description' => 'nullable|string|max:512',
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

        $event = DataIntegrityEvent::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'event_type' => $request->input('event_type'),
            'reference_id' => $request->input('reference_id'),
            'description' => $request->input('description'),
            'payload' => $request->input('payload'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $event->id,
        ], 201);
    }
}
