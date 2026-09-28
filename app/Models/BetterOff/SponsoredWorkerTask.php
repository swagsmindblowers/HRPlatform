<?php

namespace App\Models\BetterOff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Company\CompanyComplianceItem;

class SponsoredWorkerTask extends Model
{
    public const TYPE_RIGHT_TO_WORK_CHECK = 'right_to_work_check';
    public const TYPE_CERTIFICATE_OF_SPONSORSHIP = 'certificate_of_sponsorship';
    public const TYPE_REPORTING_DEADLINE = 'reporting_deadline';

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETE = 'complete';

    protected $table = 'betteroff_sponsored_worker_tasks';

    protected $fillable = [
        'sponsored_worker_id',
        'company_compliance_item_id',
        'type',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function sponsoredWorker(): BelongsTo
    {
        return $this->belongsTo(SponsoredWorker::class, 'sponsored_worker_id');
    }

    /**
     * The underlying CompanyComplianceItem this task mirrors, so the task
     * shows up in the company's existing compliance checklist (Adminland >
     * Compliance) as well as here.
     */
    public function complianceItem(): BelongsTo
    {
        return $this->belongsTo(CompanyComplianceItem::class, 'company_compliance_item_id');
    }
}
