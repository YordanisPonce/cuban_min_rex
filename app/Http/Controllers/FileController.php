<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Category;
use App\Models\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

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
        return view('payment.payment', [
            'file' => File::find($id),
            'categories' => Category::where('show_in_landing', true)->get()
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'file_id' => 'required|integer'
        ]);

        $file = File::find($request->file_id);
        if (!$file) {
            return response()->json([
                'error' => 'El arhivo seleccionado no es válido.'
            ], 422);
        }

        try {
            $session = auth()->user()->checkout(
                [
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $file->name,
                            ],
                            'unit_amount' => $file->price,
                        ],
                        'quantity' => 1,
                    ],
                    'mode' => 'payment',
                    'success_url' => route('payment.ok'),
                    'cancel_url' => route('file.pay', ['file' => $file->id]),
                    'metadata' => [
                        'file_id' => $file->id,
                        'user_id' => $request->user()->id,
                        'file_url' => storage_path('app/public/' . $file->file)
                    ],
                ]
            );

            $billing = $request->user()->billing;
            if (!$billing) {
                $billing = new Billing();
                $billing->user_id = $request->user()->id;
            }
            $billing->phone = $request->phone;
            $billing->address = $request->address;
            $billing->postal = $request->postal;
            $billing->country = $request->country;
            $billing->save();

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            // ✅ Devolvemos JSON para que el front no rompa
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
