<?php

namespace App\Services\Company\Employee\PerformanceReview;

use Exception;
use App\Services\BaseService;
use App\Jobs\LogEmployeeAudit;
use Illuminate\Validation\Rule;
use App\Models\Company\PerformanceReview;

class UpdatePerformanceReview extends BaseService
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
            'performance_review_id' => 'required|integer|exists:performance_reviews,id',
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
     * Update a performance review. Only the reviewer who created it, or
     * HR/admin, can edit it.
     *
     * @param array $data
     *
     * @return PerformanceReview
     */
    public function execute(array $data): PerformanceReview
    {
        $this->validateRules($data);

        $employee = $this->validateEmployeeBelongsToCompany($data);

        $review = PerformanceReview::where('id', $data['performance_review_id'])
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $this->author($data['author_id'])
            ->inCompany($data['company_id'])
            ->asAtLeastHR()
            ->canBypassPermissionLevelIfEmployee($review->reviewer_id)
            ->canExecuteService();

        if ($this->author->id !== $review->reviewer_id && $this->author->permission_level > 200) {
            throw new Exception('Only the reviewer or HR/admin can edit this review.');
        }

        $review->update(array_filter([
            'rating' => $data['rating'] ?? null,
            'strengths' => $data['strengths'] ?? null,
            'areas_for_improvement' => $data['areas_for_improvement'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? null,
        ], fn ($value) => ! is_null($value)));

        LogEmployeeAudit::dispatch([
            'employee_id' => $employee->id,
            'action' => 'performance_review_updated',
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'audited_at' => now(),
            'objects' => json_encode([
                'review_id' => $review->id,
            ]),
        ])->onQueue('low');

        return $review->fresh();
    }
}
