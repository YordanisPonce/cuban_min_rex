<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateSuscriptionsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscription:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating User Suscription Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating Suscription Data for current users....');
        $this->info('Getting user with an active plan....');
        $actives = [];
        foreach (User::all() as $u) {
            if ($u->hasActivePlan()) {
                array_push($actives, $u);
            }
        }
        $this->info(count($actives) . " active(s) user(s) suscription(s) finded.");
        $ss = 0;
        $ff = 0;
        foreach ($actives as $u) {
            try {
                $this->info("Getting Suscription Data for $u->name...");
                $susc = $u->getActivePlan();
                if ($susc) {
                    $this->info("$u->name with $susc->name active suscription with $susc->downloads max downloads, expire at $u->plan_expires_at.");
                    $this->info("Updating Suscription data for $u->name...");
                    Subscription::updateOrCreate(
                        ['user_id' => $u->id],
                        ['plan_name' => $susc->name, 'plan_price' => $susc->price, 'max_downloads' => $susc->downloads, 'ends_at' => $u->plan_expires_at]
                    );
                    $newSusc = Subscription::where('user_id', $u->id)->orderBy('updated_at', 'desc')->first();
                    $this->info("$u->name suscription updated with $newSusc->plan_name active suscription with $newSusc->max_downloads max downloads, expire at $newSusc->ends_at.");
                    $ss++;
                } else {
                    $this->info("Not found Suscription Data for $u->name. Skipping.");
                }
            } catch (\Throwable $th) {
                $ff++;
            }
        }
        $this->info("Proccess Finished. Updated $ss suscription(s) data succefull. $ff failed.");
    }
}
