<?php

namespace App\Models\BetterOff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company\Company;
use App\Models\Company\Candidate;
use App\Models\Company\Employee;

/**
 * Created when a sponsored hire is confirmed from a saved Scenario. Anchors
 * the generated compliance tasks (right to work check, CoS record,
 * reporting deadlines) so the offer-to-compliance handoff needs no re-entry.
 */
class SponsoredWorker extends Model
{
    protected $table = 'betteroff_sponsored_workers';

    protected $fillable = [
        'company_id',
        'scenario_id',
        'candidate_id',
        'employee_id',
        'confirmed_start_date',
    ];

    protected $casts = [
        'confirmed_start_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(SponsoredWorkerTask::class, 'sponsored_worker_id');
    }
}
