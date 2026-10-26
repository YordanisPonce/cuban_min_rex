<?php

namespace App\Livewire;

use App\Models\Download;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class UserDownloadsTable extends TableWidget
{
    /**
     * ✅ Este valor debe venir desde la Page (o desde query string si quieres).
     */
    public ?int $userId = null;

    /**
     * (Opcional) si tu page tiene un "record" y quieres seguir usando session por record.
     */
    public ?string $recordKey = null;

    public function mount(): void
    {
        // ✅ Si viene userId, lo persistimos (opcional) por recordKey para que no se pierda
        if ($this->recordKey) {
            $key = "current_user_id_" . auth()->id() . "_" . $this->recordKey;

            if ($this->userId) {
                session([$key => $this->userId]);
            } else {
                $this->userId = session($key);
            }
        }

        // ✅ Fallback final: si no vino nada, intenta cogerlo de la URL (?userId=)
        $this->userId ??= request()->integer('userId');
    }

    public function table(Table $table): Table
    {
        $userId = (int) $this->userId;

        return $table
            ->paginated(false)
            ->query(
                fn(): Builder => Download::query()
                    ->where('user_id', $userId)
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('created_at')->label('Fecha')->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y')),
                TextColumn::make('name')->label('Archivo Descargado')->default(function($record){
                    if ($record->file) {
                        return $record->file->name;
                    } 
                    
                    if($record->playlistItem){
                        return $record->playlistItem->title;
                    }

                    return 'Desconocido';
                }),
                TextColumn::make('artist')->label('DJ')->default(function($record){
                    if ($record->file) {
                        return $record->file->user->name;
                    } 
                    
                    if($record->playlistItem){
                        return $record->playlistItem->playlist->user->name;
                    }

                    return 'Desconocido';
                }),
            ])
            ->recordActions([
                //
            ])
            ->paginated()
            ->defaultPaginationPageOption(5)
            ->heading(
                fn() => $userId
                ? ('Descargas Realizadas por ' . (User::find($userId)->name ?? 'Usuario'))
                : 'Descargas Realizadas'
            );
    }
}
