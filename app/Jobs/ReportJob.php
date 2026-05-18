<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [15, 30, 60];
    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId
    )
    {
        // Set the queue name for this job
        $this->onQueue('report');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report = Report::find($this->reportId);

        // Update report status to processing
        $report->update([
            'status' => ReportStatusEnum::PROCCESSING->value,
        ]);

        // Generate report data
        $data = app(OrderService::class)->utilityGenerate();

        // Update report with generated data and mark as completed
        $report->update([
            'status' => ReportStatusEnum::COMPLETED->value,
            'data' => json_encode($data),
        ]);
    }

    public function failed(\Throwable $exception)
    {
        // Update report status to failed
        dispatch(new DeadLetterJob([
            'job' => self::class,
            'payload' => [
                'report_id' => $this->reportId,
            ],
            'error' => $exception->getMessage(),
        ]))->onQueue('dead-letter');
    }
}
