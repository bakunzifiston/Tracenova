<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Funnel extends Model
{
    protected $fillable = ['app_id', 'name', 'slug', 'steps'];

    protected $casts = ['steps' => 'array'];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    public function stepEvents(): HasMany
    {
        return $this->hasMany(FunnelStepEvent::class, 'funnel_id');
    }

    /** Get step key at index (0-based). */
    public function getStepKeyAt(int $index): ?string
    {
        $steps = $this->steps;
        return is_array($steps) && isset($steps[$index]) ? $steps[$index] : null;
    }

    /** Count distinct sessions that reached each step index. */
    public function getStepCounts(): array
    {
        $counts = [];
        $steps = $this->steps ?? [];
        foreach (array_keys($steps) as $index) {
            $counts[$index] = (int) $this->stepEvents()
                ->where('step_index', $index)
                ->selectRaw('count(distinct session_id) as c')
                ->value('c');
        }
        return $counts;
    }

    /** Drop-off count between step i and step i+1. */
    public function getDropOffs(): array
    {
        $counts = $this->getStepCounts();
        $dropOffs = [];
        for ($i = 0; $i < count($this->steps) - 1; $i++) {
            $dropOffs[$i] = ($counts[$i] ?? 0) - ($counts[$i + 1] ?? 0);
        }
        return $dropOffs;
    }
}
