<?php

namespace App\Services\Ai\Tools;

use Exception;
use App\Models\Company\Company;
use App\Models\Company\Employee;

/**
 * Small shared helper: resolve a free-text name to a single employee within
 * a company, for tools that let the AI act on someone else's behalf.
 */
class ResolveEmployeeByName
{
    public static function forCompany(Company $company, string $name): Employee
    {
        $matches = Employee::where('company_id', $company->id)
            ->where(function ($query) use ($name) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'.$name.'%'])
                    ->orWhere('first_name', 'like', '%'.$name.'%')
                    ->orWhere('last_name', 'like', '%'.$name.'%');
            })
            ->get();

        if ($matches->count() === 0) {
            throw new Exception("No employee found matching \"{$name}\".");
        }

        if ($matches->count() > 1) {
            $names = $matches->pluck('name')->implode(', ');
            throw new Exception("Several employees match \"{$name}\": {$names}. Please be more specific.");
        }

        return $matches->first();
    }
}
