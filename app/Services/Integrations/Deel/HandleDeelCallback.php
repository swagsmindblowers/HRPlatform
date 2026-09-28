<?php

namespace App\Services\Integrations\Deel;

use Exception;
use Carbon\Carbon;
use App\Services\BaseService;
use App\Models\Company\Integration;
use Illuminate\Support\Facades\Http;

class HandleDeelCallback extends BaseService
{
    const TOKEN_URL = 'https://app.deel.com/oauth2/tokens';

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
     * Exchange the authorization code Deel redirected back with for an
     * access/refresh token pair. HR/admin only.
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

        $tokenResponse = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'authorization_code',
            'code' => $data['code'],
            'client_id' => config('services.deel.client_id'),
            'client_secret' => config('services.deel.client_secret'),
            'redirect_uri' => url(config('services.deel.redirect')),
        ]);

        if ($tokenResponse->failed()) {
            throw new Exception('Failed to exchange the Deel authorization code: '.$tokenResponse->body());
        }

        $tokens = $tokenResponse->json();

        return Integration::updateOrCreate(
            ['company_id' => $data['company_id'], 'provider' => Integration::PROVIDER_DEEL],
            [
                'status' => Integration::STATUS_CONNECTED,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_at' => Carbon::now()->addSeconds($tokens['expires_in'] ?? 3600),
                'external_account_id' => $tokens['organization_id'] ?? null,
                'connected_by' => $this->author->id,
                'connected_at' => Carbon::now(),
                'last_error' => null,
            ]
        );
    }
}
