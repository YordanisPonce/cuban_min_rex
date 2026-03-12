<?php

namespace App\Filament\Resources\PlayLists\RelationManagers;

use App\Models\File;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                TextInput::make('title')
                    ->label('Nombre')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Precio')
                    ->prefix('$ ')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('cover')
                    ->label('Portada del Archivo')
                    ->image()
                    ->directory('playlists/items/covers')
                    ->disk('s3')
                    ->columnSpanFull(),
                FileUpload::make('file_path')
                    ->acceptedFileTypes(['audio/*'])
                    ->label('Archivo')
                    ->disk('s3')
                    ->directory('playlists/items/files')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nombre'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->prefix('$ '),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Agregar Archivo')->modalHeading('Agregar Archivo a la PlayList'),
            ])
            ->recordActions([
                EditAction::make()->label('Editar')->modalHeading('Editar Archivo a la PlayList'),
                DeleteAction::make()->label('Eliminar')
                    ->modalHeading('Eliminar Archivo de la PlayList')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este archivo de la PlayList? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar'),
            ])
            ->toolbarActions([
                //
            ])
            ->heading('Archivos');
    }
}
