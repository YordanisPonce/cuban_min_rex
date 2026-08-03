<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\User;
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

        $this->client = Http::withToken($this->accessToken)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Genera un token de acceso para la API de PayPal.
     * @return string
     * @throws \Exception
     */
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

    /**
     * Normaliza un monto a formato de cadena con dos decimales.
     * @param float $amount
     * @return string
     */
    protected function normalizeAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    protected function resolvePaypalDescription(Plan $plan): string
    {
        $description = trim(strip_tags($plan->description ?? ''));

        if ($description === '') {
            return 'Plan de suscripción ' . ($plan->name ?: 'de la plataforma');
        }

        return $description;
    }

    /**
     * Crea un producto en PayPal para un plan dado.
     * @param Plan $plan
     * @return string
     */
    protected function createProduct(Plan $plan): string
    {
        $response = $this->client->post('/v1/catalogs/products', [
            'name' => $plan->name,
            'description' => $this->resolvePaypalDescription($plan),
            'type' => 'SERVICE',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al crear el producto de PayPal: ' . $response->body());
        }

        return $response->json()['id'];
    }

    /**
     * Sincroniza un plan con PayPal.
     * @param Plan $plan
     * @return array
     */
    public function syncPaypalPlan(Plan $plan): array
    {
        $productId = $this->createProduct($plan);

        $response = $this->client->post('/v1/billing/plans', [
            'product_id' => $productId,
            'name' => $plan->name,
            'description' => $this->resolvePaypalDescription($plan),
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => 'MONTH',
                        'interval_count' => max(1, (int) ($plan->duration_months ?: 1)),
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $this->normalizeAmount((float) $plan->price),
                            'currency_code' => 'USD',
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'payment_failure_threshold' => 3,
            ],
            'quantity_supported' => false,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al crear el plan de PayPal: ' . $response->body());
        }

        $payload = $response->json();
        $plan->forceFill([
            'paypal_id' => $payload['id'] ?? null,
        ])->save();

        return $payload;
    }

    /**
     * Crea una orden de compra en PayPal para un plan y un pedido dados.
     * @param Plan $plan
     * @param Order $order
     * @param string|null $returnUrl
     * @param string|null $cancelUrl
     * @return array
     */
    public function createOrderForPurchase(Plan $plan, Order $order, ?string $returnUrl = null, ?string $cancelUrl = null): array
    {
        $returnUrl = $returnUrl ?: route('payment.ok');
        $cancelUrl = $cancelUrl ?: route('payment.form', ['plan' => $plan->id]);

        $response = $this->client->post('/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $order->id,
                'description' => $plan->name,
                'custom_id' => (string) $order->id,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $this->normalizeAmount((float) $plan->price),
                ],
            ]],
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al crear la orden de compra en PayPal: ' . $response->body());
        }

        $payload = $response->json();

        return [
            'paypal_order_id' => $payload['id'] ?? null,
            'status' => $payload['status'] ?? null,
            'approval_url' => $this->extractApprovalLink($payload),
            'response' => $payload,
        ];
    }

    /**
     * Crea una orden de carrito en PayPal para un pedido dado.
     * @param Order $order
     * @param string|null $returnUrl
     * @param string|null $cancelUrl
     * @return array
     */
    public function createCartOrder(Order $order, ?string $returnUrl = null, ?string $cancelUrl = null): array
    {
        $order->loadMissing('order_items.file', 'order_items.playlist', 'order_items.playlistItem');

        $items = [];
        $total = 0.0;

        foreach ($order->order_items as $orderItem) {
            $price = $this->resolveCartItemPrice($orderItem);
            $name = $this->resolveCartItemLabel($orderItem);
            $items[] = [
                'name' => $name,
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' => $this->normalizeAmount($price),
                ],
                'quantity' => 1,
                'category' => 'DIGITAL_GOODS',
            ];
            $total += $price;
        }

        if ($items === []) {
            throw new \Exception('La orden de carrito no tiene elementos para pagar.');
        }

        $returnUrl = $returnUrl ?: route('payment.ok2');
        $cancelUrl = $cancelUrl ?: route('payment.ko');

        $response = $this->client->post('/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $order->id,
                'description' => 'Compra de varios elementos del carrito',
                'custom_id' => (string) $order->id,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $this->normalizeAmount($total),
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => 'USD',
                            'value' => $this->normalizeAmount($total),
                        ],
                    ],
                ],
                'items' => $items,
            ]],
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al crear la orden de carrito en PayPal: ' . $response->body());
        }

        $payload = $response->json();

        return [
            'paypal_order_id' => $payload['id'] ?? null,
            'status' => $payload['status'] ?? null,
            'approval_url' => $this->extractApprovalLink($payload),
            'response' => $payload,
        ];
    }

    /**
     * Captura una orden de compra en PayPal.
     * @param string $orderId
     * @return array
     */
    public function captureOrder(string $orderId): array
    {
        $response = Http::withToken($this->accessToken)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post("/v2/checkout/orders/{$orderId}/capture", [
                'payment_source' => new \stdClass(),
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error al capturar la orden de PayPal: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Obtiene los detalles de una orden de compra en PayPal.
     * @param string $orderId
     * @return array
     */
    public function getOrder(string $orderId): array
    {
        $response = $this->client->get("/v2/checkout/orders/{$orderId}");

        if (!$response->successful()) {
            throw new \Exception('Error al consultar la orden de PayPal: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Crea una suscripción en PayPal para un plan y un usuario dados.
     * @param Plan $plan
     * @param User $user
     * @param Order $order
     * @param string $returnUrl
     * @param string $cancelUrl
     * @return array
     */
    public function createSubscription(Plan $plan, User $user, Order $order, string $returnUrl, string $cancelUrl): array
    {
        if (empty($plan->paypal_id)) {
            $this->syncPaypalPlan($plan);
        }

        $response = $this->client->post('/v1/billing/subscriptions', [
            'plan_id' => $plan->paypal_id,
            'subscriber' => [
                'email_address' => $user->email,
            ],
            'application_context' => [
                'brand_name' => config('app.name'),
                'locale' => 'es-ES',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'custom_id' => (string) $order->id,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al crear la suscripción en PayPal: ' . $response->body());
        }

        $payload = $response->json();

        return [
            'subscription_id' => $payload['id'] ?? null,
            'status' => $payload['status'] ?? null,
            'approval_url' => $this->extractApprovalLink($payload),
            'response' => $payload,
        ];
    }

    /**
     * Obtiene los detalles de una suscripción en PayPal.
     * @param string $subscriptionId
     * @return array
     */
    public function getSubscription(string $subscriptionId): array
    {
        $response = $this->client->get("/v1/billing/subscriptions/{$subscriptionId}");

        if (!$response->successful()) {
            throw new \Exception('Error al consultar la suscripción en PayPal: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Cancela una suscripción en PayPal.
     * @param string $subscriptionId
     * @param string $reason
     * @return array
     */
    public function cancelSubscription(string $subscriptionId, string $reason = 'User canceled'): array
    {
        $response = $this->client->post("/v1/billing/subscriptions/{$subscriptionId}/cancel", [
            'reason' => $reason,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al cancelar la suscripción en PayPal: ' . $response->body());
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            return [
                'subscription_id' => $subscriptionId,
                'status' => 'CANCELLED',
                'reason' => $reason,
                'response' => [],
            ];
        }

        return [
            'subscription_id' => $payload['id'] ?? $subscriptionId,
            'status' => $payload['status'] ?? 'CANCELLED',
            'reason' => $reason,
            'response' => $payload,
        ];
    }

    /**
     * Envía un pago (payout) a un usuario a través de PayPal.
     * @param string $email
     * @param float $amount
     * @param string $currency
     * @param string $note
     * @return array
     */
    public function sendPayout(string $email, float $amount, string $currency = 'USD', string $note = 'Payment from your app')
    {
        $itemId = uniqid('item_');
        $senderBatchId = uniqid('batch_');
        $response = $this->client->post('/v1/payments/payouts', [
            'sender_batch_header' => [
                'sender_batch_id' => $senderBatchId,
                'email_subject' => 'Has recibido un pago a través de PayPal',
                'email_message' => "¡Hola!\n\nTe informamos que has recibido un pago mediante nuestra plataforma. Revisa tu cuenta de PayPal para más detalles.\n\nGracias por utilizar nuestros servicios.",
            ],
            'items' => [
                [
                    'recipient_type' => 'EMAIL',
                    'amount' => [
                        'value' => $this->normalizeAmount($amount),
                        'currency' => $currency,
                    ],
                    'receiver' => $email,
                    'note' => $note,
                    'sender_item_id' => $itemId,
                ],
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error en payout de PayPal: ' . $response->body());
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

    /**
     * Obtiene el estado de un payout en PayPal.
     * @param string $batchId
     * @return array
     */
    public function getPayoutStatus(string $batchId)
    {
        $response = $this->client->get("/v1/payments/payouts/{$batchId}");

        if (!$response->successful()) {
            throw new \Exception('Error al consultar payout: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Extrae el enlace de aprobación de la respuesta de PayPal.
     * @param array $payload
     * @return string|null
     */
    protected function extractApprovalLink(array $payload): ?string
    {
        $links = $payload['links'] ?? [];

        foreach ($links as $link) {
            if (($link['rel'] ?? null) === 'approve' || ($link['rel'] ?? null) === 'approval_url') {
                return $link['href'] ?? null;
            }
        }

        return null;
    }

    /**
     * Resuelve el precio de un artículo del carrito.
     * @param OrderItem $orderItem
     * @return float
     */
    protected function resolveCartItemPrice(OrderItem $orderItem): float
    {
        if ($orderItem->file) {
            return (float) ($orderItem->file->price ?? 0);
        }

        if ($orderItem->playlist) {
            return (float) ($orderItem->playlist->price ?? 0);
        }

        if ($orderItem->playlistItem) {
            return (float) ($orderItem->playlistItem->price ?? 0);
        }

        return 0.0;
    }

    /**
     * Resuelve la etiqueta de un artículo del carrito.
     * @param OrderItem $orderItem
     * @return string
     */
    protected function resolveCartItemLabel(OrderItem $orderItem): string
    {
        if ($orderItem->file) {
            return (string) $orderItem->file->name;
        }

        if ($orderItem->playlist) {
            return 'Playlist: ' . $orderItem->playlist->name;
        }

        if ($orderItem->playlistItem) {
            return 'Audio: ' . $orderItem->playlistItem->title;
        }

        return 'Producto';
    }
}
