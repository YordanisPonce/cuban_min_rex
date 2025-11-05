<?php

namespace App\Services;

use App\Models\Plan;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret_key'));
    }

    public function syncStripePlan(Plan $plan): bool
    {

        if ($plan->stripe_price_id) {

            $product = $this->client->products->update($plan->stripe_product_id, [
                'name' => $plan->name,
                'description' => $plan->description,
            ]);
        } else {

            $product = $this->client->products->create([
                'name' => $plan->name,
                'description' => $plan->description,
            ]);

            $plan->stripe_product_id = $product->id;

        }

        if ($plan->stripe_price_id) {
            $this->client->prices->update($plan->stripe_price_id, [
                'active' => false,
            ]);
        }

        $interval = $this->mapInterval($plan->duration_months);

        $price = $this->client->prices->create([
            'unit_amount' => (int) ($plan->price * 100),
            'currency' => 'usd',
            'recurring' => ['interval' => $interval],
            'product' => $product->id,
        ]);

        $plan->update([
            'stripe_product_id' => $product->id,
            'stripe_price_id' => $price->id,
        ]);

        return true;
    }


    private function mapInterval(int $durationMonths): string
    {
        return match ($durationMonths) {
            12 => 'year',
            1 => 'month',
            default => 'month',
        };
    }


    public function getClient()
    {

        return $this->client;
    }
}
