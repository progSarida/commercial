<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBidding extends EditRecord
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Scorrimento in  base a data di scadenza gara
            Actions\Action::make('previous_deadline')
                ->label('Scadenza Prec.')
                ->color('success')
                ->icon('heroicon-o-arrow-left-circle')
                ->action(function () {
                    $currentBidding = $this->record;
                    $previousBidding = Bidding::where('deadline_date', '<', $currentBidding->deadline_date)
                        ->orderBy('deadline_date', 'desc')
                        ->first();
                    if ($previousBidding) {
                        $this->redirect(BiddingResource::getUrl('edit', ['record' => $previousBidding->id]));
                    } else {
                        Notification::make()
                            ->title('Nessuna scadenza precedente trovata')
                            ->warning()
                            ->send();
                    }
                }),
            Actions\Action::make('next_deadline')
                ->label('Scadenza Succ.')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->action(function () {
                    $currentBidding = $this->record;
                    $nextBidding = Bidding::where('deadline_date', '>', $currentBidding->deadline_date)
                        ->orderBy('deadline_date', 'asc')
                        ->first();
                    if ($nextBidding) {
                        $this->redirect(BiddingResource::getUrl('edit', ['record' => $nextBidding->id]));
                    } else {
                        Notification::make()
                            ->title('Nessuna scadenza successiva trovata')
                            ->warning()
                            ->send();
                    }
                }),
            // Scorrimento in base a data di sopralluogo
            Actions\Action::make('previous_inspection')
                ->label('Sopralluogo Prec.')
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn () => $this->record->inspection_deadline_date !== null)
                ->action(function () {
                    $currentBidding = $this->record;
                    $previousInspection = Bidding::whereNotNull('inspection_deadline_date')
                        ->where('inspection_deadline_date', '<', $currentBidding->inspection_deadline_date)
                        ->orderBy('inspection_deadline_date', 'desc')
                        ->first();
                    if ($previousInspection) {
                        $this->redirect(BiddingResource::getUrl('edit', ['record' => $previousInspection->id]));
                    } else {
                        Notification::make()
                            ->title('Nessun sopralluogo precedente trovato')
                            ->warning()
                            ->send();
                    }
                }),
            Actions\Action::make('next_inspection')
                ->label('Sopralluogo Succ.')
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn () => $this->record->inspection_deadline_date !== null)
                ->action(function () {
                    $currentBidding = $this->record;
                    $nextInspection = Bidding::whereNotNull('inspection_deadline_date')
                        ->where('inspection_deadline_date', '>', $currentBidding->inspection_deadline_date)
                        ->orderBy('inspection_deadline_date', 'asc')
                        ->first();
                    if ($nextInspection) {
                        $this->redirect(BiddingResource::getUrl('edit', ['record' => $nextInspection->id]));
                    } else {
                        Notification::make()
                            ->title('Nessun sopralluogo successivo trovato')
                            ->warning()
                            ->send();
                    }
                }),
            // Cancellazione gara
            Actions\DeleteAction::make(),
        ];
    }
}
