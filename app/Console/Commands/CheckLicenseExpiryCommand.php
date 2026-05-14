<?php

namespace App\Console\Commands;

use App\Services\Logisti\LogistiService;
use Illuminate\Console\Command;

class CheckLicenseExpiryCommand extends Command
{
    protected $signature = 'logisti:check-expiry';

    protected $description = 'Check license expiry for all vendors and send notifications';

    public function handle(LogistiService $service): void
    {
        $service->checkAll();

        $summary = $service->expiringSoon(30);

        $this->info("Logisti license check complete.");
        $this->table(
            ['License Type', 'Expiring in 30 days'],
            [
                ['Driver Licenses',    $summary['driver_licenses']],
                ['Transport Licenses', $summary['transport_licenses']],
                ['Clearance Licenses', $summary['clearance_licenses']],
            ]
        );
    }
}
