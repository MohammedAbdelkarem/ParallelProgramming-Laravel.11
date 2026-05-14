<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\System\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        DB::table('system_settings')->delete();
        
        DB::table('system_settings')->insert([
            [
                'id'    => 1,
                'key'   => 'price_for_kilometer',
                'value' => '2',
            ],
            [
                'id'    => 2,
                'key'   => 'order_circle_radius_kilometers',
                'value' => '20',
            ],
            [
                'id'    => 3,
                'key'   => 'platform_ratio_percentage',
                'value' => '10',
            ],
            [
                'id'    => 4,
                'key'   => 'tax_value_percentage',
                'value' => '15',
            ],
        ]);

    }
}
