<?php

namespace Tests\Support\BetterOff;

use App\BetterOff\Rates\RateVersionData;
use App\BetterOff\Rates\RateNotFoundException;
use App\BetterOff\Rates\RatesRepositoryInterface;

/**
 * Deterministic, DB-free rates source for engine unit tests. Rows are given
 * exactly as the seeded table would provide them, so tests exercise the same
 * as-of lookup semantics as production without needing a database.
 */
class InMemoryRatesRepository implements RatesRepositoryInterface
{
    /** @var array<int, array{key:string, value:float, unit:string, source_url:string, effective_from:string, effective_to:?string}> */
    private array $rows = [];

    public function withRate(string $key, float $value, string $effectiveFrom, ?string $effectiveTo = null, string $unit = 'gbp'): self
    {
        $this->rows[] = [
            'key' => $key,
            'value' => $value,
            'unit' => $unit,
            'source_url' => 'https://example.invalid/test-fixture',
            'effective_from' => $effectiveFrom,
            'effective_to' => $effectiveTo,
        ];

        return $this;
    }

    public function asOf(string $key, \DateTimeInterface $date): RateVersionData
    {
        $candidates = array_filter($this->rows, function ($row) use ($key, $date) {
            if ($row['key'] !== $key) {
                return false;
            }
            if ($row['effective_from'] > $date->format('Y-m-d')) {
                return false;
            }
            if ($row['effective_to'] !== null && $row['effective_to'] < $date->format('Y-m-d')) {
                return false;
            }

            return true;
        });

        usort($candidates, fn ($a, $b) => $b['effective_from'] <=> $a['effective_from']);
        $row = $candidates[0] ?? null;

        if (! $row) {
            throw RateNotFoundException::forKeyAsOf($key, $date);
        }

        return new RateVersionData(
            key: $row['key'],
            value: $row['value'],
            unit: $row['unit'],
            sourceUrl: $row['source_url'],
            effectiveFrom: new \DateTimeImmutable($row['effective_from']),
        );
    }

    public function currentVersionTag(): string
    {
        return 'test-fixture';
    }
}
