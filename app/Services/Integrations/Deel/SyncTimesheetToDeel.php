<?php

namespace App\Services\Integrations\Deel;

use Carbon\Carbon;
use App\Models\Company\Timesheet;
use App\Models\Company\Integration;
use Illuminate\Support\Facades\Http;

/**
 * Pushes an approved timesheet's hours to Deel for contractor payroll runs.
 * Verify the endpoint path against Deel's current API reference before
 * relying on this in production, per the note in SyncContractorToDeel.
 */
class SyncTimesheetToDeel
{
    const TIMESHEETS_URL = 'https://api.letsdeel.com/rest/v2/timesheets';

    /**
     * @param Timesheet $timesheet
     */
    public function execute(Timesheet $timesheet): void
    {
        $employee = $timesheet->employee;

        if (! $employee || ! $employee->isContractor()) {
            return;
        }

        $integration = Integration::where('company_id', $timesheet->company_id)
            ->where('provider', Integration::PROVIDER_DEEL)
            ->first();

        if (! $integration || ! $integration->isConnected()) {
            return;
        }

        $totalHours = $timesheet->timeTrackingEntries->sum('duration') / 3600;

        $response = Http::withToken($integration->access_token)
            ->post(self::TIMESHEETS_URL, [
                'external_id' => (string) $employee->id,
                'period_start' => $timesheet->started_at->format('Y-m-d'),
                'period_end' => $timesheet->ended_at->format('Y-m-d'),
                'hours' => round($totalHours, 2),
            ]);

        if ($response->status() === 401) {
            $integration->update(['status' => Integration::STATUS_ERROR, 'last_error' => 'Deel access token rejected (401) - reconnect required.']);

            return;
        }

        if ($response->failed()) {
            $integration->update(['last_error' => "Deel request failed ({$response->status()}): ".$response->body()]);

            return;
        }

        $timesheet->update([
            'external_reference' => $response->json()['id'] ?? null,
            'sync_status' => 'synced',
            'synced_at' => Carbon::now(),
        ]);
    }
}
