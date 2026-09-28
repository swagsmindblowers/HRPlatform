<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeEquityGrant extends Model
{
    use HasFactory;

    protected $table = 'employee_equity_grants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'grant_type',
        'units',
        'strike_price',
        'grant_date',
        'vesting_start_date',
        'cliff_months',
        'vesting_months',
        'notes',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'grant_date',
        'vesting_start_date',
    ];

    /**
     * Get the employee record associated with the equity grant.
     *
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Standard cliff + straight-line monthly vesting: 0% before the cliff,
     * cliff_months worth vests all at once on the cliff date, then the
     * remainder vests evenly each month until vesting_months is reached.
     *
     * @return float
     */
    public function vestedPercent(): float
    {
        $now = Carbon::now();
        $cliffDate = $this->vesting_start_date->copy()->addMonths($this->cliff_months);

        if ($now->lessThan($cliffDate)) {
            return 0.0;
        }

        $monthsVested = $this->vesting_start_date->diffInMonths($now);
        $monthsVested = min($monthsVested, $this->vesting_months);

        return round(($monthsVested / $this->vesting_months) * 100, 2);
    }

    /**
     * @return int
     */
    public function vestedUnits(): int
    {
        return (int) floor($this->units * ($this->vestedPercent() / 100));
    }
}
