<?php

namespace App\Jobs;

use App\Jobs\DeadLetterJob;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class CartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // retry up to 3 times

    public $backoff = [10, 30, 60]; // exponential backoff

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $data,
    )
    {
        // Set the queue name for this job
        $this->onQueue('cart'); 
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {

            // Lock the order row
            $orderId = app(OrderService::class)->getOrderId($this->data['user_id']);
            $order = Order::where('id', $orderId)->lockForUpdate()->firstOrFail();

            // Lock the product row
            $product = Product::where('id', $this->data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();


            $orderItem = app(OrderService::class)->createOrderItem(
                $order, 
                $product, 
                $this->data
            );
        });
    }

    // Handle failed job
    public function failed(\Throwable $exception)
    {
        dispatch(new DeadLetterJob([
            'job' => self::class,
            'payload' => [
                'data' => $this->data,
            ],
            'error' => $exception->getMessage(),
        ]))->onQueue('dead-letter');
    }
}
