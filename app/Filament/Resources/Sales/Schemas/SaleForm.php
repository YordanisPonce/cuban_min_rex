<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Select::make('file_id')
                    ->relationship('file', 'name')
                    ->default(null),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                    ->default('pending')
                    ->required(),
                TextInput::make('user_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('admin_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('customer_email')
                    ->email()
                    ->default(null),
                Select::make('play_list_id')
                    ->relationship('playList', 'name')
                    ->default(null),
                Select::make('play_list_item_id')
                    ->relationship('playListItem', 'title')
                    ->default(null),
            ]);
    }
}
