<?php

namespace App\BetterOff\Rates;

class RateNotFoundException extends \RuntimeException
{
    public static function forKeyAsOf(string $key, \DateTimeInterface $date): self
    {
        return new self(sprintf(
            'No BetterOff rate found for key "%s" as of %s. Every figure must come from the versioned rates table — add a row instead of hardcoding a value.',
            $key,
            $date->format('Y-m-d')
        ));
    }
}
