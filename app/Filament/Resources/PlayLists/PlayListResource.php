<?php

namespace App\Filament\Resources\PlayLists;

use App\Filament\Resources\PlayLists\Pages\CreatePlayList;
use App\Filament\Resources\PlayLists\Pages\EditPlayList;
use App\Filament\Resources\PlayLists\Pages\ListPlayLists;
use App\Filament\Resources\PlayLists\Pages\ViewPlayList;
use App\Filament\Resources\PlayLists\Schemas\PlayListForm;
use App\Filament\Resources\PlayLists\Tables\PlayListsTable;
use App\Models\PlayList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PlayListResource extends Resource
{
    protected static ?string $model = PlayList::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MusicalNote;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'Playlists';

    protected static ?string $navigationLabel = 'PlayLists';

    protected static string|UnitEnum|null $navigationGroup = 'Archivos';
    
    protected static ?string $modelLabel = 'PlayList';

    public static function form(Schema $schema): Schema
    {
        return PlayListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayListsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayLists::route('/'),
            'create' => CreatePlayList::route('/create'),
            'edit' => EditPlayList::route('/{record}/edit'),
            'view' => ViewPlayList::route('/{record}/view'),
        ];
    }
}
