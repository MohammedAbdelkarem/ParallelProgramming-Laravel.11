<?php

namespace Database\Seeders;

use App\Models\BmiClassification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);

        $this->call(UsersTableSeeder::class);
        $this->call(AdminProfilesTableSeeder::class);
        // $this->call(UserProfilesTableSeeder::class);

        // $this->call(SystemSettingsTableSeeder::class);

        // $this->call(NotificationsTableSeeder::class);

        $this->call(SettingSeeder::class);
        $this->call(PrivacyPolicySeeder::class);

        Product::factory()->count(50)->create();
        Order::factory()->count(20)->create();





    }
}