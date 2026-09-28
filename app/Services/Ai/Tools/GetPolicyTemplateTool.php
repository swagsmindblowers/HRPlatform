<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Http\ViewHelpers\Adminland\AdminComplianceViewHelper;

class GetPolicyTemplateTool implements AiTool
{
    public static function name(): string
    {
        return 'get_policy_template';
    }

    public static function description(): string
    {
        return 'List the company\'s HR policy templates (e.g. right to work, disciplinary, health & safety), optionally filtered by jurisdiction. These are starting-point templates, not legal advice.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'jurisdiction' => [
                    'type' => 'string',
                    'description' => 'Optional jurisdiction filter, e.g. "uk".',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $templates = AdminComplianceViewHelper::policyTemplates($company);

        if (! empty($input['jurisdiction'])) {
            $templates = array_values(array_filter($templates, fn ($t) => $t['jurisdiction'] === $input['jurisdiction']));
        }

        return [
            'templates' => $templates,
            'disclaimer' => 'These are starting-point templates, not legal advice. Have them reviewed by a qualified professional before use.',
        ];
    }
}
