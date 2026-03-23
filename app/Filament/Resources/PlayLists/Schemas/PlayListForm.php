<?php

namespace App\Filament\Resources\PlayLists\Schemas;

use Dom\Text;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PlayListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                Tabs::make('PlayListTabs')
                    ->tabs([
                        Tabs\Tab::make('Datos de la PlayList')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre de la PlayList')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->label('Precio de la PlayList')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Textarea::make('description')
                                    ->label('Descripción de la PlayList')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                FileUpload::make('cover')
                                    ->label('Portada de la PlayList')
                                    ->image()
                                    ->directory('playlists/covers')
                                    ->disk('public')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
