<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->default(fn($record) => $record->user->email ?? 'Invitado' ),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('order_items_count')
                    ->label('Archivos')
                    ->toggleable()
                    ->default(fn($record) => $record->order_items()->count()),

                TextColumn::make('amount')
                    ->label('Importe')
                    ->money('usd', true) // o 'usd'
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        'failed' => 'Cancelada',
                        default => ucfirst($state),
                    })
                    ->colors(fn(string $state) => match ($state) {
                        'paid' => ['success'],
                        'pending' => ['warning'],
                        'failed' => ['danger'],
                        default => ['warning'],
                    })
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Pagado el')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expira el')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creada el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        'failed' => 'Cancelada',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user','name', function(EloquentBuilder $query) {
                        $query->whereHas('orders');
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->label('Ver'),
                Action::make('download_files')
                    ->label('Descargar archivos')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Order $record) {
                        return $record->downloadFilesZip();
                    })
                    ->visible(function (Order $record) {
                        return $record->order_items()->count() > 0;
                    }),
                Action::make('cancel')
                    ->label('Anular Orden')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Orden')
                    ->modalDescription('¿Seguro? Esta acción influira en las cotizaciones y montos generados.')
                    ->modalCancelActionLabel('No, Cancelar')
                    ->modalSubmitActionLabel('Si, el pago fue reembolsado')
                    ->action(function (Order $record) {
                        $record->status = 'failed';
                        $record->save();

                        Notification::make()
                            ->success()
                            ->persistent()
                            ->title('Orden Cancelada')
                            ->body('La Orden #'.$record->id.' fue cancelada, no sera contada en las liquidaciones de la página')
                            ->send();
                    })
                    ->visible(function (Order $record) {
                        return $record->status === 'paid';
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(
                fn(EloquentBuilder $query) => $query->where('currency', 'USD')->orderBy('created_at', 'desc')
            );
    }
}
