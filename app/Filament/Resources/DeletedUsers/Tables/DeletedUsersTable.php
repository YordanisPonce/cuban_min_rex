<?php

namespace App\Filament\Resources\DeletedUsers\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeletedUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()->orderBy('deleted_at', 'desc'))
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('email')->label('Correo'),
                TextColumn::make('deleted_at')->label('Fecha de Eliminado')->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y')),
            ])
            ->filters([
                //TrashedFilter::make()->default('0'),
            ])
            ->recordActions([
                //EditAction::make(),
                RestoreAction::make()->visible(auth()->user()->role === 'admin')->requiresConfirmation()->successNotificationTitle('Usuario restaurado correctamente.'),
            ])
            ->toolbarActions([
                /*BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),*/
            ]);
    }
}
