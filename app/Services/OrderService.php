<?php

namespace App\Services;

use App\Constants\ExceptionMessages;
use App\Enums\OrderStatusEnum;
use App\Enums\ReportStatusEnum;
use App\Jobs\CartJob;
use App\Jobs\ReportJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderService.
 */
class OrderService
{
    public function get($data)
    {
        return getOrPaginate(
            Order::filter($data)->orderByDesc('created_at')->with('orderItems' , 'user'),
            $data
        );
    }

    public function addToCart($data)
    {
        $this->checkProductStock($data['product_id'] , $data['quantity']);

        $user_id = auth()->id();

        $data['user_id'] = $user_id;

        CartJob::dispatch($data);
    }

    public function removeFromCart($orderItemId)
    {
        $orderItem = OrderItem::findByIdOrFail($orderItemId);

        $orderId = $orderItem->order_id;

        $this->processProductStock($orderItem->product_id, $orderItem->quantity , true);

        $this->processOrderData($orderItem->order_id, $orderItem->price, $orderItem->quantity);

        $orderItem->delete();

        $this->deleteEmptyOrder($orderId);
    }

    public function changeOrderStatus($orderId , $status)
    {
        $order = Order::findByIdOrFail($orderId);
        $order->status = $status;
        $order->save();

        return $order;
    }

    public function generate()
    {
        $report = Report::create([
            'status' => ReportStatusEnum::PENDING->value
        ]);

        ReportJob::dispatch($report->id);
        
        return $report->fresh();
    }

    public function utilityGenerate()
    {
        // ---------------------------------------------
        // 1. AGGREGATED METRICS (no chunking needed)
        // ---------------------------------------------

        $ordersSummary = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('price'),
            'total_quantity' => Order::sum('quantity'),
            'orders_by_status' => Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        $productsSummary = [
            'total_products' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'best_selling_products' => Product::select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as sold'))
                ->join('order_items', 'order_items.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->limit(10)
                ->get(),
        ];

        $orderItemsSummary = [
            'total_items_sold' => OrderItem::sum('quantity'),
            'total_items_revenue' => OrderItem::sum('price'),
            'average_item_price' => OrderItem::avg('price'),
            'average_order_value' => Order::avg('price'),
        ];

        // ---------------------------------------------
        // 2. PER-ORDER HEAVY PROCESSING (requires chunking)
        // ---------------------------------------------

        $orderDetails = [];

        // Dynamic chunking parameters
        $chunk = 200;          // starting chunk size
        $maxChunk = 1000;      // upper limit
        $minChunk = 50;        // lower limit

        $lastId = 0;

        while (true) 
        {

            $start = microtime(true);

            // Fetch chunk of orders
            $orders = Order::where('id', '>', $lastId)
                ->orderBy('id')
                ->limit($chunk)
                ->get();

            if ($orders->isEmpty()) {
                break;
            }

            // Process each order in the chunk
            foreach ($orders as $order) {
                $orderDetails[] = [
                    'order_id' => $order->id,
                    'total_items' => $order->items()->sum('quantity'),
                    'total_price' => $order->items()->sum('price'),
                    'average_item_price' => $order->items()->avg('price'),
                    'created_at' => $order->created_at,
                ];
            }

            // Update last processed ID
            $lastId = $orders->last()->id;

            // Measure processing time
            $duration = microtime(true) - $start;

            // Dynamic chunking logic
            if ($duration < 0.5 && $chunk < $maxChunk) {
                $chunk += 100; // speed is good → increase chunk size
            }

            if ($duration > 1.5 && $chunk > $minChunk) {
                $chunk -= 50; // too slow → decrease chunk size
            }
        }

        // ---------------------------------------------
        // 3. FINAL REPORT OUTPUT
        // ---------------------------------------------

        return [
            'orders' => $ordersSummary,
            'products' => $productsSummary,
            'order_items' => $orderItemsSummary,
            'order_details' => $orderDetails, // new heavy section
        ];
    }

    public function getReports()
    {
        return getOrPaginate(
            Report::orderByDesc('created_at'),
            request()
        );
    }

    public function getOrderId($user_id)
    {
        $order = Order::where('status' , OrderStatusEnum::PENDING->value)
            ->where('user_id' , $user_id)->first();

        if($order)
            return $order->id;

        $order = Order::create([
            'user_id' => $user_id,
            'price' => 0,
            'quantity' => 0,
        ]);

        return $order->id;
    }

    public function checkProductStock($productId, $quantity)
    {
        $product = Product::findByIdOrFail($productId);

        if ($product->stock < $quantity)
            return forbiddenFailure([] , ExceptionMessages::MSG_PRODUCT_STOCK_NOT_ENOUGH);
    }

    public function createOrderItem($order, $product, $data)
    {
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'original_price' => $product->price,
            'price' => $product->price * $data['quantity'],
            'quantity' => $data['quantity'],
        ]);

        $this->processProductStock($data['product_id'], $data['quantity']);
        $this->processOrderData($order->id, $product->price * $data['quantity'], $data['quantity']);


        return $orderItem->fresh();
    }

    public function processProductStock($productId , $quantity , $increase = false)
    {
        $product = Product::findByIdOrFail($productId);
        
        $product->stock = $increase 
            ? $product->stock + $quantity
            : $product->stock - $quantity;

        $product->save();
    }

    public function processOrderData($order_id , $price , $quantity , $increase = false)
    {
        $order = Order::findByIdOrFail($order_id);

        $order->price = $increase 
            ? $order->price + $price
            : $order->price - $price;

        $order->quantity = $increase 
            ? $order->quantity + $quantity
            : $order->quantity - $quantity;

        $order->save();
    }

    public function deleteEmptyOrder($orderId)
    {
        $order = Order::findByIdOrFail($orderId);

        if($order->quantity == 0)
            $order->delete();
    }
}
