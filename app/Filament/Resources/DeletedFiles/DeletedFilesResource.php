<?php

namespace App\Filament\Resources\DeletedFiles;

use App\Filament\Resources\DeletedFiles\Pages\CreateDeletedFiles;
use App\Filament\Resources\DeletedFiles\Pages\EditDeletedFiles;
use App\Filament\Resources\DeletedFiles\Pages\ListDeletedFiles;
use App\Filament\Resources\DeletedFiles\Schemas\DeletedFilesForm;
use App\Filament\Resources\DeletedFiles\Tables\DeletedFilesTable;
use App\Models\File as DeletedFiles;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use UnitEnum;

class DeletedFilesResource extends Resource
{
    protected static ?string $model = DeletedFiles::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentMinus;

    protected static ?string $recordTitleAttribute = 'Usuarios Eliminados';

    protected static string|UnitEnum|null $navigationGroup = 'Archivos';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Archivos Eliminados';

    protected static ?string $modelLabel = 'Archivo Eliminado';

    protected static ?string $pluralModelLabel = 'Archivos Eliminados';

    #[Override]
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'developer';
    }

    public static function form(Schema $schema): Schema
    {
        return DeletedFilesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeletedFilesTable::configure($table);
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
            'index' => ListDeletedFiles::route('/'),
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
