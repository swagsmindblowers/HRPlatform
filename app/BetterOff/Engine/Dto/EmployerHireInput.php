<?php

namespace App\BetterOff\Engine\Dto;

final class EmployerHireInput
{
    public const HIRE_TYPE_UK = 'uk';
    public const HIRE_TYPE_SPONSORED = 'sponsored';

    public const EMPLOYER_SIZE_SMALL = 'small';
    public const EMPLOYER_SIZE_LARGE = 'large';

    public function __construct(
        public readonly string $hireType,
        public readonly float $annualSalary,
        public readonly string $employerSizeClass,
        public readonly bool $hasSponsorLicence,
        public readonly \DateTimeInterface $asOfDate,
        public readonly ?string $socCode = null,
        public readonly int $contractLengthYears = 3,
        public readonly float $employerVisaCostSharePercent = 100.0,
        public readonly bool $includeRelocation = false,
        public readonly ?float $relocationCostOverride = null,
    ) {
    }
}
