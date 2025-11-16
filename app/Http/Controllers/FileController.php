<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\File;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function download(Request $request, string $id)
    {
        $token = $request->get('token');
        if ($token) {
            $user = User::whereJsonContains('downloadToken', $token)->first();
            if ($user) {
                $downloadToken = $user->downloadToken;
                $indice = array_search($token, $downloadToken);
                unset($downloadToken[$indice]);
                $user->downloadToken = $downloadToken;
                $user->save();

                $file = File::find($id);
                $path = $file->original_file;
                if (!Storage::disk('s3')->exists($path)) {
                    abort(404);
                }
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $downloadName = "$file->name.$ext";
                return Storage::disk('s3')->download($path, $downloadName);
            }
            return redirect('/')->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
        }

        if (auth()->user()->hasActivePlan()) {

            $plan = null;

            if (auth()->user()->currentPlan) {
                $plan = auth()->user()->currentPlan;
            } else {
                $plan = Order::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first()?->plan;
            }

            if($plan){
                if (auth()->user()->getFileCurrentMonthDownloads($id) < $plan->downloads) {
                    $file = File::find($id);

                    $path = $file->original_file;

                    if (!Storage::disk('s3')->exists($path)) {
                        abort(404);
                    }

                    $file->download_count = $file->download_count + 1;
                    $file->save();

                    $download = new Download();
                    $download->user_id = auth()->user()->id;
                    $download->file_id = $file->id;
                    $download->save();
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $downloadName = "$file->name.$ext";
                    return Storage::disk('s3')->download($path, $downloadName);
                }
                return redirect()->back()->with('error', 'Ha superados las descargas por mes permitida por su plan, considere mejorar su plan.');
            }
        }
        return redirect()->back()->with('error', 'Usted no tiene permisos para descargar el archivo seleccionado.');
    }

    public function play(string $collectionId, string $id)
    {
        $track = File::orderBy('created_at', 'desc')->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'url' => Storage::disk('s3')->url($f->url ?? $f->file), // adapta si ya guardas rutas absolutas
                'title' => $f->title ?? $f->name,
            ]);

        return response()->json($track);
    }

    public function pay(string $id)
    {
        $file = File::find($id);
        if (!$file) {
            return response()->json([
                'error' => 'El archivo seleccionado no es válido.'
            ], 422);
        }

        // Valida precio
        $price = (float) $file->price;
        if ($price <= 0) {
            return response()->json([
                'error' => 'El precio del archivo no es válido.'
            ], 422);
        }

        try {

            $order = new Order();
            $order->user_id = auth()->user()?->id;
            $order->file_id = $file->id;
            $order->amount = $file->price;
            $order->status = 'pending';
            $order->save();

            // Configura tu clave secreta (recomendado: en AppServiceProvider::boot)
            Stripe::setApiKey(config('services.stripe.secret_key'));

            // URL temporal al archivo
            $urlTemporal = Storage::disk('s3')->temporaryUrl($file->original_file, now()->addHour());

            // Monto en centavos
            $amountInCents = (int) round($price * 100);

            // Metadatos para rastrear compra
            $metadata = [
                'file_id' => (string) $file->id,
                'user_id' => auth()->check() ? (string) auth()->id() : null,
                'file_url' => (string) $urlTemporal,
                'order_id' => (string) $order->id,
            ];

            // Crea la sesión de Checkout
            $session = StripeSession::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => (string) $file->name,
                            ],
                            'unit_amount' => $amountInCents,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'success_url' => route('payment.ok2') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('file.pay', ['file' => $file->id]),

                // Si no manejas customers en Stripe, usa el email
                'customer_email' => optional(auth()->user())->email,

                // Metadatos en la Session (útil para búsqueda rápida)
                'metadata' => $metadata,

                // Metadatos en el PaymentIntent (bajan al cargo)
                'payment_intent_data' => [
                    'metadata' => $metadata,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => 'No se pudo iniciar el pago.',
            ], 500);
        }
    }
}
