<?php

namespace App\Services\Integrations\Deel;

use App\Services\BaseService;

class ConnectDeel extends BaseService
{
    const AUTHORIZE_URL = 'https://app.deel.com/oauth2/authorize';
    const SCOPES = 'contracts:read contracts:write timesheets:write';

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
            'state' => 'required|string',
        ];
    }

    /**
     * Build the Deel OAuth2 authorize URL the browser should be redirected
     * to. HR/admin only - connecting a company's payroll software is an
     * admin-level action.
     *
     * @param array $data
     *
     * @return string
     */
    public function execute(array $data): string
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.deel.client_id'),
            'redirect_uri' => url(config('services.deel.redirect')),
            'scope' => self::SCOPES,
            'state' => $data['state'],
        ]);

        return self::AUTHORIZE_URL.'?'.$query;
    }
}
