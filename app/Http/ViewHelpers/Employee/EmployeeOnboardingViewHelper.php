<?php

namespace App\Http\ViewHelpers\Employee;

use App\Helpers\DateHelper;
use App\Models\Company\Employee;
use App\Models\Company\EmployeeOnboardingChecklist;

class EmployeeOnboardingViewHelper
{
    /**
     * The onboarding checklist for the given employee, if one has been
     * started.
     *
     * @param Employee $employee
     * @return array|null
     */
    public static function checklist(Employee $employee): ?array
    {
        $checklist = EmployeeOnboardingChecklist::where('employee_id', $employee->id)
            ->with('items')
            ->first();

        if (! $checklist) {
            return null;
        }

        return [
            'id' => $checklist->id,
            'started_at' => DateHelper::formatDate($checklist->started_at),
            'items' => $checklist->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'type' => $item->type,
                    'is_legally_mandated' => $item->is_legally_mandated,
                    'due_date' => $item->due_date ? DateHelper::formatDate($item->due_date) : null,
                    'completed_at' => $item->completed_at ? DateHelper::formatDate($item->completed_at) : null,
                    'is_overdue' => $item->isOverdue(),
                ];
            })->values()->all(),
        ];
    }
}
