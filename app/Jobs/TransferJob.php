<?php

namespace App\Jobs;

use App\Jobs\DeadLetterJob;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [5, 15, 30, 60, 120];
    public function __construct(
        public int $from,
        public int $to,
        public float $amount,
        public $reference,
        public ?string $notes,
        public $user_id
    ) {
        // Set the queue name for this job
        $this->onQueue('transfer');
    }

    public function handle()
    {
        // Perform the transfer logic
        app(WalletService::class)->utilityTransfer(
            $this->from,
            $this->to,
            $this->amount,
            $this->reference,
            $this->notes,
            $this->user_id
        );
    }

    public function failed(\Throwable $exception)
    {
        // Log the failure and dispatch to dead letter queue
        dispatch(new DeadLetterJob([
            'job' => self::class,
            'payload' => [
                'from' => $this->from,
                'to' => $this->to,
                'amount' => $this->amount,
                'reference' => $this->reference,
                'notes' => $this->notes,
            ],
            'error' => $exception->getMessage(),
        ]))->onQueue('dead-letter');
    }
}
