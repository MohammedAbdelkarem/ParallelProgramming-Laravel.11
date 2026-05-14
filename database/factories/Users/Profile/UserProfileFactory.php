<?php

namespace Database\Factories\Users\Profile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Users\Profile\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "banned_until" => $this->faker->randomElement([null,null,null,Carbon::now()->addYear()])
        ];
    }
}
