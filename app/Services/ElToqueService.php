<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class ElToqueService
{
    private string $baseUrl = "https://api.eltoque.com/v1";

    public function getExchangeRates()
    {
        $token =  Setting::firstOrCreate()->eltoque_api_token;

        $response = Http::withToken("{$token}")
            ->get("{$this->baseUrl}/exchange-rates");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    private function getCurrency($currency)
    {
        $data = $this->getExchangeRates();

        if (!$data || !isset($data['data'])) {
            return null;
        }

        return collect($data['data'])->firstWhere('currency', strtoupper($currency));
    }

    public static function getUsdExchangeRate()
    {
        $service = new self();
        $currency = $service->getCurrency('USD');
        return $currency ? $currency['buy'] : null;
    }
}
