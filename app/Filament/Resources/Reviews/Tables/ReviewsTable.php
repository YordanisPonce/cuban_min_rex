<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('De'),
                TextColumn::make('dj.name')
                    ->label('A')
                    ->default('CubanPool'),
                TextColumn::make('rating')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('comment')
                    ->label('Comentario'),
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
                SelectFilter::make('dj_id')
                    ->label('A')
                    ->relationship('dj', 'name', function($query){
                        return $query->whereHas('reviews');
                    })
                    ->visible(auth()->user()->role === 'admin')
            ])
            ->recordActions([
                //ViewAction::make(),
                //EditAction::make(),
            ])
            ->toolbarActions([
                //BulkActionGroup::make([
                //    DeleteBulkAction::make(),
                //]),
            ])
            ->emptyStateHeading('No hay reseñas')
            ->emptyStateDescription('Aún no se han dejado reseñas.')
            ->emptyStateIcon('heroicon-o-star');
    }
}
