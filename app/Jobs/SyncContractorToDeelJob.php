<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Company\Employee;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Integrations\Deel\SyncContractorToDeel;

class SyncContractorToDeelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Employee
     */
    public Employee $employee;

    /**
     * Create a new job instance.
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new SyncContractorToDeel)->execute($this->employee);
    }
}
