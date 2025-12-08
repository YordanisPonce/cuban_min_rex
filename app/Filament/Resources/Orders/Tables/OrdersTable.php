<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

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
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('file.name')
                    ->label('Archivo')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Importe')
                    ->money('eur', true) // o 'usd'
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'paid' => 'Pagado',
                        'pending' => 'Pendiente',
                        'failed' => 'Fallido',
                        'canceled' => 'Cancelado',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'success' => ['paid'],
                        'warning' => ['pending'],
                        'danger' => ['failed', 'canceled'],
                    ])
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
                        'failed' => 'Fallido',
                        'canceled' => 'Cancelado',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->label('Ver'),
                Action::make('descargar')
                    ->label('Descargar archivo')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn(Order $record) => $record->file_id !== null)
                    ->action(function (Order $record) {
                        // Verificar que exista archivo asociado
                        if (!$record->file || !$record->file->file) {
                            abort(404, 'Archivo no disponible.');
                        }

                        $path = $record->file->file;
                        $downloadName = $record->file->original_file ?? $record->file->name;

                        if (!Storage::disk('s3')->exists($path)) {
                            abort(404, 'Archivo no encontrado en el servidor.');
                        }

                        return Storage::disk('s3')->download($path); // ✅ sin segundo parámetro
            
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
