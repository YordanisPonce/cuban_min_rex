<?php

namespace App\Filament\Resources\Downloads;

use App\Filament\Resources\Downloads\Pages\ListDownloads;
use App\Filament\Resources\Downloads\Schemas\DownloadForm;
use App\Filament\Resources\Downloads\Schemas\DownloadInfolist;
use App\Filament\Resources\Downloads\Tables\DownloadsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;
use UnitEnum;

class DownloadResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';

    protected static ?string $navigationLabel = 'Descargas';

    protected static ?string $modelLabel = 'Descarga';

    protected static ?string $pluralModelLabel = 'Descargas';

    #[Override]
    public static function canAccess(): bool
    {
        return auth()->user()->role == 'developer';
    }

    public static function form(Schema $schema): Schema
    {
        return DownloadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DownloadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadsTable::configure($table);
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
            'index' => ListDownloads::route('/'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('files');
    }
}
