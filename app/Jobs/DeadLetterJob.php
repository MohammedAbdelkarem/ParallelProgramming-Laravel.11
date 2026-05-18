<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeadLetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $data
    )
    {
        // Set the queue name for this job
        $this->onQueue('dead-letter');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Log the failed job details for debugging
        Log::channel('dlq')->error('Dead Letter Job', $this->data);
    }
}
