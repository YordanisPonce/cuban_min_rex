<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active'),
                FileUpload::make('poster')
                    ->label('Subir Poster')
                    ->image()
                    ->disk('s3')
                    ->directory('images')
                    ->columnSpanFull()
                    // ✅ Al editar: si en BD hay URL, Filament necesita el PATH
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return null;

                        // si es URL, toma solo el path
                        if (Str::startsWith($state, ['http://', 'https://'])) {
                            $state = parse_url($state, PHP_URL_PATH) ?? '';
                        }

                        $state = ltrim($state, '/'); // "storage/originals/xxx.zip"
            
                        // ✅ limpia prefijos comunes
                        $state = preg_replace('#^(storage/|public/|storage/app/public/)#', '', $state);

                        return $state ?: null; // => "originals/xxx.zip"
                    })
                    // ✅ Al guardar: conviértelo de nuevo a URL para tu frontend
                    ->dehydrateStateUsing(fn($state) => $state ? Storage::disk('s3')->url($state) : null),

                FileUpload::make('file')
                    ->label('Subir vista previa del archivo')
                    ->acceptedFileTypes(['audio/*', 'video/*', 'application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                    ->required()
                    ->disk('s3')
                    ->directory('previews')
                    ->columnSpanFull()
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return null;

                        // si es URL, toma solo el path
                        if (Str::startsWith($state, ['http://', 'https://'])) {
                            $state = parse_url($state, PHP_URL_PATH) ?? '';
                        }

                        $state = ltrim($state, '/'); // "storage/originals/xxx.zip"
            
                        // ✅ limpia prefijos comunes
                        $state = preg_replace('#^(storage/|public/|storage/app/public/)#', '', $state);

                        return $state ?: null; // => "originals/xxx.zip"
            
                    })
                    ->dehydrateStateUsing(fn($state) => $state ? Storage::disk('s3')->url($state) : null),
                FileUpload::make('original_file')
                    ->label('Subir archivo completo')
                    ->acceptedFileTypes([
                        'audio/*',
                        'video/*',
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/x-zip',
                        'multipart/x-zip',
                    ])
                    ->required()
                    ->disk('s3')
                    ->directory('originals')
                    ->columnSpanFull()
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return null;

                        // si es URL, toma solo el path
                        if (Str::startsWith($state, ['http://', 'https://'])) {
                            $state = parse_url($state, PHP_URL_PATH) ?? '';
                        }

                        $state = ltrim($state, '/'); // "storage/originals/xxx.zip"
            
                        // ✅ limpia prefijos comunes
                        $state = preg_replace('#^(storage/|public/|storage/app/public/)#', '', $state);

                        return $state ?: null; // => "originals/xxx.zip"
                    })
                    ->dehydrateStateUsing(fn($state) => $state ? Storage::disk('s3')->url($state) : null)


            ]);
    }
}
