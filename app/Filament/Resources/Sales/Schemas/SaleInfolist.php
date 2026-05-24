<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name'),
                TextEntry::make('file.name'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('user_amount')
                    ->numeric(),
                TextEntry::make('admin_amount')
                    ->numeric(),
                TextEntry::make('customer_email'),
                TextEntry::make('playList.name'),
                TextEntry::make('playListItem.title'),
            ]);
    }
}
