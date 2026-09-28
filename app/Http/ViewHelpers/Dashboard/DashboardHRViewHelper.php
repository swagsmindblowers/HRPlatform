<?php

namespace App\Http\ViewHelpers\Dashboard;

use Carbon\Carbon;
use App\Helpers\ImageHelper;
use App\Models\Company\Company;
use App\Models\Company\Timesheet;
use Illuminate\Support\Facades\DB;
use App\Models\Company\EmployeeOnboardingChecklistItem;

class DashboardHRViewHelper
{
    /**
     * Get the list of pending validation timesheets for employees who don't
     * have managers, before the current week.
     *
     * @param Company $company
     * @return array
     */
    public static function employeesWithoutManagersWithPendingTimesheets(Company $company): array
    {
        // all the unapproved timesheets of employees without managers
        // except for the current week
        // this query is tricky and i don’t know how to do it efficiently with
        // eloquent. so the way to go is to fetch the timesheets that are ready
        // to submit first, then get all the employees with managers, then
        // remove all those employees from the list of timesheets. that will get
        // us all the timesheets for employees who don’t have managers.
        $timesheets = Timesheet::where('company_id', $company->id)
            ->where('status', Timesheet::READY_TO_SUBMIT)
            ->with('employee')
            ->whereDate('started_at', '<', Carbon::now()->startOfWeek(Carbon::MONDAY))
            ->get();

        // get the list of employees with manager, that we flatten
        /** @phpstan-ignore-next-line */
        $listOfEmployeesWithManagers = DB::table('direct_reports')
            ->where('company_id', $company->id)
            ->select('employee_id')
            ->pluck('employee_id');

        $timesheetsWithUniqueEmployees = $timesheets->unique('employee_id');
        $timesheetsWithUniqueEmployees = $timesheetsWithUniqueEmployees->whereNotIn('employee_id', $listOfEmployeesWithManagers);
        $timesheetsLeft = $timesheets->whereNotIn('employee_id', $listOfEmployeesWithManagers);

        $employeesCollection = collect([]);
        foreach ($timesheetsWithUniqueEmployees as $timesheet) {
            $employee = $timesheet->employee;

            $employeesCollection->push([
                'id' => $employee->id,
                'name' => $employee->name,
                'avatar' => ImageHelper::getAvatar($employee),
            ]);
        }

        return [
            'number_of_timesheets' => $timesheetsLeft->count(),
            'employees' => $employeesCollection,
            'url_view_all' => route('dashboard.hr.timesheet.index', [
                'company' => $company,
            ]),
        ];
    }

    public static function statisticsAboutTimesheets(Company $company)
    {
        $now = Carbon::now();
        $totals = DB::table('timesheets')
            ->whereDate('started_at', '>=', $now->copy()->startOfWeek(Carbon::MONDAY)->subDays(30))
            ->whereDate('started_at', '<', $now->copy()->startOfWeek(Carbon::MONDAY))
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = '".Timesheet::REJECTED."' then 1 end) as rejected")
            ->first();

