<?php

namespace App\Filament\Resources\Djs\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DjsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                IconColumn::make('paypal_statys')->label('Estado')
                    ->icons([
                        'heroicon-o-x-circle' => 'pending',
                        'heroicon-o-check-circle' => 'verified',
                    ])
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                    ])
                    ->tooltip(fn($state) => $state === 'pending' ? 'Sin Correo PayPal' : 'Todo Ok')
                    ->default(fn($record) => $record->paypal_email ? 'verified' : 'pending'),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('remixes')->label('Remixes')->default('0')->getStateUsing(fn($record) => $record->files()->audios()->count())->alignCenter(),
                TextColumn::make('videos')->label('Videos')->default('0')->getStateUsing(fn($record) => $record->files()->videos()->count())->alignCenter(),
                TextColumn::make('packs')->label('Packs')->default('0')->getStateUsing(fn($record) => $record->files()->zips()->count())->alignCenter(),
                TextColumn::make('playlists_count')->label('PlayLists')->default('0')->getStateUsing(fn($record) => $record->playlists()->count())->alignCenter(),
                TextColumn::make('followers_count')->label('Seguidores')->default('0')->getStateUsing(fn($record) => $record->followers()->count())->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
