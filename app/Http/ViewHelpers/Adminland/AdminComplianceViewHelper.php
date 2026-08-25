<?php

namespace App\Http\ViewHelpers\Adminland;

use App\Helpers\DateHelper;
use App\Models\Company\Company;
use App\Models\Company\PolicyTemplate;
use App\Models\Company\CompanyComplianceItem;

class AdminComplianceViewHelper
{
    /**
     * All compliance items for the given company.
     *
     * @param Company $company
     * @return array
     */
    public static function items(Company $company): array
    {
        return CompanyComplianceItem::where('company_id', $company->id)
            ->orderBy('due_date')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'category' => $item->category,
                    'jurisdiction' => $item->jurisdiction,
                    'status' => $item->status,
                    'due_date' => $item->due_date ? DateHelper::formatDate($item->due_date) : null,
                    'notes' => $item->notes,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * All policy templates, grouped by jurisdiction.
     *
     * @param Company $company
     * @return array
     */
    public static function policyTemplates(Company $company): array
    {
        return PolicyTemplate::orderBy('jurisdiction')
            ->orderBy('category')
            ->get()
            ->map(function (PolicyTemplate $template) use ($company) {
                return [
                    'id' => $template->id,
                    'jurisdiction' => $template->jurisdiction,
                    'category' => $template->category,
                    'title' => $template->title,
                    'body' => $template->renderFor($company),
                ];
            })
            ->values()
            ->all();
    }
}
