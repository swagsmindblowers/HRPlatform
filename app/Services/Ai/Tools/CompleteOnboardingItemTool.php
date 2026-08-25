<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Employee\EmployeeOnboardingViewHelper;
use App\Services\Company\Employee\Onboarding\CompleteOnboardingChecklistItem;

class CompleteOnboardingItemTool implements AiTool
{
    public static function name(): string
    {
        return 'complete_onboarding_item';
    }

    public static function description(): string
    {
        return 'Mark one of the acting employee\'s own onboarding checklist items as completed.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'checklist_item_id' => [
                    'type' => 'integer',
                    'description' => 'The id of the checklist item to mark complete.',
                ],
            ],
            'required' => ['checklist_item_id'],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        (new CompleteOnboardingChecklistItem)->execute([
            'company_id' => $company->id,
            'author_id' => $actingEmployee->id,
            'employee_id' => $actingEmployee->id,
            'checklist_item_id' => $input['checklist_item_id'],
        ]);

        return [
            'checklist' => EmployeeOnboardingViewHelper::checklist($actingEmployee),
        ];
    }
}
