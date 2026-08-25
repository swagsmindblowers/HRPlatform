<?php

namespace App\Services\Company\Adminland\Compliance;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use Illuminate\Validation\Rule;
use App\Models\Company\CompanyComplianceItem;

class CreateComplianceItem extends BaseService
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
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in([
                CompanyComplianceItem::CATEGORY_INSURANCE,
                CompanyComplianceItem::CATEGORY_TAX,
                CompanyComplianceItem::CATEGORY_PENSION,
                CompanyComplianceItem::CATEGORY_IMMIGRATION,
            ])],
            'jurisdiction' => 'required|string|max:255',
            'due_date' => 'nullable|date_format:Y-m-d',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Create a company-wide compliance item. HR/admin only.
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

        $item = CompanyComplianceItem::create([
            'company_id' => $data['company_id'],
            'title' => $data['title'],
            'category' => $data['category'],
            'jurisdiction' => $data['jurisdiction'],
            'status' => CompanyComplianceItem::STATUS_NOT_STARTED,
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'compliance_item_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'compliance_item_id' => $item->id,
                'title' => $item->title,
            ]),
        ])->onQueue('low');

        return $item;
    }
}
