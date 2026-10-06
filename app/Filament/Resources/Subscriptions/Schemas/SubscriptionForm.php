<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->default(null),
                TextInput::make('stripe_id')
                    ->default(null),
                TextInput::make('stripe_status')
                    ->default(null),
                TextInput::make('stripe_price')
                    ->default(null),
                TextInput::make('quantity')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('trial_ends_at'),
                DateTimePicker::make('ends_at'),
                DateTimePicker::make('canceled_at'),
                TextInput::make('plan_name')
                    ->default(null),
                TextInput::make('plan_price')
                    ->numeric()
                    ->default(0.0),
                TextInput::make('max_downloads')
                    ->numeric()
                    ->default(0),
                TextInput::make('extra_downloads')
                    ->numeric()
                    ->default(0),
                TextInput::make('consume_extra_downloads')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