        return [
            'total' => $totals->total,
            'rejected' => $totals->rejected,
        ];
    }

    /**
     * Get information about the discipline cases.
     */
    public static function disciplineCases(Company $company)
    {
        return [
            'url' => [
                'index' => route('dashboard.hr.disciplinecase.index', [
                    'company' => $company,
                ]),
            ],
        ];
    }

    /**
     * Get absence monitoring information: who's off today, who's off in the
     * next two weeks, and a sickness frequency table for the trailing 12
     * months so HR can spot patterns.
     *
     * @param Company $company
     * @return array
     */
    public static function absences(Company $company): array
    {
        $today = Carbon::now();
        $in14Days = Carbon::now()->addDays(14);
        $twelveMonthsAgo = Carbon::now()->subMonths(12);

        $offToday = DB::table('employee_planned_holidays')
            ->join('employees', 'employees.id', '=', 'employee_planned_holidays.employee_id')
            ->where('employees.company_id', $company->id)
            ->whereDate('employee_planned_holidays.planned_date', $today->format('Y-m-d'))
            ->select('employees.id as employee_id', 'employees.first_name', 'employees.last_name', 'employee_planned_holidays.type', 'employee_planned_holidays.full')
            ->get();

        $upcoming = DB::table('employee_planned_holidays')
            ->join('employees', 'employees.id', '=', 'employee_planned_holidays.employee_id')
            ->where('employees.company_id', $company->id)
            ->whereDate('employee_planned_holidays.planned_date', '>', $today->format('Y-m-d'))
            ->whereDate('employee_planned_holidays.planned_date', '<=', $in14Days->format('Y-m-d'))
            ->orderBy('employee_planned_holidays.planned_date')
            ->select('employees.id as employee_id', 'employees.first_name', 'employees.last_name', 'employee_planned_holidays.planned_date', 'employee_planned_holidays.type', 'employee_planned_holidays.full')
            ->get();

        $sickRecords = DB::table('employee_planned_holidays')
            ->join('employees', 'employees.id', '=', 'employee_planned_holidays.employee_id')
            ->where('employees.company_id', $company->id)
            ->where('employee_planned_holidays.type', 'sick')
            ->whereDate('employee_planned_holidays.planned_date', '>=', $twelveMonthsAgo->format('Y-m-d'))
            ->select('employees.id as employee_id', 'employees.first_name', 'employees.last_name', 'employee_planned_holidays.full')
            ->get();

        $sicknessMonitoring = $sickRecords->groupBy('employee_id')->map(function ($records) {
            $first = $records->first();

            return [
                'employee_id' => (int) $first->employee_id,
                'name' => trim($first->first_name.' '.$first->last_name),
                // number of separate sick days logged - a simple proxy for
                // absence frequency, not a full Bradford Factor calculation.
                'occurrences' => $records->count(),
                'days' => $records->sum(fn ($record) => $record->full ? 1 : 0.5),
            ];
        })->sortByDesc('days')->values();

        return [
            'off_today' => $offToday->map(fn ($row) => [
                'employee_id' => $row->employee_id,
                'name' => trim($row->first_name.' '.$row->last_name),
                'type' => $row->type,
                'full' => (bool) $row->full,
            ])->values(),
            'upcoming' => $upcoming->map(fn ($row) => [
                'employee_id' => $row->employee_id,
                'name' => trim($row->first_name.' '.$row->last_name),
                'date' => Carbon::parse($row->planned_date)->format('M j'),
                'type' => $row->type,
                'full' => (bool) $row->full,
            ])->values(),
            'sickness_monitoring' => $sicknessMonitoring,
        ];
    }

    /**
     * Get company-wide onboarding compliance deadlines: overdue items and
     * items due in the next 14 days, so HR can spot what's about to lapse
     * (right to work checks, pension auto-enrolment windows, I-9s, etc.).
     *
     * @param Company $company
     * @return array
     */
    public static function onboarding(Company $company): array
    {
        $today = Carbon::now();
        $in14Days = Carbon::now()->addDays(14);

        $items = EmployeeOnboardingChecklistItem::whereHas('checklist.employee', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $in14Days->format('Y-m-d'))
            ->with('checklist.employee')
            ->orderBy('due_date')
            ->get();

        $format = function ($item) use ($company) {
            $employee = $item->checklist->employee;

            return [
                'id' => $item->id,
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'title' => $item->title,
                'type' => $item->type,
                'is_legally_mandated' => $item->is_legally_mandated,
                'due_date' => $item->due_date->format('M j'),
                'url' => route('employees.show', [
                    'company' => $company,
                    'employee' => $employee,
                ]),
            ];
        };

        return [
            'overdue' => $items->filter(fn ($item) => $item->due_date->lt($today))->map($format)->values(),
            'upcoming' => $items->filter(fn ($item) => $item->due_date->gte($today))->map($format)->values(),
        ];
    }
}
