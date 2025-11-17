<?php

namespace App\Filament\User\Resources\TenderResource\Pages;

use App\Filament\User\Resources\TenderResource;
use App\Models\Bidding;
use App\Models\Tender;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditTender extends EditRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        // Ottengo l'ID della Bidding collegata al Tender corrente
        $currentBiddingId = $this->record->bidding_id;

        // Se non c'è una Bidding collegata, non ha senso mostrare i pulsanti di navigazione
        if (!$currentBiddingId) {
            return [
                Actions\DeleteAction::make(),
            ];
        }

        return [
            // Scorrimento in base a data di scadenza Gara (Bidding)
            Actions\Action::make('previous_deadline')
                ->label('Scadenza Prec.')
                ->color('success')
                ->icon('heroicon-o-arrow-left-circle')
                ->action(function (Tender $record) {
                    $currentBidding = $record->bidding;
                    if (!$currentBidding) return;

                    // Trova la Bidding precedente
                    $previousBidding = Bidding::where('deadline_date', '<', $currentBidding->deadline_date)
                        ->orderBy('deadline_date', 'desc')
                        ->first();

                    if ($previousBidding) {
                        // Trova il Tender collegato alla Bidding precedente
                        $previousTender = Tender::where('bidding_id', $previousBidding->id)->first();

                        if ($previousTender) {
                            $this->redirect(TenderResource::getUrl('edit', ['record' => $previousTender->id]));
                            return;
                        }
                    }
                    Notification::make()
                        ->title('Nessun appalto trovato per una scadenza precedente')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('next_deadline')
                ->label('Scadenza Succ.')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->action(function (Tender $record) {
                    $currentBidding = $record->bidding;
                    if (!$currentBidding) return;
                    
                    // Trova la Bidding successiva
                    $nextBidding = Bidding::where('deadline_date', '>', $currentBidding->deadline_date)
                        ->orderBy('deadline_date', 'asc')
                        ->first();
                        
                    if ($nextBidding) {
                        // Trova il Tender collegato alla Bidding successiva
                        $nextTender = Tender::where('bidding_id', $nextBidding->id)->first();

                        if ($nextTender) {
                            $this->redirect(TenderResource::getUrl('edit', ['record' => $nextTender->id]));
                            return;
                        }
                    }
                    
                    Notification::make()
                        ->title('Nessun appalto trovato per una scadenza successiva')
                        ->warning()
                        ->send();
                }),

            // Scorrimento in base a data di sopralluogo Gara (Bidding)
            Actions\Action::make('previous_inspection')
                ->label('Sopralluogo Prec.')
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                // L'azione è visibile solo se la Bidding correlata ha una data di sopralluogo
                ->visible(fn (Tender $record) => $record->bidding?->inspection_deadline_date !== null)
                ->action(function (Tender $record) {
                    $currentBidding = $record->bidding;
                    if (!$currentBidding || $currentBidding->inspection_deadline_date === null) return;
                    
                    // Trova la Bidding precedente per data sopralluogo
                    $previousInspection = Bidding::whereNotNull('inspection_deadline_date')
                        ->where('inspection_deadline_date', '<', $currentBidding->inspection_deadline_date)
                        ->orderBy('inspection_deadline_date', 'desc')
                        ->first();
                        
                    if ($previousInspection) {
                        // Trova il Tender collegato alla Bidding precedente
                        $previousTender = Tender::where('bidding_id', $previousInspection->id)->first();
                        
                        if ($previousTender) {
                            $this->redirect(TenderResource::getUrl('edit', ['record' => $previousTender->id]));
                            return;
                        }
                    }
                    
                    Notification::make()
                        ->title('Nessun appalto trovato per un sopralluogo precedente')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('next_inspection')
                ->label('Sopralluogo Succ.')
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                // L'azione è visibile solo se la Bidding correlata ha una data di sopralluogo
                ->visible(fn (Tender $record) => $record->bidding?->inspection_deadline_date !== null)
                ->action(function (Tender $record) {
                    $currentBidding = $record->bidding;
                    if (!$currentBidding || $currentBidding->inspection_deadline_date === null) return;

                    // Trova la Bidding successiva per data sopralluogo
                    $nextInspection = Bidding::whereNotNull('inspection_deadline_date')
                        ->where('inspection_deadline_date', '>', $currentBidding->inspection_deadline_date)
                        ->orderBy('inspection_deadline_date', 'asc')
                        ->first();
                        
                    if ($nextInspection) {
                        // Trova il Tender collegato alla Bidding successiva
                        $nextTender = Tender::where('bidding_id', $nextInspection->id)->first();

                        if ($nextTender) {
                            $this->redirect(TenderResource::getUrl('edit', ['record' => $nextTender->id]));
                            return;
                        }
                    }
                    
                    Notification::make()
                        ->title('Nessun appalto trovato per un sopralluogo successivo')
                        ->warning()
                        ->send();
                }),

            // Cancellazione gara
            Actions\DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
