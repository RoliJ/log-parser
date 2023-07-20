<?php

namespace App\Jobs;

use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * A job to process log data and insert it into the database.
 */
class ProcessLogDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logData;

    /**
     * Create a new job instance.
     *
     * @param array $logData
     * @return void
     */
    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Use the Log model's insert method to efficiently insert the log data in bulk
        Log::insert($this->logData);
    }
}