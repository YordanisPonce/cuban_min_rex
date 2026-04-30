<?php

namespace App\Filament\Resources\BlockUsers;

use App\Filament\Resources\BlockUsers\Pages\CreateBlockUsers;
use App\Filament\Resources\BlockUsers\Pages\EditBlockUsers;
use App\Filament\Resources\BlockUsers\Pages\ListBlockUsers;
use App\Filament\Resources\BlockUsers\Schemas\BlockUsersForm;
use App\Filament\Resources\BlockUsers\Tables\BlockUsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;
use UnitEnum;

class BlockUsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::NoSymbol;

    protected static ?string $recordTitleAttribute = 'Usuarios Bloqueados';

    protected static string|UnitEnum|null $navigationGroup = 'Usuarios';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Usuarios Bloqueados';

    protected static ?string $modelLabel = 'Usuario Bloqueado';

    protected static ?string $pluralModelLabel = 'Usuaios Bloqueados';

    #[Override]
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'developer';
    }

    public static function form(Schema $schema): Schema
    {
        return BlockUsersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlockUsersTable::configure($table);
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
            'index' => ListBlockUsers::route('/'),
            'create' => CreateBlockUsers::route('/create'),
            'edit' => EditBlockUsers::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_block', true);
    }
}
