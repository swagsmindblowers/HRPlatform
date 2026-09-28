<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class SeedDefaultOnboardingTemplates extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        foreach ($this->templates() as $template) {
            $templateId = DB::table('onboarding_templates')->insertGetId([
                'company_id' => null,
                'name' => $template['name'],
                'jurisdiction' => $template['jurisdiction'],
                'is_contractor_template' => $template['is_contractor_template'] ?? false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($template['items'] as $position => $item) {
                DB::table('onboarding_template_items')->insert([
                    'onboarding_template_id' => $templateId,
                    'title' => $item['title'],
                    'description' => $item['description'] ?? null,
                    'type' => $item['type'],
                    'offset_days_from_start' => $item['offset_days_from_start'],
                    'is_legally_mandated' => $item['is_legally_mandated'] ?? false,
                    'position' => $position,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('onboarding_templates')->whereNull('company_id')->delete();
    }

    /**
     * The system-default onboarding templates seeded for every company.
     *
     * @return array
     */
    private function templates(): array
    {
        return [
            [
                'name' => 'Generic onboarding',
                'jurisdiction' => 'generic',
                'items' => [
                    ['title' => 'Send offer letter', 'type' => 'task', 'offset_days_from_start' => -7],
                    ['title' => 'Set up tool access (email, Slack, laptop)', 'type' => 'task', 'offset_days_from_start' => -1],
                    ['title' => 'Day 1 welcome & orientation', 'type' => 'task', 'offset_days_from_start' => 0],
                    ['title' => '30-day check-in', 'type' => 'task', 'offset_days_from_start' => 30],
                    ['title' => '60-day check-in', 'type' => 'task', 'offset_days_from_start' => 60],
                    ['title' => '90-day check-in', 'type' => 'task', 'offset_days_from_start' => 90],
                ],
            ],
            [
                'name' => 'Contractor onboarding',
                'jurisdiction' => 'generic',
                'is_contractor_template' => true,
                'items' => [
                    ['title' => 'Send contractor agreement', 'type' => 'task', 'offset_days_from_start' => -7],
                    ['title' => 'Set up tool access', 'type' => 'task', 'offset_days_from_start' => -1],
                    ['title' => 'Day 1 kickoff', 'type' => 'task', 'offset_days_from_start' => 0],
                    ['title' => '30-day check-in', 'type' => 'task', 'offset_days_from_start' => 30],
                ],
            ],
            [
                'name' => 'UK onboarding & compliance',
                'jurisdiction' => 'uk',
                'items' => [
                    ['title' => 'Right to work check', 'description' => 'Verify and record evidence of the employee\'s right to work in the UK before their first day.', 'type' => 'compliance_deadline', 'offset_days_from_start' => 0, 'is_legally_mandated' => true],
                    ['title' => 'PAYE new starter reporting', 'description' => 'Report the new starter to HMRC via PAYE (P45 or starter checklist) on or before their first payday.', 'type' => 'compliance_deadline', 'offset_days_from_start' => 0, 'is_legally_mandated' => true],
                    ['title' => 'Workplace pension auto-enrolment assessment', 'description' => 'Assess the employee for auto-enrolment and enrol eligible jobholders within 6 weeks of their start date.', 'type' => 'compliance_deadline', 'offset_days_from_start' => 42, 'is_legally_mandated' => true],
                    ['title' => 'Sponsor licence right-to-work + CoS assignment', 'description' => 'For visa-sponsored hires: assign the Certificate of Sponsorship and confirm right-to-work evidence matches sponsor licence duties.', 'type' => 'compliance_deadline', 'offset_days_from_start' => 0, 'is_legally_mandated' => true],
                    ['title' => 'Set up tool access', 'type' => 'task', 'offset_days_from_start' => -1],
                    ['title' => 'Day 1 welcome & orientation', 'type' => 'task', 'offset_days_from_start' => 0],
                    ['title' => '30-day check-in', 'type' => 'task', 'offset_days_from_start' => 30],
                    ['title' => '60-day check-in', 'type' => 'task', 'offset_days_from_start' => 60],
                    ['title' => '90-day check-in', 'type' => 'task', 'offset_days_from_start' => 90],
                ],
            ],
            [
                'name' => 'US onboarding & compliance',
                'jurisdiction' => 'us',
                'items' => [
                    ['title' => 'Form I-9 employment eligibility verification', 'description' => 'Complete and retain Form I-9 within 3 business days of the employee\'s start date.', 'type' => 'compliance_deadline', 'offset_days_from_start' => 3, 'is_legally_mandated' => true],
                    ['title' => 'State new-hire reporting', 'description' => 'Report the new hire to the state new-hire registry (typically within 20 days of the start date).', 'type' => 'compliance_deadline', 'offset_days_from_start' => 20, 'is_legally_mandated' => true],
                    ['title' => 'Set up tool access', 'type' => 'task', 'offset_days_from_start' => -1],
                    ['title' => 'Day 1 welcome & orientation', 'type' => 'task', 'offset_days_from_start' => 0],
                    ['title' => '30-day check-in', 'type' => 'task', 'offset_days_from_start' => 30],
                    ['title' => '60-day check-in', 'type' => 'task', 'offset_days_from_start' => 60],
                    ['title' => '90-day check-in', 'type' => 'task', 'offset_days_from_start' => 90],
                ],
            ],
        ];
    }
}
