<?php

namespace App\Filament\Resources\Plans\Pages;

use App\Filament\Resources\Plans\PlanResource;
use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditPlan extends EditRecord
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('reset_cover')->label("Eliminar Imagen")
                ->icon('heroicon-o-photo')->requiresConfirmation()->action(function () {
                    $record = $this->record;
                    try {
                        $imagePath = Storage::disk('s3')->get($record->image);
                        Storage::disk('s3')->delete($imagePath);
                    } catch (\Exception $e) {
                        Notification::make()->body('No se pudo eliminar la imagen anterior de AWS S3: ' . $e->getMessage())->warning()->send();
                    }
                    $record->update(['image' => null]);
                    Notification::make()->body('Imagen eliminada')->success()->send();
                })->visible(fn () => $this->record->image !== null),
        ];
    }

    protected function afterSave(): void
    {
        $plan = $this->getRecord();
        $stripeService = app(StripeService::class);
        $stripeService->syncStripePlan($plan);
    }
}
