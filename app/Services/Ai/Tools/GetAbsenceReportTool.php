<?php

namespace App\Services\Ai\Tools;

use Exception;
use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Dashboard\DashboardHRViewHelper;

class GetAbsenceReportTool implements AiTool
{
    public static function name(): string
    {
        return 'get_absence_report';
    }

    public static function description(): string
    {
        return 'Get the company-wide absence report: who is off today, upcoming absences in the next 14 days, and sickness frequency over the trailing 12 months. Requires HR or admin permissions.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => (object) [],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        if ($actingEmployee->permission_level > config('officelife.permission_level.hr')) {
            throw new Exception('Only HR or admins can view the company-wide absence report.');
        }

        return DashboardHRViewHelper::absences($company);
    }
}
