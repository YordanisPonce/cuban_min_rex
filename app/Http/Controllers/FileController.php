<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download(string $id)
    {

        $file = File::find($id);

        $path = storage_path('app/public/' . $file->file);

        if (!file_exists($path)) {
            abort(404);
        }

        return Response::download($path);
    }

    public function play(string $collectionId, string $id)
    {
        $file = File::find($id);
        return response()->stream(function () use ($file) {
            echo file_get_contents(storage_path('app/public/' . $file->file));
        }, 200, [
            'Content-Type' => 'audio/mpeg',
            'Content-Disposition' => 'inline; filename="' . $file->name . '"',
        ]);
    }

    public function pay(string $id)
    {
        $file = File::find($id);
        if (!$file) {
            return response()->json([
                'error' => 'El arhivo seleccionado no es válido.'
            ], 422);
        }

        try {
            $urlTemporal = Storage::disk('public')->temporaryUrl($file->file, now()->addHour());

            $session = auth()->user()->checkout([
                'payment_method_types' => ['card'],
                'line_items' => 
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $file->name,
                            ],
                            'unit_amount' => intval($file->price)*100,
                        ],
                        'quantity' => 1,
                    ],
                'mode' => 'payment',
                'success_url' => route('payment.ok') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('file.pay', ['file' => $file->id]),
                'metadata' => [
                    'file_id' => $file->id,
                    'user_id' => auth()->id(),
                    'file_url' => $urlTemporal,
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // ✅ Devolvemos JSON para que el front no rompa
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
