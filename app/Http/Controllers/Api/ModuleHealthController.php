<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleHealthScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleHealthController extends Controller
{
    /**
     * Record module health score (errors, speed, drop-offs).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|string|max:64',
            'module_name' => 'nullable|string|max:128',
            'score' => 'required|numeric|min:0|max:100',
            'period_type' => 'nullable|string|in:daily,weekly',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'errors_count' => 'nullable|integer|min:0',
            'errors_score' => 'nullable|numeric|min:0|max:100',
            'speed_score' => 'nullable|numeric|min:0|max:100',
            'drop_off_score' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|array',
            'recorded_at' => 'nullable|date',
            'environment' => 'nullable|string|in:production,development,staging,testing',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $score = ModuleHealthScore::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'module_id' => $request->input('module_id'),
            'module_name' => $request->input('module_name'),
            'score' => $request->input('score'),
            'period_type' => $request->input('period_type'),
            'period_start' => $request->input('period_start'),
            'period_end' => $request->input('period_end'),
            'errors_count' => $request->input('errors_count'),
            'errors_score' => $request->input('errors_score'),
            'speed_score' => $request->input('speed_score'),
            'drop_off_score' => $request->input('drop_off_score'),
            'metadata' => $request->input('metadata'),
            'recorded_at' => $request->input('recorded_at') ? now()->parse($request->input('recorded_at')) : now(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $score->id,
        ], 201);
    }
}
