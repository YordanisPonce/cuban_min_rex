<?php

namespace App\Filament\Pages;

use App\Livewire\TabsWidget;
use App\Models\Download;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Services\PaypalService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
class Liquidations extends Page
{
    protected string $view = 'filament.pages.liquidations';

    protected static ?string $title = 'Liquidaciones';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::CurrencyDollar;

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Procesar Pagos')
                ->color('success')
                ->icon('heroicon-m-currency-dollar')
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-currency-dollar')
                ->modalHeading('Confirmar Pago')
                ->modalDescription('Resumen (informativo) de lo que se pagará a cada DJ:')
                ->modalContent(function () {

                    $gross = (float) \App\Models\Order::query()
                        ->where('status', 'paid')
                        ->whereNotNull('plan_id')
                        ->whereNull('settled_at')
                        ->sum('amount');

                    $pool = $gross * 0.70;

                    $totalPairs = (int) \App\Models\Download::query()
                        ->where('liquidated', false)
                        ->selectRaw('COUNT(DISTINCT user_id, file_id) as cnt')
                        ->value('cnt');

                    $pairsByDj = \App\Models\Download::query()
                        ->join('files', 'downloads.file_id', '=', 'files.id')
                        ->where('downloads.liquidated', false)
                        ->groupBy('files.user_id')
                        ->selectRaw('files.user_id as dj_id, COUNT(DISTINCT downloads.user_id, downloads.file_id) as cnt')
                        ->pluck('cnt', 'dj_id'); // [dj_id => cnt]
        
                    $salesByDj = \App\Models\Sale::query()
                        ->join('files', 'sales.file_id', '=', 'files.id')
                        ->where('sales.status', 'pending')
                        ->groupBy('files.user_id')
                        ->selectRaw('files.user_id as dj_id, COALESCE(SUM(sales.user_amount),0) as total')
                        ->pluck('total', 'dj_id'); // [dj_id => total]
        
                    $djs = \App\Models\User::query()
                        ->whereNot('role', 'user')
                        ->select('id', 'name')
                        ->get();

                    // Ventas pendientes: count por DJ
                    $salesCountByDj = \App\Models\Sale::query()
                        ->join('files', 'sales.file_id', '=', 'files.id')
                        ->where('sales.status', 'pending')
                        ->groupBy('files.user_id')
                        ->selectRaw('files.user_id as dj_id, COUNT(*) as cnt')
                        ->pluck('cnt', 'dj_id'); // [dj_id => cnt]
        
                    // Suscripción: pares únicos (usuario,canción) pendientes por DJ
                    $pairsByDj = \App\Models\Download::query()
                        ->join('files', 'downloads.file_id', '=', 'files.id')
                        ->where('downloads.liquidated', false)
                        ->groupBy('files.user_id')
                        ->selectRaw('files.user_id as dj_id, COUNT(DISTINCT downloads.user_id, downloads.file_id) as cnt')
                        ->pluck('cnt', 'dj_id'); // [dj_id => cnt]
        

                    $items = [];

                    foreach ($djs as $dj) {
                        $sales = (float) ($salesByDj[$dj->id] ?? 0);
                        $djPairs = (int) ($pairsByDj[$dj->id] ?? 0);
                        $salesCount = (int) ($salesCountByDj[$dj->id] ?? 0);
                        $pairsCount = (int) ($pairsByDj[$dj->id] ?? 0);


                        $subs = ($pool > 0 && $totalPairs > 0)
                            ? (float) ($pool * ($djPairs / $totalPairs))
                            : 0.0;

                        $total = round($sales + $subs, 2);
                        if ($total <= 0)
                            continue;

                        $items[] = [
                            'name' => $dj->name,
                            'total' => $total,
                            'sales_count' => $salesCount,
                            'pairs_count' => $pairsCount,
                        ];
                    }

                    usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

                    return view('filament.liquidations.dj-labels', [
                        'items' => $items,
                    ]);
                })
                ->modalSubmitActionLabel('Sí, proceder a pagar')
                ->modalCancelActionLabel('No, cancelar')
                ->action(function () {

                    $users = User::whereNot('role', 'user')->get();
                    $paypal = new PaypalService();

                    foreach ($users as $record) {

                        $salesLiquidation = (float) $record->pendingSalesTotal();
                        $subscriptionLiquidation = (float) $record->pendingSubscriptionLiquidation();
                        $total = $salesLiquidation + $subscriptionLiquidation;

                        if ($total <= 0) {
                            continue;
                        }

                        // 1) SIEMPRE crear Payment en pending
                        $payment = Payment::create([
                            'user_id' => $record->id,
                            'status' => 'pending',
                            'amount' => $total,
                            'currency' => 'USD',
                            'email' => $record->paypal_email,
                            'note' => 'Liquidación (ventas + suscripción) - CubanPool ' . now()->month . ' del año ' . now()->year . '.',
                        ]);

                        // 2) Intentar PayPal (si no tiene email, queda pending/failed igual)
                        try {
                            if (!$record->paypal_email) {
                                throw new \Exception('El usuario no tiene definido un Correo de PayPal.');
                            }

                            $response = $paypal->sendPayout(
                                $record->paypal_email,
                                $total,
                                'USD',
                                $payment->note
                            );

                            $payment->update([
                                'status' => 'succeeded',
                                'paid_at' => now(),
                                'paypal_response' => $response['paypal_response'] ?? null,
                                'item_id' => $response['item_id'] ?? null,
                                'sender_batch_id' => $response['sender_batch_id'] ?? null,
                                'currency' => $response['currency'] ?? 'USD',
                                'email' => $response['email'] ?? $record->paypal_email,
                                'note' => $response['note'] ?? $payment->note,
                                'error_message' => null,
                            ]);

                        } catch (\Throwable $th) {
                            $payment->update([
                                'status' => 'failed',      // o déjalo pending si prefieres
                                'error_message' => $th->getMessage(),
                            ]);

                            Notification::make()
                                ->title("Pago pendiente/fallido para {$record->name}: {$th->getMessage()}")
                                ->danger()
                                ->send();
                        }

                        // 3) Cerrar ventas SIEMPRE (porque ya quedó el Payment registrado)
                        Sale::query()
                            ->where('status', 'pending')
                            ->whereHas('file', fn($q) => $q->where('user_id', $record->id))
                            ->update(['status' => 'paid']);
                    }

                    // 4) Cerrar suscripciones SIEMPRE (global)
                    Order::query()
                        ->where('status', 'paid')
                        ->whereNotNull('plan_id')
                        ->whereNull('settled_at')
                        ->update(['settled_at' => now()]);

                    Download::query()
                        ->where('liquidated', false)
                        ->update(['liquidated' => true]);

                    Notification::make()
                        ->title('Proceso terminado. Revisa Payments FAILED/PENDING para reintentar.')
                        ->success()
                        ->send();
                })
            ,
            Action::make('Repartir')
                ->label('Repartir')
                ->color('warning')
                ->icon('heroicon-m-arrows-right-left')
                ->requiresConfirmation()
                ->modalHeading('Repartir (suscripciones 70% + ventas)')
                ->modalDescription('Preview informativo antes de generar los payments.')
                ->modalSubmitActionLabel('Sí, repartir')
                ->modalCancelActionLabel('No, cancelar')
                ->form([
                    TextInput::make('gross')
                        ->label('Gross ingresado (USD)')
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->live(),

                    Toggle::make('gross_includes_sales')
                        ->label('Este gross incluye ventas')
                        ->default(false)
                        ->live(),

                TextEntry::make('preview')
                        ->label('Resumen por DJ')
                        ->state(function (Get $get) {
                            $gross = (float) ($get('gross') ?? 0);
                            if ($gross <= 0) {
                                return new HtmlString('<div style="font-size:14px;color:#6b7280;">Introduce un gross para ver el reparto.</div>');
                            }

                            $salesGrossPending = (float) \App\Models\Sale::query()
                                ->where('status', 'pending')
                                ->sum('amount');

                            $grossSubs = ($get('gross_includes_sales') ?? false)
                                ? max(0, $gross - $salesGrossPending)
                                : $gross;

                            $poolSubs = $grossSubs * 0.70;

                            $totalPairs = (int) \App\Models\Download::query()
                                ->where('liquidated', false)
                                ->selectRaw('COUNT(DISTINCT user_id, file_id) as cnt')
                                ->value('cnt');

                            $pairsByDj = \App\Models\Download::query()
                                ->join('files', 'downloads.file_id', '=', 'files.id')
                                ->where('downloads.liquidated', false)
                                ->groupBy('files.user_id')
                                ->selectRaw('files.user_id as dj_id, COUNT(DISTINCT downloads.user_id, downloads.file_id) as cnt')
                                ->pluck('cnt', 'dj_id');

                            $salesNetByDj = \App\Models\Sale::query()
                                ->join('files', 'sales.file_id', '=', 'files.id')
                                ->where('sales.status', 'pending')
                                ->groupBy('files.user_id')
                                ->selectRaw('files.user_id as dj_id, COALESCE(SUM(sales.user_amount),0) as total')
                                ->pluck('total', 'dj_id');

                            $salesCountByDj = \App\Models\Sale::query()
                                ->join('files', 'sales.file_id', '=', 'files.id')
                                ->where('sales.status', 'pending')
                                ->groupBy('files.user_id')
                                ->selectRaw('files.user_id as dj_id, COUNT(*) as cnt')
                                ->pluck('cnt', 'dj_id');

                            $djs = \App\Models\User::query()
                                ->whereNot('role', 'user')
                                ->select('id', 'name')
                                ->get();

                            $items = [];
                            foreach ($djs as $dj) {
                                $djPairs = (int) ($pairsByDj[$dj->id] ?? 0);

                                $subs = ($poolSubs > 0 && $totalPairs > 0 && $djPairs > 0)
                                    ? (float) ($poolSubs * ($djPairs / $totalPairs))
                                    : 0.0;

                                $sales = (float) ($salesNetByDj[$dj->id] ?? 0);
                                $total = round($subs + $sales, 2);

                                if ($total <= 0)
                                    continue;

                                $items[] = [
                                    'name' => $dj->name,
                                    'sales_count' => (int) ($salesCountByDj[$dj->id] ?? 0),
                                    'pairs' => $djPairs,
                                    'total' => $total,
                                ];
                            }

                            usort($items, fn($a, $b) => $b['total'] <=> $a['total']);

                            // Render tabla inline compacta (similar a la que ya te quedó bonita)
                            $html = '<div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;width:100%;max-width:720px;margin:0 auto;">';
                            $html .= '<table style="width:100%;border-collapse:collapse;font-size:13px;table-layout:fixed;">';
                            $html .= '<colgroup><col style="width:40%"><col style="width:20%"><col style="width:40%"></colgroup>';
                            $html .= '<thead style="background:#f9fafb;"><tr>';
                            $html .= '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">DJ</th>';
                            $html .= '<th style="padding:8px 12px;text-align:right;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;white-space:nowrap;">Monto</th>';
                            $html .= '<th style="padding:8px 12px;text-align:left;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Detalle</th>';
                            $html .= '</tr></thead><tbody>';

                            foreach ($items as $it) {
                                $html .= '<tr>';
                                $html .= '<td style="padding:7px 12px;border-bottom:1px solid #f3f4f6;">' . e($it['name']) . '</td>';
                                $html .= '<td style="padding:7px 12px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:800;white-space:nowrap;">$' . number_format($it['total'], 2) . '</td>';
                                $html .= '<td style="padding:7px 12px;border-bottom:1px solid #f3f4f6;">'
                                    . number_format($it['sales_count']) . ' ventas · '
                                    . number_format($it['pairs']) . ' descargas (subs)'
                                    . '</td>';
                                $html .= '</tr>';
                            }

                            $html .= '</tbody></table></div>';

                            return new HtmlString($html);
                        })
                        ->columnSpanFull(),
                ])
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TabsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
