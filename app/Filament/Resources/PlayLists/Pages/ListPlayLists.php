<?php

namespace App\Filament\Resources\PlayLists\Pages;

use App\Enums\FolderTypeEnum;
use App\Filament\Resources\PlayLists\PlayListResource;
use App\Http\Controllers\NotificationController;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ListPlayLists extends ListRecords
{
    protected static string $resource = PlayListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make()->label('Nueva PlayList'),
            Action::make('fastCreate')->label('Cargar Playlist')
            ->icon('heroicon-o-arrow-up-circle')
            ->schema(function($schema) {
                return $schema
                    ->schema([
                        TextInput::make('name')->label('Nombre de la Playlist')->required()->columnSpanFull(),
                        TextInput::make('price')->label('Precio total')->numeric()->prefix('$ ')->default(0.00),
                        TextInput::make('items_price')->label('Precio de los Audios por separado')->numeric()->prefix('$ ')->default(0.00),
                        Select::make('folder_id')
                            ->label('Carpeta')
                            ->options(fn () => \App\Models\Folder::where('type', FolderTypeEnum::PLAYLIST->value)->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
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
                    ])->columns(2);
            })
            ->modalSubmitActionLabel('Crear Playlist')
            ->modalCancelActionLabel('Cancelar')
            ->action(function(array $data) {
                try {
                    // Crear la playlist
                    $playlist = PlayListResource::getModel()::create([
                        'name' => $data['name'],
                        'price' => $data['price'] ?? 0.00,
                        'user_id' => auth()->id(),
                    ]);
                    // Procesar cada archivo subido                
                    foreach ($data['items'] as $file) {
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
                    }

                    $followers = $file->user->followers;
                    foreach ($followers as $follower) {
                        if ($follower->ntfs_prefs->new_remixes) {
                            NotificationController::sendPlayListNtf($follower->id, $playlist->id);
                        }
                    }
                    // Retornar un mensaje de éxito o redirigir
                    Notification::make()->title('Playlist creada exitosamente')->success()->send();
                } catch (\Throwable $th) {
                    Notification::make()->title('Error al crear la playlist')->body($th->getMessage())->danger()->send();
                }
                
            })
        ];
    }
}
