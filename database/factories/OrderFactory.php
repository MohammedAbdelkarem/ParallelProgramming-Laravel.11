<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id'  => 3,
            'price'    => 0, // سيتم حسابه بعد إنشاء OrderItems
            'quantity' => 0, // سيتم حسابه بعد إنشاء OrderItems
            'status'   => 'pending',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {

            $itemsCount = rand(1, 7);

            // إنشاء OrderItems داخل نفس الفاكتوري
            $items = collect();

            for ($i = 0; $i < $itemsCount; $i++) {

                $originalPrice = fake()->randomFloat(2, 5, 200);
                $quantity      = fake()->numberBetween(1, 5);

                $items->push(
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'user_id'        => 3,
                        'product_id'     => fake()->numberBetween(1, 50),
                        'original_price' => $originalPrice,
                        'price'          => $originalPrice * $quantity,
                        'quantity'       => $quantity,
                    ])
                );
            }

            // تحديث السعر والكمية بناءً على OrderItems
            $order->update([
                'price'    => $items->sum(fn ($item) => $item->price),
                'quantity' => $items->sum('quantity'),
            ]);
        });
    }
}
