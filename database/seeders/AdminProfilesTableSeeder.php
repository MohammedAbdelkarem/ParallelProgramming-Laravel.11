<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminProfilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_profiles')->insert(array(
            0 =>
            array(
                'user_id'    => 1,
                'password'   => Hash::make(123456789),
                'created_by' => NULL,
                'created_at' => '2025-01-12 10:19:11',
                'updated_at' => '2025-01-12 10:19:11',
            ),
            1 =>
            array(
                'user_id'    => 2,
                'password'   => Hash::make(123456789),
                'created_by' => NULL,
                'created_at' => '2025-01-12 10:19:11',
                'updated_at' => '2025-01-12 10:19:11',
            ),
        ));
    }
}