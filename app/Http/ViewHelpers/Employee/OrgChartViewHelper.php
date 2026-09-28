<?php

namespace App\Http\ViewHelpers\Employee;

use App\Helpers\ImageHelper;
use App\Models\Company\Company;
use App\Models\Company\Employee;
use Illuminate\Support\Collection;

class OrgChartViewHelper
{
    /**
     * Build the full org chart as a forest of trees (most companies will
     * have a single root, but we don't assume it).
     *
     * @param Company $company
     * @return array
     */
    public static function tree(Company $company): array
    {
        $employees = Employee::where('company_id', $company->id)
            ->where('locked', false)
            ->with(['position', 'managers', 'directReports'])
            ->get();

        $rootEmployees = $employees->filter(function (Employee $employee) {
            return $employee->managers->isEmpty();
        });

        return $rootEmployees->map(function (Employee $employee) use ($employees) {
            return self::node($employee, $employees, collect([$employee->id]));
        })->values()->all();
    }

    /**
     * @param Employee $employee
     * @param Collection $allEmployees
     * @param Collection $visited guards against manager/report loops in bad data
     * @return array
     */
    private static function node(Employee $employee, Collection $allEmployees, Collection $visited): array
    {
        $directReportIds = $employee->directReports->pluck('employee_id');

        $children = $allEmployees
            ->whereIn('id', $directReportIds)
            ->reject(fn (Employee $report) => $visited->contains($report->id))
            ->map(function (Employee $report) use ($allEmployees, $visited) {
                return self::node($report, $allEmployees, $visited->push($report->id));
            })
            ->values()
            ->all();

        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'avatar' => ImageHelper::getAvatar($employee, 64),
            'position' => (! $employee->position) ? null : $employee->position->title,
            'url' => route('employees.show', [
                'company' => $employee->company_id,
                'employee' => $employee->id,
            ]),
            'children' => $children,
        ];
    }
}
