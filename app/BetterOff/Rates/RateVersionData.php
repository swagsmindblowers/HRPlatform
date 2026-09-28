<?php

namespace App\BetterOff\Rates;

/**
 * Immutable, framework-agnostic view of a single rate as-at a given date.
 * The engine only ever sees this, never the Eloquent model, so it stays a
 * pure, deterministic set of functions.
 */
final class RateVersionData
{
    public function __construct(
        public readonly string $key,
        public readonly float $value,
        public readonly string $unit,
        public readonly string $sourceUrl,
        public readonly \DateTimeInterface $effectiveFrom,
        public readonly ?string $verifiedBy = null,
        public readonly ?\DateTimeInterface $verifiedAt = null,
    ) {
    }
}
