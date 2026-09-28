<?php

namespace App\Services\Ai\Tools;

use App\Models\Company\Company;
use App\Models\Company\Employee;
use App\Models\Company\Integration;

class GetIntegrationStatusTool implements AiTool
{
    public static function name(): string
    {
        return 'get_integration_status';
    }

    public static function description(): string
    {
        return 'Check whether the company has Xero (accounting) or Deel (payroll) connected, and their sync status.';
    }

    public static function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => (object) [],
            'required' => [],
        ];
    }

    public function execute(array $input, Employee $actingEmployee, Company $company): array
    {
        $integrations = Integration::where('company_id', $company->id)->get()->keyBy('provider');

        $format = function (?Integration $integration) {
            return [
                'status' => $integration->status ?? Integration::STATUS_DISCONNECTED,
                'connected_at' => optional($integration)->connected_at,
                'last_error' => optional($integration)->last_error,
            ];
        };

        return [
            'xero' => $format($integrations->get(Integration::PROVIDER_XERO)),
            'deel' => $format($integrations->get(Integration::PROVIDER_DEEL)),
        ];
    }
}
