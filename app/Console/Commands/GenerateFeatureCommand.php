<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateFeatureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:feature {path} {--policy} {--factory} {--seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');
        $policy = $this->option('policy');
        $factory = $this->option('factory');
        $seeder = $this->option('seeder');

        Artisan::call("make:model $path -m");
        $this->info("Model: app/models/$path created");
        $this->info("Migration: created");
        $this->info("");

        Artisan::call("make:controller {$path}Controller --api");
        $this->info("Controller: app/controllers/{$path}Controller created");
        $this->info("");

        Artisan::call("make:service {$path}Service");
        $this->info("Service: app/services/{$path}Service created");
        $this->info("");

        Artisan::call("make:request {$path}Request");
        $this->info("Request: app/requests/{$path}Request created");
        $this->info("");

        Artisan::call("make:resource {$path}Resource");
        $this->info("Resource: app/resources/{$path}Resource created");
        $this->info("");

        if ($policy) {
            Artisan::call("make:policy {$path}Policiy");
            $this->info("Policy: app/policies/{$path}Policy created");
            $this->info("");
        }

        if ($factory) {
            Artisan::call("make:factory {$path}Factory");
            $this->info("Factory: app/factories/{$path}Factory created");
            $this->info("");
        }

        if ($seeder) {
            Artisan::call("make:seeder {$path}Seeder");
            $this->info("Seeder: app/seeders/{$path}Seeder created");
            $this->info("");
        }
    }
}