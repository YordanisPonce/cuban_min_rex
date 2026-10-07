<?php

namespace App\Filament\Resources\DownloadPacks;

use App\Filament\Resources\DownloadPacks\Pages\CreateDownloadPack;
use App\Filament\Resources\DownloadPacks\Pages\EditDownloadPack;
use App\Filament\Resources\DownloadPacks\Pages\ListDownloadPacks;
use App\Filament\Resources\DownloadPacks\Schemas\DownloadPackForm;
use App\Filament\Resources\DownloadPacks\Tables\DownloadPacksTable;
use App\Models\DownloadPack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DownloadPackResource extends Resource
{
    protected static ?string $model = DownloadPack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Gift;

    protected static ?string $navigationLabel = 'Paquetes de Descarga';

    protected static ?string $modelLabel = 'Paquete de Descarga';

    protected static ?int $navigationSort = 8;

    protected static ?string $pluralModelLabel = 'Paquetes de Descarga';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'developer';
    }

    public static function form(Schema $schema): Schema
    {
        return DownloadPackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadPacksTable::configure($table);
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
            'index' => ListDownloadPacks::route('/'),
            'create' => CreateDownloadPack::route('/create'),
            'edit' => EditDownloadPack::route('/{record}/edit'),
        ];
    }
}
