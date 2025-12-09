<?php

namespace App\Filament\User\Resources\TenderResource\Pages;

use App\Filament\User\Resources\TenderResource;
use App\Models\Bidding;
use App\Models\Tender;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditTender extends EditRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        $currentBiddingId = $this->record->bidding_id;
        // Se non c'è una Bidding collegata, non ha senso mostrare i pulsanti di navigazione
        if (!$currentBiddingId) { return []; }
        $currentBidding = $this->record->bidding;
        if (!$currentBidding) { return []; }
        // Precedente per deadline_date: data precedente O stessa data con ID minore
        $previousBidding = Bidding::where(function ($query) use ($currentBidding) {
                $query->where('deadline_date', '<', $currentBidding->deadline_date)
                    ->orWhere(function ($q) use ($currentBidding) {
                        $q->where('deadline_date', '=', $currentBidding->deadline_date)
                          ->where('id', '<', $currentBidding->id);
                    });
            })
            ->orderBy('deadline_date', 'desc')->orderBy('id', 'desc')->first();
        $previousTender = $previousBidding ? Tender::where('bidding_id', $previousBidding->id)->first() : null;
        // Successivo per deadline_date: data successiva O stessa data con ID maggiore
        $nextBidding = Bidding::where(function ($query) use ($currentBidding) {
                $query->where('deadline_date', '>', $currentBidding->deadline_date)
                    ->orWhere(function ($q) use ($currentBidding) {
                        $q->where('deadline_date', '=', $currentBidding->deadline_date)
                          ->where('id', '>', $currentBidding->id);
                    });
            })
            ->orderBy('deadline_date', 'asc')->orderBy('id', 'asc')->first();
        $nextTender = $nextBidding ? Tender::where('bidding_id', $nextBidding->id)->first() : null;
        // Precedente per inspection_deadline_date: data precedente O stessa data con ID minore
        $previousInspectionBidding = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '<', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '<', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'desc')->orderBy('id', 'desc')->first();
        $previousInspectionTender = $previousInspectionBidding ? Tender::where('bidding_id', $previousInspectionBidding->id)->first() : null;
        // Successivo per inspection_deadline_date: data successiva O stessa data con ID maggiore
        $nextInspectionBidding = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '>', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '>', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'asc')->orderBy('id', 'asc')->first();
        $nextInspectionTender = $nextInspectionBidding ? Tender::where('bidding_id', $nextInspectionBidding->id)->first() : null;

        return [
            // Scorrimento in base a data di scadenza Gara (Bidding)
            Actions\Action::make('previous_deadline')
                ->label('Scadenza Prec.')
                ->color('success')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $previousTender !== null)
                ->action(fn() => $this->redirect(TenderResource::getUrl('edit', ['record' => $previousTender->id]))),

            Actions\Action::make('next_deadline')
                ->label('Scadenza Succ.')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $nextTender !== null)
                ->action(fn() => $this->redirect(TenderResource::getUrl('edit', ['record' => $nextTender->id]))),

            // Scorrimento in base a data di sopralluogo Gara (Bidding)
            Actions\Action::make('previous_inspection')
                ->label('Sopralluogo Prec.')
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $previousInspectionTender !== null)
                ->action(fn() => $this->redirect(TenderResource::getUrl('edit', ['record' => $previousInspectionTender->id]))),

            Actions\Action::make('next_inspection')
                ->label('Sopralluogo Succ.')
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $nextInspectionTender !== null)
                ->action(fn() => $this->redirect(TenderResource::getUrl('edit', ['record' => $nextInspectionTender->id]))),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->color('success'),
            $this->getCancelFormAction(),
            $this->getResetFormAction(),
            $this->getDeleteFormAction()
                ->extraAttributes([
                    'class' => ' md:ml-auto md:w-auto ',
                ]),
        ];
    }

    protected function getDeleteFormAction()
    {
        return Actions\DeleteAction::make('delete')
                ->requiresConfirmation()
                ->modalHeading('Conferma eliminazione contatto')
                ->modalDescription('Sei sicuro di voler eliminare questo contatto? Questa azione non può essere annullata.')
                ->modalSubmitActionLabel('Elimina')
                ->modalCancelActionLabel('Annulla');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return Actions\Action::make('cancel')
            ->label('Indietro')
            ->color('gray')
            ->url(function () {
                if ($this->previousUrl && str($this->previousUrl)->contains('/contacts?')) {
                    return $this->previousUrl;
                }
                return TenderResource::getUrl('index');
            });
    }

    protected function getResetFormAction(): Actions\Action
    {
        return Actions\Action::make('reset')
            ->label('Annulla')
            ->color('gray')
            ->action(function () {
                $this->data = $this->getRecord()->toArray();
                $this->fillForm();
            });
    }
}
