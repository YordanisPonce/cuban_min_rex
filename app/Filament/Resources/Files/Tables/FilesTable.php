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
                TextColumn::make('categories')
                    ->label('Categorías')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->categories->pluck('name');
                    })
                    ->default('Sin categoría'),
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
                    ->formatStateUsing(fn(string $state) => SectionEnum::getTransformName($state)),
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
                SelectFilter::make('categories')
                    ->relationship(name: 'categories', titleAttribute: 'name')
                    ->label('Categorías')
                    ->options(fn (): array => Category::orderBy('name')->pluck('name', 'id')->all())
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ]),
                SelectFilter::make('sections')
                    ->label('Secciones a mostrar')
                    ->options(function () {
                        $options = [];
                        $sections = SectionEnum::cases();
                        foreach ($sections as $section) {
                            $options[$section->value] = SectionEnum::getTransformName($section->value);
                        }
                        return $options;
                    })->multiple()
                    ->query(function (EloquentBuilder $query, array $data) {
                        $query->where(function (EloquentBuilder $query) use ($data) {
                            foreach ($data as $section) {
                                $query->whereJsonContains('sections', $section);
                            }
                        });
                    }),
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
