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
                $this->warn("Buscando registros de suscripciones ....");
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
                if(Carbon::parse($order->created_at)->greaterThan(Carbon::now()->subHours(72))){
                    $newOrder = new Order();
                    $newOrder->user_id = $user->id;
                    $newOrder->plan_id = $plan->id;
                    $newOrder->amount = $plan?->price;
                    $newOrder->status = 'paid';
                    $newOrder->paid_at = Carbon::now();
                    $newOrder->customer_email = $user->email;
                    $newOrder->save();
                }
                $this->info("Suscripción Actualizada.");
                return;
            } else {
                $this->info("No se pudo acceder a los datos de $email");
            }
        } catch (\Throwable $th) {
            $this->error('Error al ejecutar comando: '. $th->getMessage());
        }
    }
}
