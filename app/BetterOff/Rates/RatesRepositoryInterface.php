<?php

namespace App\BetterOff\Rates;

interface RatesRepositoryInterface
{
    /**
     * Return the rate row valid on the given date.
     *
     * @throws RateNotFoundException
     */
    public function asOf(string $key, \DateTimeInterface $date): RateVersionData;

    /**
     * A short, human-readable tag identifying the current state of the rates
     * table (used to stamp saved scenarios so an old figure can be traced
     * back to what was live when it was calculated).
     */
    public function currentVersionTag(): string;
}
