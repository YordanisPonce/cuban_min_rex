<?php

namespace App\Filament\Resources\Files\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;

class FileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                FileUpload::make('name')
                    ->disk('public')
                    ->directory('files')
                    ->preserveFilenames(),
                Select::make('collection_id')
                    ->label('Selecciona una Colección')
                    ->options(function () {
                        return Collection::where('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })
            ]);
    }
}
