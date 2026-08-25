<?php

namespace App\Http\ViewHelpers\Employee;

use App\Helpers\DateHelper;
use App\Models\Company\Employee;

class EmployeeEquityViewHelper
{
    /**
     * All equity grants for the given employee, with vesting computed.
     *
     * @param Employee $employee
     * @return array
     */
    public static function grants(Employee $employee): array
    {
        return $employee->equityGrants()
            ->orderBy('grant_date', 'desc')
            ->get()
            ->map(function ($grant) {
                return [
                    'id' => $grant->id,
                    'grant_type' => $grant->grant_type,
                    'units' => $grant->units,
                    'strike_price' => $grant->strike_price,
                    'grant_date' => DateHelper::formatDate($grant->grant_date),
                    'vesting_start_date' => DateHelper::formatDate($grant->vesting_start_date),
                    'cliff_months' => $grant->cliff_months,
                    'vesting_months' => $grant->vesting_months,
                    'notes' => $grant->notes,
                    'vested_percent' => $grant->vestedPercent(),
                    'vested_units' => $grant->vestedUnits(),
                ];
            })
            ->values()
            ->all();
    }
}
