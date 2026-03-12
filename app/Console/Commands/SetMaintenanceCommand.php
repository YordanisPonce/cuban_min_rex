<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-maintenance {status : The status of the maintenance mode (on or off)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active or deactivate the maintenance mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->argument('status');

        if (!is_string($status)) {
            $this->error('Invalid status. Please provide "on" or "off".');
            return 1;
        }

        if (!in_array($status, ['on', 'off'])) {
            $this->error('Invalid status. Please provide "on" or "off".');
            return 1;
        }

        $maintenanceStatus = $status === 'on';

        // Update the maintenance status in the database
        $setting = \App\Models\Setting::first();
        if ($setting) {
            $setting->maintenance = $maintenanceStatus;
            $setting->save();

            $this->info('Maintenance mode has been set to: ' . ($maintenanceStatus ? 'on' : 'off'));
            return 0;
        } else {
            $this->error('Settings record not found.');
            return 1;
        }
    }
}
