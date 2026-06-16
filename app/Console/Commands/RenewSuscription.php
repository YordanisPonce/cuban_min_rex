<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RenewSuscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:renew-subscription {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew the User Subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $email = $this->option('email') ?? null;
            if (!$email) {
                $this->error('Email requerido');
            }
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->info("Accediendo a los datos de $user->name");
                $planId = $user->current_plan_id;
                if(!$planId){
                    $this->warn("No se encontro plan reciente, buscar en registros ....");
                    $order = Order::where('user_id', $user->id)->whereNotNull('plan_id')->where('status', 'paid')->orderBy('created_at', 'desc')->first();
                    if(!$order){
                        $this->warn("No se encontraron suscripciones anteriores en los registros, no se puede renovar. Saltando.");
                        return;
                    }
                    $this->info("Última suscripción encontrada, fecha de suscripción $order->created_at. Recopilando datos del plan.....");
                    $plan = Plan::find($order->plan_id);
                    if(!$plan){
                        $this->warn("No se encontraron datos del plan. Deteniendo Proceso.");
                        return;
                    }
                    $this->info("Actualizando suscripción de $user->name.....");
                    $user->current_plan_id = $plan->id;
                    $user->plan_start_at = Carbon::now();
                    $user->plan_expires_at = Carbon::now()->addMonths($plan->duration_months);
                    $user->save();
                    Subscription::updateOrCreate(
                        ['user_id' => $user->id],
                        ['ends_at' => $user->plan_expires_at]
                    );
                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->plan_id = $plan->id;
                    $order->amount = $plan?->price;
                    $order->status = 'paid';
                    $order->paid_at = Carbon::now();
                    $order->customer_email = $user->email;
                    $order->save();
                    $this->info("Suscripción Actualizada.");
                    return;
                }
                $this->info("Accediendo a los datos del plan...");
                $plan = Plan::find($planId);
                if(!$plan){
                    $this->warn("No se encontraron datos del plan. Deteniendo Proceso.");
                    return;
                }
                $this->info("Actualizando suscripción de $user->name...");
                $user->current_plan_id = $plan->id;
                $user->plan_start_at = Carbon::now();
                $user->plan_expires_at = Carbon::now()->addMonths($plan->duration_months);
                $user->save();
                Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    ['ends_at' => $user->plan_expires_at]
                );
                $order = new Order();
                $order->user_id = $user->id;
                $order->plan_id = $plan->id;
                $order->amount = $plan?->price;
                $order->status = 'paid';
                $order->paid_at = Carbon::now();
                $order->customer_email = $user->email;
                $order->save();
            } else {
                $this->info("No se pudo acceder a los datos de $email");
            }
        } catch (\Throwable $th) {
            $this->error('Error al ejecutar comando: '. $th->getMessage());
        }
    }
}
