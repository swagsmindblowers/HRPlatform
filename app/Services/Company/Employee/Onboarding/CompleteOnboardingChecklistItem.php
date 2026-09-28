<?php

namespace App\Services\Company\Employee\Onboarding;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use App\Models\Company\EmployeeOnboardingChecklistItem;

class CompleteOnboardingChecklistItem extends BaseService
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
            'checklist_item_id' => 'required|integer|exists:employee_onboarding_checklist_items,id',
        ];
    }

    /**
     * Mark an onboarding checklist item as completed. The employee
     * themselves or HR/admin can do this.
     *
     * @param array $data
     *
     * @return EmployeeOnboardingChecklistItem
     */
    public function execute(array $data): EmployeeOnboardingChecklistItem
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canBypassPermissionLevelIfEmployee($data['employee_id'])
            ->canExecuteService();

        $item = EmployeeOnboardingChecklistItem::whereHas('checklist', function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        })->findOrFail($data['checklist_item_id']);

        $item->update([
            'completed_at' => Carbon::now(),
            'completed_by' => $this->author->id,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'onboarding_item_completed',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'item_title' => $item->title,
            ]),
        ])->onQueue('low');

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'onboarding_item_completed',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'item_title' => $item->title,
            ]),
        ])->onQueue('low');

        return $item;
    }
}
