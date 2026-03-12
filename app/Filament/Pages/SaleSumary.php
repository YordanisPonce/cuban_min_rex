<?php

namespace App\Filament\Pages;

use App\Enums\SectionEnum;
use App\Livewire\DownloadsSummaryTable;
use App\Models;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SaleSumary extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected string $view = 'filament.pages.sale-sumary';
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Resumen';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Resumen de ventas y descargas';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role == 'admin' || auth()->user()?->role == 'worker';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role == 'admin' || auth()->user()?->role == 'worker';
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('file.id')->label('Id del archivo')->searchable()->default('-'),
                Tables\Columns\TextColumn::make('file.name')->label('Nombre del archivo')->searchable()
                ->default(function ($record) {
                    if ($record->file) {
                        return $record->file->name;
                    } elseif ($record->playlist) {
                        return 'Playlist: '.$record->playlist->name;
                    } elseif ($record->playlistItem) {
                        return 'Audio: '.$record->playlistItem->title;
                    }
                    return '-';
                }),
                Tables\Columns\TextColumn::make('files.categories')
                    ->label('Categorías')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->file->categories->pluck('name');
                    })
                    ->default('Sin categoría'),
                Tables\Columns\TextColumn::make('customer_email')->label('Cliente')->default('Usuario Anónimo'),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de venta')->formatStateUsing(function($state) {
                    Carbon::setLocale('es');
                    return Carbon::parse($state)->translatedFormat('j \d\e F \d\e Y');
                }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file.categories')
                    ->label('Categoría')
                    ->relationship('file.categories', 'name')
                    ->optionsLimit(100)
                    ->multiple(),
            ])
            ->recordActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->poll(null)
            ->heading('Ventas realiazadas')
            ->description('Aquí puedes ver un resumen de las ventas realizadas.')
            ->emptyStateHeading('No se han realizado ventas')
            ->emptyStateDescription('Aún no se han realizado ventas. ¡Empieza a vender tus archivos para que aparezcan aquí!')
            ->searchPlaceholder('Buscar por ID o Nombre')
            ->defaultPaginationPageOption('5')
            ->modifyQueryUsing(
                fn($query) => auth()->user()->role!=='admin' ? $query->whereHas('file', function($q) { $q->where('user_id', auth()->user()->id);}) : $query
            );
    }

    protected function getTableQuery()
    {
        $query = Models\Sale::query();

        $query = auth()->user()->pendingSales();

        return $query->orderBy('created_at', 'desc');
    }

    public function getFooterWidgets(): array
    {
        return [
            DownloadsSummaryTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
