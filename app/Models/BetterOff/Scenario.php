<?php

namespace App\Models\BetterOff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company\Company;
use App\Models\Company\JobOpening;
use App\Models\Company\Candidate;

/**
 * An immutable snapshot of one cost-of-hire calculation: the inputs, the
 * as-at date, and the rates-table version used, so a saved figure can always
 * be reproduced even after the rates table changes later.
 */
class Scenario extends Model
{
    use HasFactory;

    protected $table = 'betteroff_scenarios';

    protected $fillable = [
        'company_id',
        'job_opening_id',
        'candidate_id',
        'created_by_user_id',
        'hire_type',
        'as_of_date',
        'rates_version_tag',
        'inputs',
        'employer_result',
        'candidate_result',
        'share_token',
        'share_expires_at',
    ];

    protected $casts = [
        'as_of_date' => 'date',
        'inputs' => 'array',
        'employer_result' => 'array',
        'candidate_result' => 'array',
        'share_expires_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ScenarioEvent::class, 'scenario_id');
    }
}
