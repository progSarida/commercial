<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->color('success'),
            $this->getCancelFormAction(),
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
                return BiddingResource::getUrl('index');
            });
    }

    protected function afterSave(): void
    {
        $this->handleZipUpload($this->record, $this->data);
    }

    protected static function handleZipUpload($record, array $data): void
    {
        // Se non c'è ZIP caricato o già processato → esci
        if (empty($data['temp_zip']) || $record->attachment_path) {
            return;
        }

        $zipPath = array_values($data['temp_zip'])[0];
        $fullZipPath = storage_path('app/public/' . $zipPath);

        if (!file_exists($fullZipPath)) {
            return;
        }

        // CARTELLA FINALE CON L'ID
        $extractPath = "biddings_attach/{$record->id}";
        Storage::disk('public')->makeDirectory($extractPath);

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath) === true) {
            $zip->extractTo(storage_path('app/public/' . $extractPath));
            $zip->close();

            // Cancella lo ZIP temporaneo
            Storage::disk('public')->delete($zipPath);

            // SALVA IL PERCORSO NEL DATABASE
            $record->update([
                'attachment_path' => $extractPath,
            ]);

            // Opzionale: svuota il campo temp così non riappare
            // (non serve se usi ->visible() sopra)
        }
    }
}
