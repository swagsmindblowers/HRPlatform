<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Company\Expense;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Integrations\Xero\SyncExpenseToXero;

class SyncApprovedExpenseToXero implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Expense
     */
    public Expense $expense;

    /**
     * Create a new job instance.
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new SyncExpenseToXero)->execute($this->expense);
    }
}
