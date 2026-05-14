<?php

namespace Database\Factories\System\Notification;

use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\Info\FAQ>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        $title = [
            "message"       => $this->faker->unique()->sentence(4),
            "attributes"    => []
        ];

        $body = [
            "message"       => $this->faker->unique()->text(255),
            "attributes"    => []
        ];

        $data = [
            "title" => json_encode($title),
            "body" => json_encode($body),
            "created_by" => 1,
            "type" => NotificationTypes::PUBLIC->value,
            "page" => NotificationScreens::HOME->value,
            "clickable" => false,
            "requested_id" => null,
            "is_public" => true,
        ];
        return $data;
    }
}
