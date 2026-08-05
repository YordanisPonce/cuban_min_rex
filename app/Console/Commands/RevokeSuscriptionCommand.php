<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RevokeSuscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:revoke-suscription  {--email=} {--with-downloads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke a suscruption';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $email = $this->option('email') ?? null;
            $withDownloads = (bool) $this->option('with-downloads');
            if (!$email) {
                $this->error('Email requerido');
            }
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->info("Accediendo a los datos de $user->name");
                $this->info("Revocando suscripción de $user->name.....");
                $user->current_plan_id = null;
                $user->plan_start_at = null;
                $user->plan_expires_at = null;
                $user->save();
                $this->info("Suscripción Revocada.");
            } else {
                $this->info("No se pudo acceder a los datos de $email");
            }
        } catch (\Throwable $th) {
            $this->error('Error al ejecutar comando: '. $th->getMessage());
        }
    }
}
