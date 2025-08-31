<?php

namespace App\Filament\Resources\Plans\Pages;

use App\Filament\Resources\Plans\PlanResource;
use App\Services\StripeService;
use Filament\Resources\Pages\CreateRecord;

class CreatePlan extends CreateRecord
{
    protected static string $resource = PlanResource::class;

    protected function afterCreate(): void
    {
        $plan = $this->getRecord();
        $stripeService = app(StripeService::class);
        $stripeService->syncStripePlan($plan);
    }
}
