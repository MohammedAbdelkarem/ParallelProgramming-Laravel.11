<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\ActorTypeEnum;
use Illuminate\Support\Facades\DB;

class PrivacyPolicySeeder extends Seeder
{
    public function run(): void
    {
        foreach (ActorTypeEnum::cases() as $actor) {
            DB::table('privacy_policies')->insert([
                'content' => "Default privacy policy content for {$actor->value}.",
                'actor_type'   => $actor->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
