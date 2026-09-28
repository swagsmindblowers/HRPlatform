<?php

namespace App\BetterOff\Engine\Dto;

final class CandidateTakeHomeResult
{
    /**
     * @param string[] $assumptions
     */
    public function __construct(
        public readonly float $currentNetMonthly,
        public readonly float $newNetMonthly,
        public readonly float $betterOffByPerMonth,
        public readonly array $assumptions,
        public readonly \DateTimeInterface $asOfDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'current_net_monthly' => $this->currentNetMonthly,
            'new_net_monthly' => $this->newNetMonthly,
            'better_off_by_per_month' => $this->betterOffByPerMonth,
            'assumptions' => $this->assumptions,
            'as_of_date' => $this->asOfDate->format('Y-m-d'),
        ];
    }
}
