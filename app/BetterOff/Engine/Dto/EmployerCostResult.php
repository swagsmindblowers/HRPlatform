<?php

namespace App\BetterOff\Engine\Dto;

final class EmployerCostResult
{
    /**
     * @param LineItem[] $lineItems
     * @param string[] $warnings
     */
    public function __construct(
        public readonly string $hireType,
        public readonly array $lineItems,
        public readonly float $totalCost,
        public readonly array $warnings,
        public readonly \DateTimeInterface $asOfDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'hire_type' => $this->hireType,
            'line_items' => array_map(fn (LineItem $i) => $i->toArray(), $this->lineItems),
            'total_cost' => $this->totalCost,
            'warnings' => $this->warnings,
            'as_of_date' => $this->asOfDate->format('Y-m-d'),
        ];
    }
}
