<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class SeedDefaultPolicyTemplates extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        foreach ($this->templates() as $template) {
            DB::table('policy_templates')->insert([
                'jurisdiction' => $template['jurisdiction'],
                'category' => $template['category'],
                'title' => $template['title'],
                'body' => $template['body'],
                'is_system_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('policy_templates')->where('is_system_default', true)->delete();
    }

    /**
     * A UK-focused starter policy library. These are starting-point
     * templates, not legal advice - the app surfaces a persistent
     * disclaimer alongside them.
     *
     * @return array
     */
    private function templates(): array
    {
        $disclaimer = "> **Not legal advice.** This is a starting-point template. Have it reviewed by a qualified employment lawyer or HR professional before use, and adapt it to {{company_name}}'s specific circumstances.\n\n";

        return [
            [
                'jurisdiction' => 'uk',
                'category' => 'immigration',
                'title' => 'Right to Work Policy',
                'body' => $disclaimer."# Right to Work Policy\n\n{{company_name}} is committed to preventing illegal working and complies with the Immigration, Asylum and Nationality Act 2006.\n\n## Policy\n\n1. Before employment begins, {{company_name}} will conduct a right to work check on every prospective employee, either via an original document check or the Home Office online checking service.\n2. Checks will be repeated for employees with time-limited permission to work, before their permission expires.\n3. Evidence of checks will be securely retained for the duration of employment and for two years afterwards.\n4. No individual will begin work until a satisfactory right to work check has been completed.\n\n## Sponsor licence duties\n\nWhere {{company_name}} holds a sponsor licence, this policy also covers the record-keeping and reporting duties required by UK Visas and Immigration (UKVI), including tracking Certificate of Sponsorship assignments and reporting changes in circumstances.",
            ],
            [
                'jurisdiction' => 'uk',
                'category' => 'immigration',
                'title' => 'Sponsor Licence Compliance Policy',
                'body' => $disclaimer."# Sponsor Licence Compliance Policy\n\n## Purpose\n\nThis policy sets out how {{company_name}} meets its sponsor duties as a UK Visas and Immigration (UKVI) licensed sponsor.\n\n## Sponsor duties\n\n1. Maintain accurate records of sponsored employees, including right to work evidence, contact details, and current role.\n2. Report significant changes (role change, salary change, absence, resignation, or termination) to UKVI within the required timeframe.\n3. Assign Certificates of Sponsorship only for genuine vacancies that meet the relevant route's requirements.\n4. Monitor sponsored employees' immigration status and visa expiry dates, and act on any lapses.\n5. Cooperate fully with UKVI compliance visits and audits.\n\n## Record retention\n\nSponsorship-related records are retained for the duration of sponsorship and for one year afterwards, in line with UKVI guidance.",
            ],
            [
                'jurisdiction' => 'uk',
                'category' => 'hr',
                'title' => 'Disciplinary & Grievance Policy',
                'body' => $disclaimer."# Disciplinary & Grievance Policy\n\n## Disciplinary procedure\n\n1. Informal resolution will be attempted first, where appropriate.\n2. Where formal action is needed, {{company_name}} will follow the ACAS Code of Practice on disciplinary and grievance procedures: investigate, notify in writing, hold a hearing, allow the right to be accompanied, and confirm the outcome in writing with a right of appeal.\n3. Disciplinary sanctions may include a verbal warning, written warning, final written warning, or dismissal, depending on severity.\n\n## Grievance procedure\n\n1. Employees should raise grievances in writing with their manager or HR in the first instance.\n2. {{company_name}} will arrange a meeting to discuss the grievance without unreasonable delay, and confirm the outcome in writing with a right of appeal.",
            ],
            [
                'jurisdiction' => 'uk',
                'category' => 'hr',
                'title' => 'Equal Opportunities Policy',
                'body' => $disclaimer."# Equal Opportunities Policy\n\n{{company_name}} is committed to equality of opportunity and to providing a working environment free from discrimination, in line with the Equality Act 2010.\n\n## Policy\n\n1. {{company_name}} will not discriminate on the grounds of any protected characteristic, including age, disability, gender reassignment, marriage/civil partnership, pregnancy/maternity, race, religion or belief, sex, or sexual orientation.\n2. This applies to recruitment, terms of employment, training, promotion, and termination.\n3. Employees who believe they have experienced discrimination should raise this via the Grievance Policy.\n4. Reasonable adjustments will be made for employees and candidates with disabilities.",
            ],
            [
                'jurisdiction' => 'uk',
                'category' => 'hr',
                'title' => 'Health & Safety Policy',
                'body' => $disclaimer."# Health & Safety Policy\n\n{{company_name}} is committed to providing a safe and healthy working environment in compliance with the Health and Safety at Work etc. Act 1974.\n\n## Responsibilities\n\n1. {{company_name}} will assess and manage workplace risks, provide necessary training, and maintain safe systems of work.\n2. Employees are required to take reasonable care of their own health and safety and that of others affected by their work.\n3. Accidents and near-misses must be reported promptly.\n4. {{company_name}} maintains employer's liability insurance as required by the Employers' Liability (Compulsory Insurance) Act 1969.",
            ],
            [
                'jurisdiction' => 'uk',
                'category' => 'hr',
                'title' => 'Data Protection Policy',
                'body' => $disclaimer."# Data Protection Policy\n\n{{company_name}} processes personal data in accordance with the UK GDPR and the Data Protection Act 2018.\n\n## Policy\n\n1. Personal data will be processed lawfully, fairly, and transparently, and only for specified, legitimate purposes.\n2. Data will be kept accurate, up to date, and no longer than necessary.\n3. Appropriate technical and organisational measures will be used to protect personal data against unauthorised access, loss, or damage.\n4. Employees and candidates have the right to access, correct, or request erasure of their personal data, subject to legal exceptions.\n5. Data breaches will be assessed and, where required, reported to the ICO within 72 hours.",
            ],
        ];
    }
}
