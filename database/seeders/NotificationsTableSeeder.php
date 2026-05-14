<?php

namespace Database\Seeders;

use App\Enums\NotificationScreens;
use App\Models\System\Notification\Notification;
use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Notification::factory()->count(60)->create();
    }
}
