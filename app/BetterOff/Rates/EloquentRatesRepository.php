<?php

namespace App\BetterOff\Rates;

use App\Models\BetterOff\RateVersion;

class EloquentRatesRepository implements RatesRepositoryInterface
{
    public function asOf(string $key, \DateTimeInterface $date): RateVersionData
    {
        $row = RateVersion::query()
            ->where('key', $key)
            ->whereDate('effective_from', '<=', $date->format('Y-m-d'))
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $date->format('Y-m-d'));
            })
            ->orderByDesc('effective_from')
            ->first();

        if (! $row) {
            throw RateNotFoundException::forKeyAsOf($key, $date);
        }

        return new RateVersionData(
            key: $row->key,
            value: (float) $row->value,
            unit: $row->unit,
            sourceUrl: $row->source_url,
            effectiveFrom: $row->effective_from,
            verifiedBy: $row->verified_by,
            verifiedAt: $row->verified_at,
        );
    }

    public function currentVersionTag(): string
    {
        $latest = RateVersion::query()->max('updated_at');

        return $latest ? 'rates-'.substr(md5((string) $latest), 0, 12) : 'rates-empty';
    }
}
