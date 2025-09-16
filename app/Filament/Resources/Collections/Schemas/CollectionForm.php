<?php

namespace App\Filament\Resources\Collections\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                TextInput::make('name')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('images') 
                    ->preserveFilenames(),
                Select::make('category_id')
                    ->label('Selecciona una Categoría')
                    ->options(function () {
                        return Category::where('is_general', true)->orWhere('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })
            ]);
    }
}
