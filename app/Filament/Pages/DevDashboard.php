<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SalesByMonthChart;
use App\Models\Sale;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;

class DevDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    public ?string $month = null;
    public ?string $year = null;

    protected string $view = 'filament.pages.dev-dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static ?string $slug = 'dev-dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Developer Dashboard';

    protected static ?int $navigationSort = 1;

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
                ->label('Filtrar por Fecha')
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
                        route('filament.admin.pages.dev-dashboard', [
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
                    $this->redirect(route('filament.admin.pages.dev-dashboard'));
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
                        return Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y');
                    }),
                TextColumn::make('file.name')
                    ->label('Archivo Vendido'),
                TextColumn::make('amount')
                    ->prefix('$ ')
                    ->label('Precio'),
                TextColumn::make('comision')
                    ->prefix('$ ')
                    ->label('Comisión')
                    ->default(fn($record) => $record->amount * 0.2),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->poll(null)
            ->heading('Ventas');
    }

    protected function getTableQuery()
    {
        $query = Sale::query();

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
           //
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SalesByMonthChart::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getWidgetData(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
        ];
    }
}
