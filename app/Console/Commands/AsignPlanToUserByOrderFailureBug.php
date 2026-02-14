<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class AsignPlanToUserByOrderFailureBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:asign-plan-to-user-by-order-failure-bug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asign plan to perezquesada1986@gmail.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'perezquesada1986@gmail.com')->first();

        if($user){
            $order = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
            $plan = Plan::find($order->plan_id);

            if ($order && $plan) {
                $order->status = 'paid'; 
                $order->paid_at = now(); 
                $order->expires_at = now()->addMonths($plan->duration_months); 
                $order->save(); 
                
                $user->current_plan_id = $plan->id; 
                $user->plan_expires_at = now()->addMonths($plan->duration_months); 
                $user->save(); 
                
                $subscription = new Subscription(); 
                $subscription->user_id = $user->id; 
                $subscription->ends_at = now()->addMonths($plan->duration_months); 
                $subscription->save(); 
            }
        }
    }
}
