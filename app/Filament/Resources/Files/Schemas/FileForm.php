<?php

namespace App\Filament\Resources\Files\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class FileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Select::make('collection_id')
                    ->label('Selecciona una Colección')
                    ->options(function () {
                        return Collection::where('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    }),
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('file')
                    ->label('Archivo Adjunto')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'video/mp4', 'video/avi'])
                    ->maxSize(20480)
                    ->required()
                    ->disk('public')
                    ->directory('files')
                    ->downloadable()
                    ->columnSpanFull(),
            ]);
    }
}
