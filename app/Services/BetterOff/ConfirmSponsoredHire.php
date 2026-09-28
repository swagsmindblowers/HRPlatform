<?php

namespace App\Services\BetterOff;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Models\BetterOff\Scenario;
use App\Models\BetterOff\SponsoredWorker;
use App\Models\BetterOff\SponsoredWorkerTask;
use App\Models\Company\CompanyComplianceItem;
use App\Services\Company\Adminland\Compliance\CreateComplianceItem;

/**
 * Confirms a sponsored hire from a saved Scenario and generates the
 * compliance handoff: a right-to-work check due before the start date, a
 * certificate-of-sponsorship record, and a reporting-deadline task. Each
 * generated task is mirrored into the company's existing compliance
 * checklist (CompanyComplianceItem) via the same service Adminland already
 * uses, so nothing needs re-entry and the tasks show up where HR already
 * looks for compliance work.
 *
 * Administrator-only: this is a higher bar than saving a scenario, since it
 * creates real, dated compliance obligations for the company.
 */
class ConfirmSponsoredHire extends BaseService
{
    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'author_id' => 'required|integer|exists:employees,id',
            'scenario_id' => 'required|integer|exists:betteroff_scenarios,id',
            'candidate_id' => 'nullable|integer|exists:candidates,id',
            'employee_id' => 'nullable|integer|exists:employees,id',
            'confirmed_start_date' => 'nullable|date_format:Y-m-d',
        ];
    }

    public function execute(array $data): SponsoredWorker
    {
        $this->validateRules($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastAdministrator()
            ->canExecuteService();

        $scenario = Scenario::where('company_id', $data['company_id'])->findOrFail($data['scenario_id']);

        $sponsoredWorker = SponsoredWorker::create([
            'company_id' => $data['company_id'],
            'scenario_id' => $scenario->id,
            'candidate_id' => $data['candidate_id'] ?? $scenario->candidate_id,
            'employee_id' => $data['employee_id'] ?? null,
            'confirmed_start_date' => $data['confirmed_start_date'] ?? null,
        ]);

        $startDate = $data['confirmed_start_date'] ?? null;

        $this->createTask(
            $sponsoredWorker,
            SponsoredWorkerTask::TYPE_RIGHT_TO_WORK_CHECK,
            'Right to work check',
            $startDate ? Carbon::parse($startDate)->subDay()->format('Y-m-d') : null,
            'Must be completed before the employee\'s first day.'
        );

        $this->createTask(
            $sponsoredWorker,
            SponsoredWorkerTask::TYPE_CERTIFICATE_OF_SPONSORSHIP,
            'Certificate of sponsorship record',
            $startDate,
            'Assign and record the certificate of sponsorship used for this hire.'
        );

        $this->createTask(
            $sponsoredWorker,
            SponsoredWorkerTask::TYPE_REPORTING_DEADLINE,
            'Sponsor reporting deadline (10 working days)',
            $startDate ? Carbon::parse($startDate)->addWeekdays(10)->format('Y-m-d') : null,
            'Report the start date (or any change) to UKVI via the Sponsorship Management System within 10 working days.'
        );

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'betteroff_sponsored_hire_confirmed',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'sponsored_worker_id' => $sponsoredWorker->id,
                'scenario_id' => $scenario->id,
            ]),
        ])->onQueue('low');

        return $sponsoredWorker->load('tasks');
    }

    private function createTask(SponsoredWorker $sponsoredWorker, string $type, string $title, ?string $dueDate, string $notes): SponsoredWorkerTask
    {
        $complianceItem = (new CreateComplianceItem())->execute([
            'company_id' => $sponsoredWorker->company_id,
            'author_id' => $this->author->id,
            'title' => $title,
            'category' => CompanyComplianceItem::CATEGORY_IMMIGRATION,
            'jurisdiction' => 'UK',
            'due_date' => $dueDate,
            'notes' => $notes,
        ]);

        return SponsoredWorkerTask::create([
            'sponsored_worker_id' => $sponsoredWorker->id,
            'company_compliance_item_id' => $complianceItem->id,
            'type' => $type,
            'due_date' => $dueDate,
            'status' => SponsoredWorkerTask::STATUS_NOT_STARTED,
        ]);
    }
}
