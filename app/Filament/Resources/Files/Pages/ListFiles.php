<?php

namespace App\Filament\Resources\Files\Pages;

use App\Filament\Resources\Files\FileResource;
use App\Models\Category;
use App\Models\Collection;
use App\Models\File;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
                        ->acceptedFileTypes(['audio/mpeg','application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                        ->maxSize(512000)
                        ->required()
                        ->disk('public')
                        ->directory('files')
                        ->downloadable()
                        ->preserveFilenames()
                        ->columnSpanFull(),
                    Select::make('collection_id')
                        ->label('Selecciona una Colección')
                        ->options(function () {
                            return Collection::where('user_id', Auth::user()->id)
                                ->pluck('name', 'id');
                        })->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (Collection::find($state)) {
                                $set('dinamic_category_id', Collection::find($state)->category->id);
                                $set('category_id', Collection::find($state)->category->id);
                            } else {
                                $set('dinamic_category_id', $state);
                            }
                        }),
                    Select::make('dinamic_category_id')
                        ->label('Selecciona una Categoría')
                        ->options(function () {
                            return Category::where('is_general', true)->orWhere('user_id', Auth::user()->id)
                                ->pluck('name', 'id');
                        })
                        ->disabled(fn($get) => $get('collection_id') !== null)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('category_id', $state);
                        }),
                    Hidden::make('category_id')
                        ->default(fn($get) => $get('dinamic_category_id')),
                ])->action(function (array $data): void {
                    $file = new File();
                    $file->name = basename('public/files' . $data['file']);
                    $file->file = $data['file'];
                    $file->collection_id = $data['collection_id'];
                    $file->category_id = $data['category_id'];
                    $file->user_id = Auth::user()->id;
                    $file->save();

                    if(pathinfo('storage/public/files/' . $file->file, PATHINFO_EXTENSION) === 'zip'){
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
                                    $file->file = 'files/' . $f;
                                    $file->collection_id = $data['collection_id'];
                                    $file->category_id = $data['category_id'];
                                    $file->user_id = Auth::user()->id;
                                    $file->save();
                                }
                            }

                            array_map('unlink', glob("$extractPath/*.*"));
                            rmdir($extractPath);
                        } else {
                            throw new \Exception('No se pudo abrir el archivo ZIP.');
                        }
                    }
                }),

            CreateAction::make()
                ->label('Nuevo archivo'),
        ];
    }
}
