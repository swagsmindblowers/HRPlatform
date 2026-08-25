<?php

namespace App\Services\Company\Employee\Equity;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use Illuminate\Validation\Rule;
use App\Models\Company\EmployeeEquityGrant;

class CreateEquityGrant extends BaseService
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
            'grant_type' => ['required', Rule::in(['options', 'rsu'])],
            'units' => 'required|integer|min:1',
            'strike_price' => 'nullable|integer|min:0',
            'grant_date' => 'required|date_format:Y-m-d',
            'vesting_start_date' => 'required|date_format:Y-m-d',
            'cliff_months' => 'required|integer|min:0',
            'vesting_months' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Create an equity grant for the given employee. HR/admin only - equity
     * awards are not something an employee can grant themselves.
     *
     * @param array $data
     *
     * @return EmployeeEquityGrant
     */
    public function execute(array $data): EmployeeEquityGrant
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $grant = EmployeeEquityGrant::create([
            'employee_id' => $data['employee_id'],
            'grant_type' => $data['grant_type'],
            'units' => $data['units'],
            'strike_price' => $data['strike_price'] ?? null,
            'grant_date' => $data['grant_date'],
            'vesting_start_date' => $data['vesting_start_date'],
            'cliff_months' => $data['cliff_months'],
            'vesting_months' => $data['vesting_months'],
            'notes' => $data['notes'] ?? null,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'equity_grant_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'grant_id' => $grant->id,
            ]),
        ])->onQueue('low');

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'equity_grant_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'grant_id' => $grant->id,
            ]),
        ])->onQueue('low');

        return $grant;
    }
}
