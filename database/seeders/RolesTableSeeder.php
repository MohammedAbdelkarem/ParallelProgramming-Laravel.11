<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Super Admin',
                'description' => 'Super Admin In The System.',
                'created_at' => '2025-01-12 10:02:54',
                'updated_at' => '2025-01-12 10:02:54',
                'created_by' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Admin',
                'description' => 'Admin in the system.',
                'created_at' => '2025-01-12 10:03:27',
                'updated_at' => '2025-01-12 10:03:27',
                'created_by' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'User',
                'description' => 'System User.',
                'created_at' => '2025-01-12 10:03:49',
                'updated_at' => '2025-01-12 10:03:49',
                'created_by' => NULL,
            ),
        ));
        
        
    }
}