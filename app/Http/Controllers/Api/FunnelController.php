<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Models\FunnelStepEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FunnelController extends Controller
{
    /**
     * Record a funnel step event. Funnel identified by funnel_id (from dashboard).
     */
    public function storeStep(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'funnel_id' => 'required_without:funnel_slug|nullable|integer|exists:funnels,id',
            'funnel_slug' => 'required_without:funnel_id|nullable|string|max:255',
            'step_key' => 'required|string|max:80',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'required|string|max:64',
            'user_id' => 'nullable|string|max:64',
            'occurred_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $funnel = null;
        if ($request->has('funnel_id')) {
            $funnel = Funnel::where('id', $request->input('funnel_id'))->where('app_id', $app->id)->first();
        } else {
            $funnel = Funnel::where('app_id', $app->id)->where('slug', $request->input('funnel_slug'))->first();
        }

        if (!$funnel) {
            return response()->json(['success' => false, 'error' => 'Funnel not found.'], 404);
        }

        $steps = $funnel->steps ?? [];
        $stepIndex = array_search($request->input('step_key'), $steps, true);
        if ($stepIndex === false) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid step_key. Expected one of: ' . implode(', ', $steps),
            ], 422);
        }

        $event = FunnelStepEvent::create([
            'funnel_id' => $funnel->id,
            'environment' => $request->input('environment', 'production'),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
            'step_key' => $request->input('step_key'),
            'step_index' => $stepIndex,
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'metadata' => $request->input('metadata'),
        ]);

        return response()->json(['success' => true, 'id' => $event->id], 201);
    }
}
