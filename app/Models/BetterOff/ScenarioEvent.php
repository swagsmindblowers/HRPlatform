<?php

namespace App\Models\BetterOff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit trail row for a scenario: who ran, saved, or shared it, and when.
 */
class ScenarioEvent extends Model
{
    public const ACTION_RAN = 'ran';
    public const ACTION_SAVED = 'saved';
    public const ACTION_SHARED = 'shared';

    protected $table = 'betteroff_scenario_events';

    protected $fillable = [
        'scenario_id',
        'user_id',
        'action',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }
}
