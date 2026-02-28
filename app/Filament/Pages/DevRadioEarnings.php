<?php

namespace App\Filament\Pages;

use App\Enums\SectionEnum;
use App\Filament\Widgets\DevRadioStatsWidget;
use App\Filament\Widgets\RadioCUPSalesByMonthChart;
use App\Filament\Widgets\RadioSalesByMonthChart;
use App\Livewire\DevRadioCUPSalesTable;
use App\Models\Order;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;

class DevRadioEarnings extends Page implements HasTable
{
    use InteractsWithTable;

    public ?string $month = null;
    public ?string $year = null;

    protected string $view = 'filament.pages.dev-radio-earnings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Radio;

    protected static ?string $slug = 'dev-radio-earnings';

    protected static ?string $navigationLabel = 'Ganancias Emisora';

    protected static ?string $title = 'Ganancias Emisora';

    protected static ?int $navigationSort = 3;

    public function mount(): void
    {
        $this->month = request()->query('month', $this->month);
        $this->year = request()->query('year', $this->year);
    }

    public function getFilterKey(): string
    {
        return md5($this->month . $this->year);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role==='developer';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role==='developer';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filtrar por Mes/Año')
                ->icon('heroicon-o-funnel')
                ->modal()
                ->form([
                    Select::make('month')
                        ->label('Mes de venta') 
                        ->options([ 
                            '01' => 'Enero', 
                            '02' => 'Febrero', 
                            '03' => 'Marzo', 
                            '04' => 'Abril', 
                            '05' => 'Mayo', 
                            '06' => 'Junio', 
                            '07' => 'Julio', 
                            '08' => 'Agosto', 
                            '09' => 'Septiembre', 
                            '10' => 'Octubre', 
                            '11' => 'Noviembre', 
                            '12' => 'Diciembre', 
                        ]), 
                    Select::make('year') 
                        ->label('Año de venta') 
                        ->options(
                            array_combine( 
                                range(date('Y'), date('Y') - 10), 
                                range(date('Y'), date('Y') - 10) 
                            )
                        ),
                ])
                ->action(function (array $data): void {
                    $this->redirect(
                        route('filament.admin.pages.dev-radio-earnings', [
                            'month' => $data['month'] ?? null,
                            'year' => $data['year'] ?? null,
                        ])
                    );
                })
                ->modalSubmitActionLabel('Aplicar Filtro')
                ->modalWidth('md'),
            Action::make('clear')
                ->label('Limpiar Filtros')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->action(function (): void {
                    $this->redirect(route('filament.admin.pages.dev-radio-earnings'));
                })
                ->visible(fn() => $this->month || $this->year),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->formatStateUsing(function($state){
                        Carbon::setLocale('es');
                        return Carbon::parse($state)->translatedFormat('j \d\e F \d\e Y');
                    }),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->default('Anónimo'),
                TextColumn::make('file.name')
                    ->label('Archivo')
                    ->default(fn($record) => $record->order_items->first()->file->name ?? 'Desconocido'),
                TextColumn::make('amount')
                    ->prefix('$ ')
                    ->label('Precio'),
                TextColumn::make('comision')
                    ->prefix('$ ')
                    ->label('Comisión')
                    ->default(fn($record) => $record->amount * 0.1),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll(null)
            ->heading('Ventas en USD')
            ->emptyStateIcon(Heroicon::MusicalNote)
            ->emptyStateHeading('Sin coincidendias')
            ->emptyStateDescription('No se han registrado ventas que cumplan con los filtros.');
    }

    protected function getTableQuery()
    {
        $query = /*Sale::query()->whereHas('file', function($q){
            $q->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
        });*/ Order::query()->where('status','paid')
            ->where('currency', 'USD')
            ->whereHas('order_items', function($q){
                $q->whereHas('file', function($q){
                    $q->whereJsonContains('sections', SectionEnum::CUBANDJS->value)->orWhereJsonContains('sections', SectionEnum::CUBANDJS_LIVE_SESSIONS->value);
                });
            });

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return $query;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DevRadioStatsWidget::class,
            DevRadioCUPSalesTable::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RadioSalesByMonthChart::class,
            RadioCUPSalesByMonthChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }

    public function getWidgetData(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}