<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BackfillRenewOrders extends Command
{
    protected $signature = 'orders:backfill-renews
        {--dry : No guarda, solo muestra lo que haría}
        {--user_id= : Solo un usuario}
        {--max=5000 : Máximo de órdenes a crear}
        {--until= : Fecha YYYY-MM-DD hasta donde backfillear (default: hoy)}
    ';

    protected $description = 'Genera órdenes faltantes de renew (suscripciones) clonando la orden anterior.';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry');
        $onlyUserId = $this->option('user_id');
        $maxCreate = (int) $this->option('max');
        $untilOpt = $this->option('until');

        $until = $untilOpt ? Carbon::parse($untilOpt)->endOfDay() : now();

        $created = 0;

        $usersQ = User::query();
        if ($onlyUserId) $usersQ->where('id', $onlyUserId);

        $usersQ->select(['id', 'plan_expires_at'])->chunkById(200, function ($users) use (&$created, $dry, $maxCreate, $until) {
            foreach ($users as $user) {
                if ($created >= $maxCreate) {
                    $this->warn("Límite alcanzado ({$maxCreate}).");
                    return;
                }

                // Última orden de suscripción pagada del usuario
                $last = Order::query()
                    ->with('plan:id,duration_months')
                    ->where('user_id', $user->id)
                    ->where('status', 'paid')
                    ->whereNotNull('plan_id')
                    ->orderByDesc('paid_at')
                    ->orderByDesc('id')
                    ->first();

                if (!$last || !$last->plan || (int)$last->plan->duration_months <= 0) {
                    continue;
                }

                // Normaliza paid_at / expires_at si faltan (por si hay datos viejos)
                $paidAt = $last->paid_at ? Carbon::parse($last->paid_at) : Carbon::parse($last->created_at);
                $expiresAt = $last->expires_at
                    ? Carbon::parse($last->expires_at)
                    : $paidAt->copy()->addMonthsNoOverflow((int)$last->plan->duration_months);

                // Hasta dónde backfillear: el menor entre (user.plan_expires_at) y (--until/hoy)
                $userCap = $user->plan_expires_at ? Carbon::parse($user->plan_expires_at)->endOfDay() : null;
                $cap = $userCap ? ($userCap->lt($until) ? $userCap : $until) : $until;

                // Si ya expiró antes del último expires, no hay nada que hacer
                if ($cap->lte($expiresAt)) {
                    continue;
                }

                // Generar en cadena: nextPaid = prevExpires
                $cursorPaid = $expiresAt->copy();

                while ($cursorPaid->lt($cap) && $created < $maxCreate) {
                    $nextExpires = $cursorPaid->copy()->addMonthsNoOverflow((int)$last->plan->duration_months);

                    // DEDUPE: si ya existe una orden para ese rango (paid_at/expires_at), no creamos
                    $exists = Order::query()
                        ->where('user_id', $user->id)
                        ->where('plan_id', $last->plan_id)
                        ->where('status', 'paid')
                        ->whereNotNull('paid_at')
                        ->whereNotNull('expires_at')
                        ->where('paid_at', $cursorPaid->toDateTimeString())
                        ->where('expires_at', $nextExpires->toDateTimeString())
                        ->exists();

                    if (!$exists) {
                        $marker = 'backfill:' . $last->id . ':' . $cursorPaid->format('YmdHis');

                        // Segundo dedupe por marker si ya existe (por re-ejecución)
                        $existsMarker = Order::query()
                            ->where('stripe_session_id', $marker)
                            ->exists();

                        if (!$existsMarker) {
                            $payload = [
                                'user_id' => $user->id,
                                'plan_id' => $last->plan_id,
                                'file_id' => null,
                                'stripe_session_id' => $marker,
                                'stripe_payment_intent' => $last->stripe_payment_intent, // opcional, puedes poner null
                                'amount' => $last->amount,
                                'status' => 'paid',
                                'paid_at' => $cursorPaid->toDateTimeString(),
                                'expires_at' => $nextExpires->toDateTimeString(),
                                'settled_at' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $created++;

                            if (!$dry) {
                                DB::table('orders')->insert($payload);
                            }

                            $this->line("User {$user->id}: +Order {$cursorPaid->toDateString()} -> {$nextExpires->toDateString()} ({$marker})");
                        }
                    }

                    // avanzar cadena
                    $cursorPaid = $nextExpires;
                }
            }
        });

        $this->info(($dry ? '[DRY] ' : '') . "Órdenes creadas: {$created}");

        return self::SUCCESS;
    }
}
