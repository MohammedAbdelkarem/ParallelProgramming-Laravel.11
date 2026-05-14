<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('user_profiles')->insert(array(
            0 =>
            array(
                'user_id' => 2,
                'banned_until' => NULL,
                'created_at' => '2025-01-16 23:37:50',
                'updated_at' => '2025-01-16 23:37:50',
            ),
            1 =>
            array(
                'user_id' => 3,
                'banned_until' => NULL,
                'created_at' => '2025-01-16 23:38:13',
                'updated_at' => '2025-01-16 23:38:13',
            ),
        ));
    }
}
