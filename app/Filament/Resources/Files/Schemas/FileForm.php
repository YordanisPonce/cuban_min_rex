<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Enums\SectionEnum;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
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
                Select::make('categories')
                    ->label('Selecciona la/las Categoría(s)')
                    ->searchable()
                    ->multiple()
                    ->relationship(name: 'categories', titleAttribute: 'name')
                    ->preload(),
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
                    ->columnSpanFull(),
                FileUpload::make('file')
                    ->label('Subir vista previa del archivo')
                    ->acceptedFileTypes(['audio/*', 'video/*', 'application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                    ->required()
                    ->disk('s3')
                    ->columnSpanFull(),

                FileUpload::make('original_file')
                    ->label('Subir archivo completo')
                    ->acceptedFileTypes(['audio/*', 'video/*', 'application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                    ->required()
                    ->disk('s3')
                    ->columnSpanFull(),
                Select::make('sections')
                    ->label('Secciones a mostrar')
                    ->options(function () {
                        $options = [];

                        $sections = SectionEnum::cases();
    
                        foreach ($sections as $sectionsCase) {
                            $options[$sectionsCase->value] = SectionEnum::getTransformName($sectionsCase->value);
                        }

                        return $options;
                    })
                    ->multiple()
                    ->required(),

            ]);
    }
}
