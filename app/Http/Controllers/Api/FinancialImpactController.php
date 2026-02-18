<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialImpact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinancialImpactController extends Controller
{
    /**
     * Record a revenue impact (failed payment, system error, downtime).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'impact_type' => 'required|string|in:failed_payment,system_error,downtime,custom',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'reference_id' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'occurred_at' => 'nullable|date',
            'environment' => 'nullable|string|in:production,development,staging,testing',
            'session_id' => 'nullable|string|max:64',
            'user_id' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $app = $request->attributes->get('app');

        $impact = FinancialImpact::create([
            'app_id' => $app->id,
            'environment' => $request->input('environment', 'production'),
            'impact_type' => $request->input('impact_type'),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency', 'USD'),
            'reference_id' => $request->input('reference_id'),
            'description' => $request->input('description'),
            'metadata' => $request->input('metadata'),
            'occurred_at' => $request->input('occurred_at') ? now()->parse($request->input('occurred_at')) : now(),
            'session_id' => $request->input('session_id'),
            'user_id' => $request->input('user_id'),
        ]);

        return response()->json([
            'success' => true,
            'id' => $impact->id,
        ], 201);
    }
}
