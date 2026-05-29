<?php

namespace App\Filament\Resources\PlayLists\RelationManagers;

use App\Models\File;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::user()->id),
                TextInput::make('title')
                    ->label('Nombre')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Precio')
                    ->prefix('$ ')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('cover')
                    ->label('Portada del Archivo')
                    ->image()
                    ->directory('playlists/items/covers')
                    ->disk('s3')
                    ->columnSpanFull(),
                FileUpload::make('file_path')
                    ->acceptedFileTypes(['audio/*'])
                    ->label('Archivo')
                    ->disk('s3')
                    ->directory('playlists/items/files')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nombre'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->prefix('$ '),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //CreateAction::make()->label('Agregar Archivo')->modalHeading('Agregar Archivo a la PlayList'),
                Action::make('fill')->label('Agregar Archivos')
                    ->modalHeading('Agregar Archivos a la PlayList')
                    ->schema([
                        TextInput::make('items_price')->label('Precio de los Audios por separado')->numeric()->prefix('$ ')->default(0.00),
                        FileUpload::make('items')
                            ->label('Archivos')
                            ->helperText('Puedes subir múltiples archivos de audio para esta playlist. Mientras mayor cantidad de archivos, más tiempo tomará el procesamiento. Se paciente, por favor.')
                            ->disk('public')
                            ->directory('temp/playlists')
                            ->multiple()
                            ->preserveFilenames()
                            ->required()
                            ->columnSpanFull()
                            ->previewable(false)
                            ->acceptedFileTypes(['audio/*'])
                            ->placeholder(new HtmlString('<div class="text-center text-sm text-gray-500">Arrastra y suelta tus archivos aquí o haz <b style="color: oklch(0.666 0.179 58.318); cursor: pointer;">Clic</b> para seleccionar</div>'))
                    ])
                    ->action(function(array $data){
                        $playlist = $this->getOwnerRecord();

                        $suc = 0;
                        $fail = 0;
                        
                        // Procesar cada archivo subido                
                        foreach ($data['items'] as $file) {
                            try {
                                $name = Str::random().'.'.pathinfo($file, PATHINFO_EXTENSION);
                                $stream = Storage::disk('public')->path($file);
                                Storage::disk('s3')->putFileAs('playlists/items/files', $stream, $name);
                                $nameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
                                $playlist->items()->create([
                                    'title' => $nameWithoutExtension,
                                    'price' => $data['items_price'] ?? 0.00,
                                    'file_path' => 'playlists/items/files/'.$name,
                                    'cover' => null, // Puedes agregar lógica para generar una portada si lo deseas
                                ]);
                                // Eliminar el archivo temporal después de procesarlo
                                unlink($stream);

                                $suc ++;
                            } catch (\Throwable $th) {
                                $fail ++;
                            }
                        }

                        Notification::make()->title('Proceso terminado')->body('Subidos: '. $suc . ' archivos. Fallaron: '. $fail)->success()->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Editar')->modalHeading('Editar Archivo a la PlayList'),
                DeleteAction::make()->label('Eliminar')
                    ->modalHeading('Eliminar Archivo de la PlayList')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este archivo de la PlayList? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar'),
            ])
            ->toolbarActions([
                //
            ])
            ->heading('Archivos');
    }
}
