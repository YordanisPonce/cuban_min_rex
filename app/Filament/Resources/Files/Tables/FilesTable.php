<?php

namespace App\Filament\Resources\Files\Tables;

use App\Enums\SectionEnum;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Response;

class FilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),
                TextColumn::make('bpm')
                    ->label('BPM')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoría'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                        default => ucfirst($state),
                    })
                    ->colors(fn(string $state) => match ($state) {
                        'active' => ['success'],
                        'inactive' => ['primary'],
                        default => ['primary'],
                    }),
                TextColumn::make('sections')
                    ->label('Secciones a mostrar')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        SectionEnum::MAIN->value => config('app.name'),
                        SectionEnum::CUBANDJS->value => 'CubanDJs',
                        default => ucfirst($state),
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->options(fn (): array => Category::orderBy('name')->pluck('name', 'id')->all())
            ])->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            )
            ->recordActions([
                // Action::make('download')
                //     ->label('Descargar')
                //     ->hidden(fn($record) => Auth::user()->id != $record->user_id)
                //     ->icon(svg('entypo-download'))
                //     ->action(function ($record) {
                //         $path = storage_path('app/public/'.$record->file);
                //         return Response::download($path);
                //     }),
                EditAction::make()->hidden(fn($record) => Auth::user()->id != $record->user_id && Auth::user()->role != 'admin')->label('Editar'),
                DeleteAction::make()->hidden(fn($record) => Auth::user()->id != $record->user_id && Auth::user()->role != 'admin')->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(
                fn(EloquentBuilder $query) => auth()->user()->role!=='admin' ? $query->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc') : $query->orderBy('created_at', 'desc')
            );
    }
}
