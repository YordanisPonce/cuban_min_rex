<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use App\Models\Collection;
use App\Models\File;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
                    FileUpload::make('file')
                        ->label('Cargar archivo ZIP')
                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                        ->maxSize(20480)
                        ->required()
                        ->disk('public')
                        ->directory('files/zip')
                        ->downloadable()
                        ->preserveFilenames()
                        ->columnSpanFull(),
                    Select::make('collection_id')
                        ->label('Selecciona una Colección')
                        ->required()
                        ->options(function () {
                            return Collection::where('user_id', Auth::user()->id)
                                ->pluck('name', 'id');
                        }),
                ])->action(function (array $data): void {
                    $file = new File();
                    $file->name = basename('public/files'.$data['file']);
                    $file->file = $data['file'];
                    $file->collection_id = $data['collection_id'];
                    $file->user_id = Auth::user()->id;
                    $file->save();

                    $zip = new ZipArchive;
                    $path = storage_path('app/public/' . $file->file);

                    if ($zip->open($path) === TRUE) {
                        
                        $extractPath = storage_path('app/temp');
                        $zip->extractTo($extractPath);
                        $zip->close();

                        $files = scandir($extractPath);

                        foreach ($files as $f) {
                            if ($f !== '.' && $f !== '..') {
                                Storage::disk('public')->putFileAs('files', new \Illuminate\Http\File($extractPath . '/' . $f), $f);
                                $file = new File();
                                $file->name = $f;
                                $file->file = 'files/'.$f;
                                $file->collection_id = $data['collection_id'];
                                $file->user_id = Auth::user()->id;
                                $file->save();
                            }
                        }

                        array_map('unlink', glob("$extractPath/*.*"));
                        rmdir($extractPath);
                    } else {
                        throw new \Exception('No se pudo abrir el archivo ZIP.');
                    }
                }),

                CreateAction::make()
                    ->label('Nuevo archivo'),
            ];
        }
}
