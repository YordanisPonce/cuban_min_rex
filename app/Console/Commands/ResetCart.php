<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-cart {--email= : usuario del carrito}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a Cart';

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
                $this->info("Accediendo al carrito de $user->name");
                $cart = $user->cart;
                if ($cart) {
                    $this->info('Reiniciando Carrito');
                    $cart->cart_items()->delete();
                    $this->info('Carrito reiniciado correctamente');
                }
            } else {
                $this->info("No se pudo acceder al carrito de $email");
            }
        } catch (\Throwable $th) {
            $this->error('Error al ejecutar comando: '. $th->getMessage());
        }
    }
}
