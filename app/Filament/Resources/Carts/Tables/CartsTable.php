<?php

namespace App\Filament\Resources\Carts\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Usuario')
                    ->searchable(),
                TextColumn::make('count')
                    ->label('Elementos en el Carrito')
                    ->alignCenter()
                    ->default(function($record) {
                        return $record->cart->cart_items->count();
                    }),
                TextColumn::make('amount')
                    ->label('Precio')
                    ->alignCenter()
                    ->default(function($record) {
                        return $record->cart->get_cart_count();
                    })
                    ->numeric()
                    ->prefix('$ '),
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
                Action::make('empty')
                    ->color('danger')
                    ->icon('heroicon-o-minus-circle')
                    ->action(function($record) {
                        try {
                            $record->cart->cart_items()->delete();
                            Notification::make()
                                ->title("Carrito Limpio")
                                ->success()
                                ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title("Error al vaciar carrito: ".$th->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => $record->cart->cart_items->count() > 0 && auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
