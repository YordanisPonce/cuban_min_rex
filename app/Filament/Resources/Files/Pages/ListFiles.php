<?php

namespace App\Filament\Resources\Files\Pages;

use App\Enums\SectionEnum;
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
                        ->acceptedFileTypes(['audio/*', 'video/*'])
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
                    Select::make('categories')
                        ->label('Selecciona la/las Categoría(s)')
                        ->searchable()
                        ->multiple()
                        ->options(function () {
                            return Category::where('is_general', true)->orWhere('user_id', Auth::user()->id)
                                ->pluck('name', 'id');
                        })
                        ->preload(),
                    Select::make('sections')
                        ->label('Secciones a mostrar')
                        ->options(function () {
                            $options = [];

                            $sections = SectionEnum::cases();
        
                            foreach ($sections as $sectionsCase) {
                                $options[$sectionsCase->value] = SectionEnum::getTransformName($sectionsCase->value);
                            }

                            return $options;
                        })
                        ->multiple()
                        ->required(),
                ])->action(function (array $data): void {
                    try {
                        // ✅ 1) Ruta REAL del archivo en tu disco local (public)
                        $localPath = Storage::disk('public')->path($data['original_file']);

                        if (!file_exists($localPath)) {
                            throw new \Exception("No existe el archivo local: {$localPath}");
                        }

                        // ✅ 2) Subir a S3 el BINARIO (no la URL)
                        $stream = fopen($localPath, 'r');
                        Storage::disk('s3')->writeStream($data['original_file'], $stream);
                        if (is_resource($stream))
                            fclose($stream);

                        // ✅ 3) Crear registro principal
                        $file = new File();
                        $file->name = $data['name'] ?? basename($data['file'] ?? $data['original_file']);
                        $file->file = $data['file'];
                        $file->poster = $data['image'];
                        $file->original_file = $data['original_file'];
                        $file->user_id = Auth::user()->id;
                        $file->price = $data['price'] ?? 0;
                        $file->bpm = $data['bpm'];
                        $file->sections = $data['sections'];
                        $file->save();
                        $file->categories()->sync($data['categories']);

                        // ✅ 4) Si es ZIP, extraer desde el archivo local (public) y subir cada archivo a S3
                        if (strtolower(pathinfo($localPath, PATHINFO_EXTENSION)) === 'zip') {

                            $collection = new Collection();
                            $collection->name = $data['name'] ?? basename($data['file'] ?? $data['original_file']);
                            $collection->image = $data['image'];
                            $collection->user_id = Auth::user()->id;
                            $collection->save();

                            $zip = new \ZipArchive();

                            if ($zip->open($localPath) === true) {
                                Log::info('Entro al ZIP ' . $localPath);

                                $extractPath = storage_path('app/temp_' . uniqid());
                                if (!file_exists($extractPath)) {
                                    mkdir($extractPath, 0755, true);
                                }

                                $zip->extractTo($extractPath);
                                $zip->close();

                                $files = array_values(array_diff(scandir($extractPath), ['.', '..']));

                                // Solo archivos (no carpetas)
                                $onlyFiles = array_values(array_filter($files, function ($f) use ($extractPath) {
                                    return is_file($extractPath . DIRECTORY_SEPARATOR . $f);
                                }));

                                $fileCount = count($onlyFiles);
                                Log::info("Escaneados {$fileCount} archivos");

                                $filePrice = ($data['price'] && $data['price'] > 0 && $fileCount > 0)
                                    ? $data['price'] / $fileCount
                                    : 0;

                                foreach ($onlyFiles as $f) {
                                    $filePath = $extractPath . DIRECTORY_SEPARATOR . $f;

                                    Log::info("Subiendo archivo extraído: {$f}");

                                    // ✅ Extensión REAL del archivo dentro del ZIP
                                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                    $filePathInS3 = 'files/' . uniqid() . ($ext ? ".{$ext}" : '');

                                    $contentStream = fopen($filePath, 'r');
                                    Storage::disk('s3')->writeStream($filePathInS3, $contentStream);
                                    if (is_resource($contentStream))
                                        fclose($contentStream);

                                    $newFile = new File();
                                    $newFile->name = pathinfo($f, PATHINFO_FILENAME);
                                    $newFile->file = $filePathInS3;
                                    $newFile->original_file = $filePathInS3;
                                    $newFile->collection_id = $collection->id;
                                    $newFile->poster = $data['image'];
                                    $newFile->user_id = Auth::user()->id;
                                    $newFile->price = $filePrice;
                                    $newFile->bpm = $data['bpm'];
                                    $newFile->status = "inactive";
                                    $newFile->sections = $data['sections'];
                                    $newFile->save();
                                    $newFile->categories()->sync($data['categories']);

                                    Storage::disk('public')->delete($filePath);
                                }

                                // ✅ Limpiar temp
                                // foreach (glob($extractPath . DIRECTORY_SEPARATOR . '*') as $tempFile) {
                                //     if (is_file($tempFile))
                                //         unlink($tempFile);
                                // }
                                // if (is_dir($extractPath))
                                //     rmdir($extractPath);

                                Storage::disk('public')->delete($extractPath);

                            } else {
                                Notification::make()
                                    ->title('No se pudo abrir el archivo ZIP: ' . $localPath)
                                    ->danger()
                                    ->send();
                            }
                        }

                        // ✅ 5) Borrar el archivo local (public) ya que está en S3
                        Storage::disk('public')->delete($data['original_file']);

                    } catch (\Throwable $e) {
                        Log::error('Error subiendo archivo', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->title('Error subiendo archivo: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        throw $e;
                    }

                }),
        ];
    }
}
