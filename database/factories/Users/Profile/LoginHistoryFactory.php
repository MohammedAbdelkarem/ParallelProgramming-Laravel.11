<?php

namespace Database\Factories\Users\Profile;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\LoginHistory>
 */
class LoginHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "ip_address"        => $this->faker->ipv4(),
            "country_code"      => 'SY',
            "device_name"       => 'Nokia',
            "country"           => 'Syria',
            "city"              => $this->faker->randomElement(['Homs', 'Hama', 'Damascus']),
            "created_at"        => $this->faker->dateTimeBetween('-1 years'),
        ];
    }
}
