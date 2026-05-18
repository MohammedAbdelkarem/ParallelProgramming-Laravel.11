<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class GenerateReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reports and dispatch report jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(OrderService::class)->generate();

        $this->info('Report generation triggered.');
    }
}
