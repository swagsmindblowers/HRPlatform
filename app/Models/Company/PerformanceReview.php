<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerformanceReview extends Model
{
    use HasFactory;

    const TYPE_30_DAY = '30_day';
    const TYPE_60_DAY = '60_day';
    const TYPE_90_DAY = '90_day';
    const TYPE_AD_HOC = 'ad_hoc';

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';

    protected $table = 'performance_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_type',
        'review_date',
        'rating',
        'strengths',
        'areas_for_improvement',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'review_date',
    ];

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return BelongsTo
     */
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }
}
