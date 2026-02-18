<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Funnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FunnelController extends Controller
{
    public function index(App $app): View
    {
        $this->authorize('view', $app);

        $funnels = $app->funnels()->withCount('stepEvents')->latest()->get();
        return view('funnels.index', compact('app', 'funnels'));
    }

    public function create(App $app): View
    {
        $this->authorize('view', $app);

        return view('funnels.create', compact('app'));
    }

    public function store(Request $request, App $app): RedirectResponse
    {
        $this->authorize('view', $app);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'steps' => 'required|string|max:2000', // comma or newline separated
        ]);

        $steps = array_map('trim', preg_split('/[\s,]+/', $validated['steps'], -1, PREG_SPLIT_NO_EMPTY));
        $steps = array_values(array_unique(array_filter($steps)));
        if (count($steps) < 2) {
            return redirect()->back()->withInput()->withErrors(['steps' => 'At least 2 steps are required.']);
        }

        $slug = Str::slug($validated['name']);
        $existing = $app->funnels()->where('slug', $slug)->first();
        if ($existing) {
            $slug = $slug . '-' . substr(uniqid(), -4);
        }

        $app->funnels()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'steps' => $steps,
        ]);

        return redirect()->route('apps.funnels.index', $app)->with('success', 'Funnel created.');
    }

    public function show(App $app, Funnel $funnel): View
    {
        $this->authorize('view', $app);

        if ($funnel->app_id !== $app->id) {
            abort(404);
        }
        $stepCounts = $funnel->getStepCounts();
        $dropOffs = $funnel->getDropOffs();
        return view('funnels.show', compact('app', 'funnel', 'stepCounts', 'dropOffs'));
    }

    public function edit(App $app, Funnel $funnel): View
    {
        $this->authorize('view', $app);

        if ($funnel->app_id !== $app->id) {
            abort(404);
        }
        return view('funnels.edit', compact('app', 'funnel'));
    }

    public function update(Request $request, App $app, Funnel $funnel): RedirectResponse
    {
        $this->authorize('view', $app);

        if ($funnel->app_id !== $app->id) {
            abort(404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'steps' => 'required|string|max:2000',
        ]);

        $steps = array_map('trim', preg_split('/[\s,]+/', $validated['steps'], -1, PREG_SPLIT_NO_EMPTY));
        $steps = array_values(array_unique(array_filter($steps)));
        if (count($steps) < 2) {
            return redirect()->back()->withInput()->withErrors(['steps' => 'At least 2 steps are required.']);
        }

        $funnel->update(['name' => $validated['name'], 'steps' => $steps]);
        return redirect()->route('apps.funnels.show', [$app, $funnel])->with('success', 'Funnel updated.');
    }

    public function destroy(App $app, Funnel $funnel): RedirectResponse
    {
        $this->authorize('view', $app);

        if ($funnel->app_id !== $app->id) {
            abort(404);
        }
        $funnel->delete();
        return redirect()->route('apps.funnels.index', $app)->with('success', 'Funnel deleted.');
    }
}
