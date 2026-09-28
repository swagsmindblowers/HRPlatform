<?php

namespace App\Services\Company\Employee\Equity;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use App\Models\Company\EmployeeEquityGrant;

class DestroyEquityGrant extends BaseService
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
            'equity_grant_id' => 'required|integer|exists:employee_equity_grants,id',
        ];
    }

    /**
     * Destroy an equity grant. HR/admin only.
     *
     * @param array $data
     *
     * @return bool
     */
    public function execute(array $data): bool
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $grant = EmployeeEquityGrant::where('id', $data['equity_grant_id'])
            ->where('employee_id', $data['employee_id'])
            ->firstOrFail();

        $grant->delete();

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'equity_grant_destroyed',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
            ]),
        ])->onQueue('low');

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'equity_grant_destroyed',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([]),
        ])->onQueue('low');

        return true;
    }
}
