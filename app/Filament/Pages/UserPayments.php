<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;

class UserPayments extends Page
{
    protected string $view = 'filament.pages.user-payments';

    protected static ?string $title = 'Pagos realizados';

    protected static ?string $navigationLabel = null; // No mostrar en la navegación
    
    protected static bool $shouldRegisterNavigation = false; // No registrar en la navegación

    public User $user;

    public function mount($record)
    {
        $this->user = User::findOrFail($record);
    }

}
