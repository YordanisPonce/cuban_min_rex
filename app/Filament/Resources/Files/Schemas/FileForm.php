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

        $toKey = function ($value): ?string {
            if (!$value)
                return null;

            // Si viene URL -> quédate con el path
            if (Str::startsWith($value, ['http://', 'https://'])) {
                $value = parse_url($value, PHP_URL_PATH) ?? '';
            }

            $value = ltrim($value, '/'); // "storage/images/a.png" o "images/a.png"

            // Quita prefijos típicos
            $value = preg_replace('#^(storage/|public/|storage/app/public/)#', '', $value);

            return $value ?: null; // "images/a.png"
        };
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

                    // ✅ hidrata el state como key de S3
                    ->formatStateUsing(fn($state) => $toKey($state))

                    // ✅ obliga a Filament a usar una URL válida para el preview

                    // ✅ cuando quitas el archivo en el form, que borre bien en S3
                    ->deleteUploadedFileUsing(fn($file) => Storage::disk('s3')->delete($toKey($file)))
                    ->getUploadedFileUrlUsing(fn($file) => Storage::disk('s3')->url($toKey($file)))
                    ->dehydrateStateUsing(fn($state) => $state ? Storage::disk('s3')->url($state) : null),

                // (si quieres seguir guardando URL en BD)

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
