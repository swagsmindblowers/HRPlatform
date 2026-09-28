<?php

namespace App\Services\Company\Adminland\Compliance;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use Illuminate\Validation\Rule;
use App\Models\Company\CompanyComplianceItem;

class UpdateComplianceItemStatus extends BaseService
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
            'compliance_item_id' => 'required|integer|exists:company_compliance_items,id',
            'status' => ['required', Rule::in([
                CompanyComplianceItem::STATUS_NOT_STARTED,
                CompanyComplianceItem::STATUS_IN_PROGRESS,
                CompanyComplianceItem::STATUS_COMPLETE,
            ])],
        ];
    }

    /**
     * Update the status of a compliance item. HR/admin only.
     *
     * @param array $data
     *
     * @return CompanyComplianceItem
     */
    public function execute(array $data): CompanyComplianceItem
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $item = CompanyComplianceItem::where('company_id', $data['company_id'])
            ->findOrFail($data['compliance_item_id']);

        $item->update([
            'status' => $data['status'],
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'compliance_item_status_updated',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'compliance_item_id' => $item->id,
                'status' => $item->status,
            ]),
        ])->onQueue('low');

        return $item;
    }
}
