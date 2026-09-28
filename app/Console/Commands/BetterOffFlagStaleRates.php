<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BetterOff\RateVersion;

class BetterOffFlagStaleRates extends Command
{
    protected $signature = 'betteroff:flag-stale-rates {--days= : Override the review window in days}';

    protected $description = 'List BetterOff rate rows that have not been re-verified within the configured review window.';

    public function handle()
    {
        $days = (int) ($this->option('days') ?? config('betteroff.rate_review_window_days', 90));
        $cutoff = now()->subDays($days);

        $stale = RateVersion::query()
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('verified_at')->orWhere('verified_at', '<', $cutoff);
            })
            ->get();

        if ($stale->isEmpty()) {
            $this->info("No BetterOff rates are stale (review window: {$days} days).");

            return self::SUCCESS;
        }

        $this->warn("{$stale->count()} BetterOff rate(s) need re-verification (review window: {$days} days):");
        $this->table(
            ['key', 'value', 'last verified', 'source'],
            $stale->map(fn (RateVersion $r) => [
                $r->key,
                $r->value,
                optional($r->verified_at)->toDateString() ?? 'never',
                $r->source_url,
            ])->all()
        );

        return self::FAILURE;
    }
}
