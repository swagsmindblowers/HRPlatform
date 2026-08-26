<?php

namespace App\Services\Integrations\Xero;

use Exception;
use Carbon\Carbon;
use App\Services\BaseService;
use App\Models\Company\Integration;
use Illuminate\Support\Facades\Http;

class HandleXeroCallback extends BaseService
{
    const TOKEN_URL = 'https://identity.xero.com/connect/token';
    const CONNECTIONS_URL = 'https://api.xero.com/connections';

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
            'code' => 'required|string',
        ];
    }

    /**
     * Exchange the authorization code Xero redirected back with for an
     * access/refresh token pair, then look up the connected Xero
     * organisation (tenant) id. HR/admin only.
     *
     * @param array $data
     *
     * @return Integration
     */
    public function execute(array $data): Integration
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $tokenResponse = Http::asForm()
            ->withBasicAuth(config('services.xero.client_id'), config('services.xero.client_secret'))
            ->post(self::TOKEN_URL, [
                'grant_type' => 'authorization_code',
                'code' => $data['code'],
                'redirect_uri' => url(config('services.xero.redirect')),
            ]);

        if ($tokenResponse->failed()) {
            throw new Exception('Failed to exchange the Xero authorization code: '.$tokenResponse->body());
        }

        $tokens = $tokenResponse->json();

        $connectionsResponse = Http::withToken($tokens['access_token'])
            ->get(self::CONNECTIONS_URL);

        $tenantId = $connectionsResponse->ok() ? ($connectionsResponse->json()[0]['tenantId'] ?? null) : null;

        return Integration::updateOrCreate(
            ['company_id' => $data['company_id'], 'provider' => Integration::PROVIDER_XERO],
            [
                'status' => Integration::STATUS_CONNECTED,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_at' => Carbon::now()->addSeconds($tokens['expires_in'] ?? 1800),
                'external_account_id' => $tenantId,
                'connected_by' => $this->author->id,
                'connected_at' => Carbon::now(),
                'last_error' => null,
            ]
        );
    }
}
