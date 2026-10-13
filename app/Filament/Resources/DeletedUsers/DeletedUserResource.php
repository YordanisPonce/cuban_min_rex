<?php

namespace App\Filament\Resources\DeletedUsers;


use App\Filament\Resources\DeletedUsers\Pages\ListDeletedUsers;
use App\Filament\Resources\DeletedUsers\Schemas\DeletedUserForm;
use App\Filament\Resources\DeletedUsers\Tables\DeletedUsersTable;
use App\Models\User as DeletedUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

class DeletedUserResource extends Resource
{
    protected static ?string $model = DeletedUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserMinus;

    protected static ?string $recordTitleAttribute = 'Usuarios Eliminados';

    protected static string|UnitEnum|null $navigationGroup = 'Usuarios';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Usuarios Eliminados';

    protected static ?string $modelLabel = 'Usuario Eliminado';

    protected static ?string $pluralModelLabel = 'Usuaios Eliminados';

    #[Override]
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'developer';
    }

    public static function form(Schema $schema): Schema
    {
        return DeletedUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeletedUsersTable::configure($table);
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
            'index' => ListDeletedUsers::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
