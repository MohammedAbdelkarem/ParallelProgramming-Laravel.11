<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(array $attributes = []): array
    {
        $created_at = fake()->dateTimeBetween('-10 month');

        // dd($attributes);

        return [
            'role_id'           => array_key_exists('role_id', $attributes) ? $attributes['role_id'] : 3,
            'name'              => fake()->firstName() . ' ' . fake()->lastName(),
            'phone_number'      => "+9639" . random_int(10000000, 99999999),
            'email'             => fake()->unique()->safeEmail(),
            'birth_date'        => fake()->dateTimeBetween('-40 years', '-18 years'),
            'is_male'           => fake()->randomElement([false, true]),
            'language'          => fake()->randomElement(config('_custom.accepted_languages')),
            'active_notifications'  => fake()->randomElement([0, 1]),
            'account_verified_at'   => $created_at,
            'avatar'                => fake()->imageUrl(),
            'created_at'            => $created_at,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}