<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class PaypalService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;
    protected string $accessToken;
    protected PendingRequest $client;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.sandbox')
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $this->accessToken = $this->generateAccessToken();

        // Cliente HTTP preconfigurado con token
        $this->client = Http::withToken($this->accessToken)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson();
    }

    protected function generateAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            throw new \Exception("Error al obtener el token de PayPal: " . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function sendPayout(string $email, float $amount, string $currency = 'USD', string $note = 'Payment from your app')
    {
        $itemId = uniqid("item_");
        $senderBatchId = uniqid("batch_");
        $response = $this->client->post('/v1/payments/payouts', [
            "sender_batch_header" => [
                "sender_batch_id" => $senderBatchId,
                "email_subject" => "Has recibido un pago a través de PayPal",
                "email_message" => "¡Hola!\n\nTe informamos que has recibido un pago mediante nuestra plataforma. Revisa tu cuenta de PayPal para más detalles.\n\nGracias por utilizar nuestros servicios.",
            ],
            "items" => [
                [
                    "recipient_type" => "EMAIL",
                    "amount" => [
                        "value" => number_format($amount, 2, '.', ''),
                        "currency" => $currency,
                    ],
                    "receiver" => $email,
                    "note" => $note,
                    "sender_item_id" => $itemId,
                ]
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception("Error en payout de PayPal: " . $response->body());
        }

        return [
            'paypal_response' => $response->json(),
            'item_id' => $itemId,
            'sender_batch_id' => $senderBatchId,
            'amount' => $amount,
            'currency' => $currency,
            'email' => $email,
            'note' => $note,
        ];
    }

    // Puedes agregar más métodos aquí reutilizando $this->client
    // Ej: consultar saldo, ver estado de payout, etc.


    public function getPayoutStatus(string $batchId)
    {
        $response = $this->client->get("/v1/payments/payouts/{$batchId}");

        if (!$response->successful()) {
            throw new \Exception("Error al consultar payout: " . $response->body());
        }

        return $response->json();
    }

}
