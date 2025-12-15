<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use App\Types\CustomTemporaryUploadedFile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ZipArchive;

class ListFiles extends ListRecords
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Importar Archivos')
                ->button()
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required(),
                    FileUpload::make('file')->maxSize(800000)
                        ->label('Subir vista previa del archivo')
                        ->acceptedFileTypes(['audio/*', 'video/*', 'application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                        ->required()
                        ->disk('s3')
                        ->directory('files')
                        ->helperText('El nombre del archivo no debe exceder los 255 caracteres')
                        ->columnSpanFull(),
                    FileUpload::make('original_file')->maxSize(800000)
                        ->label('Subir archivo completo')
                        ->acceptedFileTypes(['audio/*', 'video/*', 'application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                        ->required()
                        ->disk('public')
                        ->directory('files')
                        ->helperText('El nombre del archivo no debe exceder los 255 caracteres')
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->label('Subir Poster')
                        ->image()
                        ->disk('s3')
                        ->directory('images'),
                    TextInput::make('price')
                        ->label('Precio')
                        ->numeric()
                        ->prefix('$'),
                    TextInput::make('bpm')
                        ->label('BPM')
                        ->required(),
                    // Select::make('collection_id')
                    //     ->label('Selecciona una Colección')
                    //     ->options(function () {
                    //         return Collection::where('user_id', Auth::user()->id)
                    //             ->pluck('name', 'id');
                    //     })->reactive()
                    //     ->afterStateUpdated(function ($state, callable $set) {
                    //         if (Collection::find($state)) {
                    //             $set('dinamic_category_id', Collection::find($state)->category->id);
                    //             $set('category_id', Collection::find($state)->category->id);
                    //         } else {
                    //             $set('dinamic_category_id', $state);
                    //         }
                    //     }),
                    Select::make('dinamic_category_id')
                        ->label('Selecciona una Categoría')
                        ->options(function () {
                            return Category::where('is_general', true)->orWhere('user_id', Auth::user()->id)->orderBy('name')
                                ->pluck('name', 'id');
                        })
                        ->disabled(fn($get) => $get('collection_id') !== null)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('category_id', $state);
                        }),
                    Hidden::make('category_id')
                        ->default(fn($get) => $get('dinamic_category_id')),
                ])->action(function (array $data): void {
                    try{
                        $localFile = Storage::disk('public')->url($data['original_file']);

                        Storage::disk('s3')->put($data['original_file'], $localFile);

                        $file = new File();
                        $file->name = $data['name'] ?? basename(Storage::disk('s3')->url($data['file']));
                        $file->file = $data['file'];
                        $file->poster = $data['image'];
                        $file->original_file = $data['original_file'];
                        $file->category_id = $data['category_id'];
                        $file->user_id = Auth::user()->id;
                        $file->price = $data['price'] ?? 0;
                        $file->bpm = $data['bpm'];
                        $file->save();

                        // Verificar si es un ZIP
                        if (pathinfo($localFile, PATHINFO_EXTENSION) === 'zip') {

                            // Crear la colección/pack
                            $collection = new Collection();
                            $collection->name = $data['name'] ?? basename(Storage::disk('s3')->url($data['file']));
                            $collection->category_id = $data['category_id'];
                            $collection->image = $data['image'];
                            $collection->user_id = Auth::user()->id;
                            $collection->save();

                            // Descargar el ZIP desde S3 a un archivo temporal
                            //$zipPath = storage_path('app/temp_' . uniqid() . '.zip');
                            //Storage::disk('s3')->download($file->file, $zipPath);

                            $zipPath = Storage::disk('public')->path($data['original_file']);

                            $zip = new ZipArchive;

                            if ($zip->open($zipPath) === TRUE) {

                                Log::info('Entro al ZIP '.$zipPath);

                                $extractPath = storage_path('app/temp_' . uniqid());

                                if (!file_exists($extractPath)) {
                                    mkdir($extractPath, 0755, true);
                                }

                                $zip->extractTo($extractPath);
                                $zip->close();

                                // Obtener archivos del ZIP (excluyendo . y ..)
                                $files = array_diff(scandir($extractPath), ['.', '..']);
                                //$files = scandir($extractPath);
                                $fileCount = count($files);

                                Log::info('Escanados '.$fileCount.' archivos');

                                // Calcular precio por archivo si hay precio total
                                $filePrice = ($data['price'] && $data['price'] > 0 && $fileCount > 0)
                                    ? $data['price'] / $fileCount
                                    : 0;

                                foreach ($files as $f) {
                                    $filePath = $extractPath . '/' . $f;

                                    Log::info('Leyendo archivo '.$f);

                                    // Verificar que sea un archivo (no directorio)
                                    if (is_file($filePath)) {

                                        Log::info(''.$f.' es un archivo');

                                        // Subir cada archivo a S3
                                        $filePathInS3 = 'files/' . uniqid() . '.' . pathinfo($localFile, PATHINFO_EXTENSION);
                                        Storage::disk('s3')->put($filePathInS3, file_get_contents($filePath));

                                        // Crear registro de archivo
                                        $newFile = new File();  // ¡Nota: variable diferente!
                                        $newFile->name = pathinfo($f, PATHINFO_FILENAME); // Nombre sin extensión
                                        $newFile->file = $filePathInS3;
                                        $newFile->original_file = $filePathInS3;
                                        $newFile->collection_id = $collection->id; // Asociar a la colección
                                        $newFile->category_id = $data['category_id'];
                                        $newFile->poster = $data['image'];
                                        $newFile->user_id = Auth::user()->id;
                                        $newFile->price = $filePrice;
                                        $newFile->bpm = $data['bpm'];
                                        $newFile->status = "inactive";
                                        $newFile->save();
                                    }
                                }

                                // Limpiar archivos temporales
                                array_map('unlink', glob("$extractPath/*"));
                                if (is_dir($extractPath)) {
                                    rmdir($extractPath);
                                }
                                if (file_exists($zipPath)) {
                                    unlink($zipPath);
                                }

                            } else {
                                //throw new \Exception('No se pudo abrir el archivo ZIP: ' . $zipPath);
                                Notification::make()
                                        ->title('No se pudo abrir el archivo ZIP: ' . $zipPath)
                                        ->danger()
                                        ->send();
                            }
                        }

                        Storage::disk('public')->delete($data['original_file']);
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title($th->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
