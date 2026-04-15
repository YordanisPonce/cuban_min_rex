<?php

namespace App\Filament\Resources\Djs;

use App\Filament\Resources\Djs\Pages\CreateDj;
use App\Filament\Resources\Djs\Pages\EditDj;
use App\Filament\Resources\Djs\Pages\ListDjs;
use App\Filament\Resources\Djs\Schemas\DjForm;
use App\Filament\Resources\Djs\Tables\DjsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DjResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'Dj';
    
    protected static string|null $label = 'Djs';

    protected static ?int $navigationSort = 7;

    protected static string|UnitEnum|null $navigationGroup = 'Usuarios';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return DjForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DjsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDjs::route('/'),
            'create' => CreateDj::route('/create'),
            'edit' => EditDj::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Dj';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Djs';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('role', ['worker', 'admin']);
    }
}
