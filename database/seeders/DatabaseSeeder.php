<?php

namespace Database\Seeders;

use App\Models\BmiClassification;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        $this->call(DriverCompanySeeder::class);
        $this->call(CustomClearenceCompanySeeder::class);
        $this->call(DriverSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PrivacyPolicySeeder::class);
        $this->call(ZatcaCredentialsSeeder::class);



    }
}