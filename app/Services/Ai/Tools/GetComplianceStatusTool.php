<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Adminland\AdminComplianceViewHelper;

class GetComplianceStatusTool implements AiTool
{
    public static function name(): string
    {
        return 'get_compliance_status';
    }

    public static function description(): string
    {
        return 'Get the company\'s compliance checklist (insurance, tax, pension, immigration obligations) and their status. HR/admin only.';
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
        if ($actingEmployee->permission_level > 200) {
            throw new \Exception('Only HR/admin can view the company compliance checklist.');
        }

        return [
            'items' => AdminComplianceViewHelper::items($company),
        ];
    }
}
