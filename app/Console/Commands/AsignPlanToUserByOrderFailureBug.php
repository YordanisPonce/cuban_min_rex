<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;

class AsignPlanToUserByOrderFailureBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-unlimited-downloads-to-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unlimited downloads to yoniseljimenez@gmail.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'yoniseljimenez@gmail.com')->first();

        if($user){
            $this->info('Estableciendo un Limite de Descargas a yoniseljimenez@gmail.com');
            $user->update([
                'plan_start_at' => Carbon::now(),
            ]);
            $downloadLeft = $user->currentPlan->downloads - $user->get_current_plan_consume_downloads();
            $this->info('Descargas restantes: '. $downloadLeft);
            $timeLeft = Carbon::parse($user->plan_expires_at)->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_TO_NOW);
            $this->info('Plan vence: '. $timeLeft);
        }
    }
}
