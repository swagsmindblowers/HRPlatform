<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnboardingTemplateItem extends Model
{
    use HasFactory;

    protected $table = 'onboarding_template_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'onboarding_template_id',
        'title',
        'description',
        'type',
        'offset_days_from_start',
        'is_legally_mandated',
        'position',
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
    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }
}
