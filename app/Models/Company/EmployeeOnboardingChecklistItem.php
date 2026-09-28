<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeOnboardingChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'employee_onboarding_checklist_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_onboarding_checklist_id',
        'title',
        'description',
        'type',
        'is_legally_mandated',
        'due_date',
        'completed_at',
        'completed_by',
        'position',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'due_date',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_legally_mandated' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function checklist()
    {
        return $this->belongsTo(EmployeeOnboardingChecklist::class, 'employee_onboarding_checklist_id');
    }

    /**
     * @return bool
     */
    public function isOverdue(): bool
    {
        return ! $this->completed_at && $this->due_date && $this->due_date->isPast();
    }
}
