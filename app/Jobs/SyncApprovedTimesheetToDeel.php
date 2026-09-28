<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Company\Timesheet;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Integrations\Deel\SyncTimesheetToDeel;

class SyncApprovedTimesheetToDeel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Timesheet
     */
    public Timesheet $timesheet;

    /**
     * Create a new job instance.
     */
    public function __construct(Timesheet $timesheet)
    {
        $this->timesheet = $timesheet;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new SyncTimesheetToDeel)->execute($this->timesheet);
    }
}
