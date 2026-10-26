<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;

class UserDownloadHistory extends Page
{
    protected string $view = 'filament.pages.user-download-history';

    protected static ?string $title = 'Descargas realizadas';

    protected static ?string $navigationLabel = null; // No mostrar en la navegación
    
    protected static bool $shouldRegisterNavigation = false; // No registrar en la navegación
    
    public User $user;

    public function mount($record)
    {
        $this->user = User::findOrFail($record);
    }
}
