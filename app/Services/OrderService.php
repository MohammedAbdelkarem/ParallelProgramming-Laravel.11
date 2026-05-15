<?php

namespace App\Services;

use App\Constants\ExceptionMessages;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

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
        $order = Order::findByIdOrFail($this->getOrderId());

        $product = Product::findByIdOrFail($data['product_id']);

        $this->checkProductStock($data['product_id'], $data['quantity']);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'product_id' => $data['product_id'],
            'original_price' => $product->price,
            'price' => $product->price * $data['quantity'],
            'quantity' => $data['quantity'],
        ]);

        $this->processProductStock($data['product_id'], $data['quantity']);

        $this->processOrderData($order->id, $orderItem->price, $orderItem->quantity , true);

        return $orderItem;
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

    private function getOrderId()
    {
        $order = Order::where('status' , OrderStatusEnum::PENDING->value)
            ->where('user_id' , auth()->id())->first();

        if($order)
            return $order->id;

        $order = Order::create([
            'user_id' => auth()->id(),
            'price' => 0,
            'quantity' => 0,
        ]);

        return $order->id;
    }

    private function checkProductStock($productId, $quantity)
    {
        $product = Product::findByIdOrFail($productId);

        if ($product->stock < $quantity)
            return forbiddenFailure([] , ExceptionMessages::MSG_PRODUCT_STOCK_NOT_ENOUGH);
    }

    private function processProductStock($productId , $quantity , $increase = false)
    {
        $product = Product::findByIdOrFail($productId);
        
        $product->stock = $increase 
            ? $product->stock + $quantity
            : $product->stock - $quantity;

        $product->save();
    }

    private function processOrderData($order_id , $price , $quantity , $increase = false)
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

    private function deleteEmptyOrder($orderId)
    {
        $order = Order::findByIdOrFail($orderId);

        if($order->quantity == 0)
            $order->delete();
    }
}
