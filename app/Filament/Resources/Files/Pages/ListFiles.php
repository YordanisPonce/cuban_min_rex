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
use Filament\Forms\Components\TextInput;
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
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required(),
                    FileUpload::make('file')
                        ->label('Cargar archivo')
                        ->acceptedFileTypes(['audio/*','video/*','application/zip', 'application/x-zip-compressed', 'application/x-zip', 'multipart/x-zip'])
                        ->required()
                        ->disk('s3')
                        ->directory('files')
                        ->downloadable()
                        ->preserveFilenames()
                        ->columnSpanFull(),
                    FileUpload::make('image')
                        ->label('Subir Foto del Pack')
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
                    $file->name = $data['name'] ?? basename( Storage::disk('s3')->url($data['file']));
                    $file->file = $data['file'];
                    //$file->collection_id = $data['collection_id'];
                    $file->category_id = $data['category_id'];
                    $file->user_id = Auth::user()->id;
                    $file->price = $data['price'] ?? 0;
                    $file->bpm = $data['bpm'];
                    $file->save();

                    if(pathinfo(Storage::disk('s3')->url($file->url ?? $file->file), PATHINFO_EXTENSION) === 'zip'){

                        $collection = new Collection();
                        $collection->name = $data['name'] ?? basename( Storage::disk('s3')->url($data['file']));
                        $collection->category_id = $data['category_id'];
                        $collection->image = $data['image'];
                        $collection->user_id = Auth::user()->id;
                        $collection->save();

                        $zip = new ZipArchive;
                        $path = Storage::disk('s3')->url($file->url ?? $file->file);

                        if ($zip->open($path) === TRUE) {

                            $extractPath = storage_path('app/temp');
                            $zip->extractTo($extractPath);
                            $zip->close();

                            $files = scandir($extractPath);

                            $filePrice = $data['price'] ? $data['price']/$file->count() : 0;

                            foreach ($files as $f) {
                                if ($f !== '.' && $f !== '..') {
                                    Storage::disk('s3')->putFileAs('files', new \Illuminate\Http\File($extractPath . '/' . $f), $f);
                                    $file = new File();
                                    $file->name = $f;
                                    $file->file = 'files/' . $f;
                                    $file->collection_id = $collection->id;
                                    $file->category_id = $data['category_id'];
                                    $file->user_id = Auth::user()->id;
                                    $file->price = $filePrice;
                                    $file->bpm = $data['bpm'];
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
        ];
    }
}
