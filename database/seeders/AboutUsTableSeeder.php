<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AboutUsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('about_us')->delete();

        \DB::table('about_us')->insert(array(
            0 =>
            array(
                'id' => 2,
                'lang' => 'ar',
                'text' => '<h1>حول</h1>
<h4>Name</h4>
<p>نص طويل</p>',
                'update_by' => 1,
                'created_at' => '2024-02-02 02:28:15',
                'updated_at' => '2024-02-02 02:28:15',
            ),
        ));
    }
}