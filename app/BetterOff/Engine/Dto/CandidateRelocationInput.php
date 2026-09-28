<?php

namespace App\BetterOff\Engine\Dto;

final class CandidateRelocationInput
{
    public function __construct(
        public readonly float $currentAnnualSalary,
        public readonly string $currentLocationCostIndexKey,
        public readonly float $newAnnualSalary,
        public readonly string $newLocationCostIndexKey,
        public readonly \DateTimeInterface $asOfDate,
    ) {
    }
}
