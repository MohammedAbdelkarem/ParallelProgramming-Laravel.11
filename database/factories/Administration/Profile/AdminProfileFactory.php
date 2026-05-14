<?php

namespace Database\Factories\Administration\Profile;

use App\Models\Administration\Profile\AdminProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminProfileFactory extends Factory
{
    protected $model = AdminProfile::class;

    public function definition(): array
    {
        return [
            "password"   => Hash::make(123456789),
            "created_by" => 1,
        ];
    }
}
