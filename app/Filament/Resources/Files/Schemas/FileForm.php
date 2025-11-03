<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\Category;
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
                    ->required()
                    ->columnSpanFull(),
                /*Select::make('collection_id')
                    ->label('Selecciona una Colección')
                    ->options(function () {
                        return Collection::where('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (Collection::find($state)) {
                            $set('dinamic_category_id', Collection::find($state)->category->id);
                            $set('category_id', Collection::find($state)->category->id);
                        } else {
                            $set('dinamic_category_id', $state);
                        }
                    }),*/
                Select::make('dinamic_category_id')
                    ->label('Selecciona una Categoría')
                    ->options(function () {
                        return Category::where('is_general', true)->orWhere('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })
                    ->disabled(fn($get) => $get('collection_id') !== null)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('category_id', $state);
                    }),
                Hidden::make('category_id')
                    ->default(fn($get) => $get('dinamic_category_id')),
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('bpm')
                    ->label('BPM')
                    ->required(),
                FileUpload::make('file')
                    ->label('Subir vista previa del archivo')
                    ->acceptedFileTypes(['audio/*','video/*','application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                    ->required()
                    ->disk('s3')
                    ->directory('files')
                    ->maxSize(512000)
                    ->columnSpanFull(),
                FileUpload::make('original_file')
                    ->label('Subir archivo completo')
                    ->acceptedFileTypes(['audio/*','video/*','application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                    ->required()
                    ->disk('s3')
                    ->directory('files')
                    ->maxSize(512000)
                    ->columnSpanFull(),
            ]);
    }
}
