<?php

namespace App\Services\Company\Employee\PerformanceReview;

use Carbon\Carbon;
use App\Jobs\LogAccountAudit;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use Illuminate\Validation\Rule;
use App\Models\Company\PerformanceReview;

class CreatePerformanceReview extends BaseService
{
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
            'employee_id' => 'required|integer|exists:employees,id',
            'review_type' => ['required', Rule::in([
                PerformanceReview::TYPE_30_DAY,
                PerformanceReview::TYPE_60_DAY,
                PerformanceReview::TYPE_90_DAY,
                PerformanceReview::TYPE_AD_HOC,
            ])],
            'review_date' => 'required|date_format:Y-m-d',
            'rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => ['nullable', Rule::in([
                PerformanceReview::STATUS_DRAFT,
                PerformanceReview::STATUS_SUBMITTED,
            ])],
        ];
    }

    /**
     * Create a performance review for the given employee. HR/admin or the
     * employee's manager can create one.
     *
     * @param array $data
     *
     * @return PerformanceReview
     */
    public function execute(array $data): PerformanceReview
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canBypassPermissionLevelIfManager($data['author_id'], $data['employee_id'])
            ->canExecuteService();

        $review = PerformanceReview::create([
            'employee_id' => $employee->id,
            'reviewer_id' => $this->author->id,
            'review_type' => $data['review_type'],
            'review_date' => $data['review_date'],
            'rating' => $data['rating'] ?? null,
            'strengths' => $data['strengths'] ?? null,
            'areas_for_improvement' => $data['areas_for_improvement'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? PerformanceReview::STATUS_DRAFT,
        ]);

        LogAccountAudit::dispatch([
            'company_id' => $data['company_id'],
            'action' => 'performance_review_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'review_id' => $review->id,
            ]),
        ])->onQueue('low');

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'performance_review_created',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => Carbon::now(),
            'objects' => json_encode([
                'review_id' => $review->id,
            ]),
        ])->onQueue('low');

        return $review;
    }
}
