<?php

namespace App\Services\Integrations\Xero;

use App\Services\BaseService;

class ConnectXero extends BaseService
{
    const AUTHORIZE_URL = 'https://login.xero.com/identity/connect/authorize';
    const SCOPES = 'openid profile email offline_access accounting.transactions accounting.contacts';

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
     * Build the Xero OAuth2 authorize URL the browser should be redirected
     * to. HR/admin only - connecting a company's accounting software is an
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
            'client_id' => config('services.xero.client_id'),
            'redirect_uri' => url(config('services.xero.redirect')),
            'scope' => self::SCOPES,
            'state' => $data['state'],
        ]);

        return self::AUTHORIZE_URL.'?'.$query;
    }
}
