<?php

namespace App\Services\Company\Employee\Onboarding;

use Exception;
use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use Illuminate\Validation\Rule;
use App\Models\Company\OnboardingTemplate;
use App\Models\Company\EmployeeOnboardingChecklist;

class CreateOnboardingChecklistForEmployee extends BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'author_id' => 'required|integer|exists:employees,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'jurisdiction' => ['required', Rule::in([
                OnboardingTemplate::JURISDICTION_GENERIC,
                OnboardingTemplate::JURISDICTION_UK,
                OnboardingTemplate::JURISDICTION_US,
            ])],
            'is_contractor' => 'nullable|boolean',
        ];
    }

    /**
     * Start an onboarding checklist for an employee, based on the given
     * jurisdiction. HR/admin only.
     *
     * @param array $data
     *
     * @return EmployeeOnboardingChecklist
     */
    public function execute(array $data): EmployeeOnboardingChecklist
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        if (EmployeeOnboardingChecklist::where('employee_id', $employee->id)->exists()) {
            throw new Exception('This employee already has an onboarding checklist.');
        }

        $template = OnboardingTemplate::findBestMatch($data['jurisdiction'], $data['is_contractor'] ?? false);
        if (! $template) {
            throw new Exception('No onboarding template could be found.');
        }

        $startedAt = $employee->hired_at ? Carbon::parse($employee->hired_at) : Carbon::now();

        $checklist = EmployeeOnboardingChecklist::create([
            'employee_id' => $employee->id,
            'onboarding_template_id' => $template->id,
            'started_at' => $startedAt,
        ]);

        foreach ($template->items as $position => $templateItem) {
            $checklist->items()->create([
                'title' => $templateItem->title,
                'description' => $templateItem->description,
                'type' => $templateItem->type,
                'is_legally_mandated' => $templateItem->is_legally_mandated,
                'due_date' => $startedAt->copy()->addDays($templateItem->offset_days_from_start),
                'position' => $position,
            ]);
        }

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'onboarding_checklist_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'template' => $template->name,
            ]),
        ])->onQueue('low');

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'onboarding_checklist_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'template' => $template->name,
            ]),
        ])->onQueue('low');

        return $checklist;
    }
}
