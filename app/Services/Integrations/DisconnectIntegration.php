<?php

namespace App\Services\Integrations;

use App\Services\BaseService;
use Illuminate\Validation\Rule;
use App\Models\Company\Integration;

class DisconnectIntegration extends BaseService
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
            'provider' => ['required', Rule::in([Integration::PROVIDER_XERO, Integration::PROVIDER_DEEL])],
        ];
    }

    /**
     * Disconnect an integration and clear its stored tokens. HR/admin only.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        Integration::where('company_id', $data['company_id'])
            ->where('provider', $data['provider'])
            ->update([
                'status' => Integration::STATUS_DISCONNECTED,
                'access_token' => null,
                'refresh_token' => null,
                'expires_at' => null,
            ]);
    }
}
