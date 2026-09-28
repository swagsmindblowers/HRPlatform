<?php

namespace App\Services\Integrations\Deel;

use App\Models\Company\Employee;
use App\Models\Company\Integration;
use Illuminate\Support\Facades\Http;

/**
 * Pushes a contractor employee record to Deel so their payments can be
 * run through Deel's contractor payroll. Endpoint paths follow Deel's
 * public REST API (api.letsdeel.com) as documented at the time this was
 * written - verify against Deel's current API reference before relying
 * on this in production, since third-party API surfaces change.
 */
class SyncContractorToDeel
{
    const PEOPLE_URL = 'https://api.letsdeel.com/rest/v2/people';

    /**
     * @param Employee $employee
     */
    public function execute(Employee $employee): void
    {
        if (! $employee->isContractor()) {
            return;
        }

        $integration = Integration::where('company_id', $employee->company_id)
            ->where('provider', Integration::PROVIDER_DEEL)
            ->first();

        if (! $integration || ! $integration->isConnected()) {
            return;
        }

        $response = Http::withToken($integration->access_token)
            ->post(self::PEOPLE_URL, [
                'full_name' => $employee->name,
                'emails' => [$employee->email],
                'external_id' => (string) $employee->id,
            ]);

        if ($response->status() === 401) {
            $integration->update(['status' => Integration::STATUS_ERROR, 'last_error' => 'Deel access token rejected (401) - reconnect required.']);

            return;
        }

        if ($response->failed()) {
            $integration->update(['last_error' => "Deel request failed ({$response->status()}): ".$response->body()]);

            return;
        }

        $integration->update(['last_error' => null]);
    }
}
