<?php

namespace App\Filament\Pages;

use App\Livewire\UserPaymentsTable;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class UserPayments extends Page
{

    protected string $view = 'filament.pages.user-payments';

    protected static ?string $title = ' ';

    protected static ?string $navigationLabel = null; // No mostrar en la navegación

    protected static bool $shouldRegisterNavigation = false; // No registrar en la navegación

    public User $user;


    public function mount(int|string $record): void
    {
        $this->user = User::findOrFail($record);
    }

    // Agregar este método para pasar datos a la vista
    protected function getViewData(): array
    {
        return [
            'userId' => $this->user->id,
        ];
    }
}
