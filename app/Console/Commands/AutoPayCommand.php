<?php

namespace App\Console\Commands;

use App\Models\Download;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\AdminLiquidationNotification;
use App\Notifications\LiquidationNotification;
use App\Notifications\NotPaypalEmail;
use App\Notifications\ServerError;
use App\Services\PaypalService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoPayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-pay-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Pay Remixers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $remixers = User::whereNot('role', 'user')->get();

        $allPaids = [];
        
        foreach ($remixers as $record) {
            if (!$record->paypal_email) {
                $record->notify(new NotPaypalEmail());
            } else {
                $paypal = new PaypalService();
                try {
                    
                    $id = $record->id;

                    if($record->pendingSaleLiquidation() !== 0){
                        $response = $paypal->sendPayout($record->paypal_email,$record->pendingSaleLiquidation(),'USD','Liquidación por ventas del mes '.Carbon::now()->month.' del año '.Carbon::now()->year.'.');

                        $payment = new Payment();
                        $payment->user_id = $record->id;
                        $payment->paypal_response = $response['paypal_response'];
                        $payment->item_id = $response['item_id'];
                        $payment->sender_batch_id = $response['sender_batch_id'];
                        $payment->amount = $response['amount'];
                        $payment->currency = $response['currency'];
                        $payment->email = $response['email'];
                        $payment->note = $response['note'];
                        $payment->save();

                        Sale::whereHas('file', function ($query) use ($id) {
                            $query->where('user_id', $id);
                        })->update(['status' => 'paid']);

                        $record->notify(new LiquidationNotification($payment->amount,$payment->note));

                        $paid = [
                            'user' => $record->name,
                            'email' => $record->paypal_email,
                            'amount' => $payment->amount,
                            'razon' => $payment->note
                        ];
                        array_push($allPaids, $paid);
                    }
                    
                    if($record->pendingSubscriptionLiquidation() !== 0){
                        $res = $paypal->sendPayout($record->paypal_email,$record->pendingSubscriptionLiquidation(),'USD','Liquidación por subscripciones del mes '.Carbon::now()->month.' del año '.Carbon::now()->year.'.');

                        $payment2 = new Payment();
                        $payment2->user_id = $record->id;
                        $payment2->paypal_response = $res['paypal_response'];
                        $payment2->item_id = $res['item_id'];
                        $payment2->sender_batch_id = $res['sender_batch_id'];
                        $payment2->amount = $res['amount'];
                        $payment2->currency = $res['currency'];
                        $payment2->email = $res['email'];
                        $payment2->note = $res['note'];
                        $payment2->save();

                        Download::whereHas('file', function($query) use ($id) {
                            $query->where('user_id', $id);
                        })
                        ->where('liquidated', false)
                        ->update(['liquidated' => true]);

                        $record->notify(new LiquidationNotification($payment2->amount,$payment2->note));

                        $paid = [
                            'user' => $record->name,
                            'email' => $record->paypal_email,
                            'amount' => $payment2->amount,
                            'razon' => $payment2->note
                        ];
                        array_push($allPaids, $paid);
                    }

                } catch (\Throwable $th) {
                    $admins = User::where('role', 'admin')->get();
            
                    foreach ($admins as $admin) {
                        $admin->notify(new ServerError('Autopago al Remixer '.$record->name, $th->getMessage()));
                    }
                }
            }
        }

        $admins = User::where('role', 'admin')->get();
            
        foreach ($admins as $admin) {
            $admin->notify(new AdminLiquidationNotification($allPaids));
        }
    }
}
