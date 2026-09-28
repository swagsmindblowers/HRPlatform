<?php

namespace App\Services\BetterOff;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Models\BetterOff\Scenario;
use App\Models\BetterOff\ScenarioEvent;

/**
 * Generates (or refreshes) a signed, time-limited share link for a saved
 * scenario's candidate view. The employer chooses to share; the link never
 * exposes employer cost lines — that's enforced by the controller serving
 * only the candidate_result, not the full scenario, at the share URL.
 */
class ShareScenario extends BaseService
{
    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'author_id' => 'required|integer|exists:employees,id',
            'scenario_id' => 'required|integer|exists:betteroff_scenarios,id',
        ];
    }

    public function execute(array $data): Scenario
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canExecuteService();

        $scenario = Scenario::where('company_id', $data['company_id'])->findOrFail($data['scenario_id']);

        $scenario->update([
            'share_token' => $scenario->share_token ?? bin2hex(random_bytes(16)),
            'share_expires_at' => Carbon::now()->addDays(30),
        ]);

        ScenarioEvent::create([
            'scenario_id' => $scenario->id,
            'user_id' => $this->author->user_id ?? null,
            'action' => ScenarioEvent::ACTION_SHARED,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'betteroff_scenario_shared',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode(['scenario_id' => $scenario->id]),
        ])->onQueue('low');

        return $scenario;
    }
}
