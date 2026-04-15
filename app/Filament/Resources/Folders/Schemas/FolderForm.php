<?php

namespace App\Filament\Resources\Folders\Schemas;

use App\Enums\FolderTypeEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FolderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(function(){
                        $options = [];

                        foreach (FolderTypeEnum::cases() as $case) {
                            $options[$case->value] = FolderTypeEnum::getTransformName($case->value);
                        }

                        return $options;
                    })
                    ->default(FolderTypeEnum::PLAYLIST->value),
                Textarea::make('description')
                    ->label('Descripción')
                    ->default(null)
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Imagen de portada')
                    ->columnSpanFull()
                    ->image()
                    ->disk('s3')
                    ->directory('images/folders'),
            ]);
    }
}
