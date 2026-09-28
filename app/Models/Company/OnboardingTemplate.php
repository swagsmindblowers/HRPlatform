<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnboardingTemplate extends Model
{
    use HasFactory;

    const JURISDICTION_GENERIC = 'generic';
    const JURISDICTION_UK = 'uk';
    const JURISDICTION_US = 'us';

    protected $table = 'onboarding_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'jurisdiction',
        'is_contractor_template',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_contractor_template' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(OnboardingTemplateItem::class)->orderBy('position');
    }

    /**
     * Find the best-matching system template for a jurisdiction, falling
     * back to the generic template if no jurisdiction-specific one exists.
     *
     * @param string $jurisdiction
     * @param bool $contractor
     * @return self|null
     */
    public static function findBestMatch(string $jurisdiction, bool $contractor = false): ?self
    {
        if ($contractor) {
            return self::whereNull('company_id')
                ->where('is_contractor_template', true)
                ->first();
        }

        return self::whereNull('company_id')
            ->where('jurisdiction', $jurisdiction)
            ->where('is_contractor_template', false)
            ->first()
            ?? self::whereNull('company_id')
                ->where('jurisdiction', self::JURISDICTION_GENERIC)
                ->where('is_contractor_template', false)
                ->first();
    }
}
