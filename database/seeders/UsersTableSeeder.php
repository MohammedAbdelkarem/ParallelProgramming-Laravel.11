<?php

namespace Database\Seeders;

use App\Models\Administration\Profile\AdminProfile;
use App\Models\User;
use App\Models\Users\Product\Product;
use App\Models\Users\Profile\Address;
use App\Models\Users\Profile\LoginHistory;
use App\Models\Users\Profile\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->delete();

        DB::table('users')->insert(array(
            0 =>
            array(
                'id' => 1,
                'role_id' => 1,
                'name' => 'Template Super Admin',
                'phone_number' => '+963900000000',
                'email' => 'superAdmin@email.com',
                'birth_date' => null,
                'is_male' => true,
                'language' => 'en',
                'active_notifications' => 1,
                'deactive_at' => NULL,
                'account_verified_at' => '2025-01-12 10:09:07',
                'deleted_at' => NULL,
                'created_at' => '2025-01-12 10:09:07',
                'updated_at' => '2025-01-12 10:09:07',
            ),
            1 =>
            array(
                'id' => 2,
                'role_id' => 2,
                'name' => 'Template Admin',
                'phone_number' => '+963900000000',
                'email' => 'admin@email.com',
                'birth_date' => null,
                'is_male' => true,
                'language' => 'en',
                'active_notifications' => 1,
                'deactive_at' => NULL,
                'account_verified_at' => '2025-01-12 10:09:07',
                'deleted_at' => NULL,
                'created_at' => '2025-01-12 10:09:07',
                'updated_at' => '2025-01-12 10:09:07',
            ),
        ));

        User::find(1)->wallet()->create([]);
        
        LoginHistory::factory()->count(7)->create([
            "user_id" => 1
        ]);

        LoginHistory::factory()->count(3)->create([
            "user_id" => 2
        ]);
    }
}