<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyComplianceItem extends Model
{
    use HasFactory;

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETE = 'complete';

    const CATEGORY_INSURANCE = 'insurance';
    const CATEGORY_TAX = 'tax';
    const CATEGORY_PENSION = 'pension';
    const CATEGORY_IMMIGRATION = 'immigration';

    protected $table = 'company_compliance_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'title',
        'category',
        'jurisdiction',
        'status',
        'due_date',
        'notes',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'due_date',
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
