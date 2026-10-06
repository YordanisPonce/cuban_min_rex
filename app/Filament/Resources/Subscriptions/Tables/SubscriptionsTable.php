<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('Usuario')
                    ->formatStateUsing(fn($state) => User::find($state)?->name),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => $state === 'default' ? 'Stripe' : 'Paypal'),
                TextColumn::make('stripe_id')
                    ->label('Identificador')
                    ->searchable(),
                TextColumn::make('stripe_status')
                    ->badge()
                    ->label('Estado')
                    ->color(fn($state) => $state == 'active' ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state == 'active' ? 'Activa' : 'Cancelada'),
                TextColumn::make('plan_name')
                    ->label('Plan')
                    ->searchable(),
                TextColumn::make('ends_at')
                    ->label('Vence')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                /*BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),*/
            ])->modifyQueryUsing(fn($query) => $query->whereNotNull('stripe_id'));
    }
}
