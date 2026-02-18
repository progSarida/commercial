<?php

namespace App\Filament\User\Resources\CallResource\Pages;

use App\Filament\User\Resources\CallResource;
use App\Enums\OutcomeType;
use App\Enums\ContactType;
use App\Models\Contact;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCall extends EditRecord
{
    protected static string $resource = CallResource::class;

    protected function afterSave(): void // Oppure afterCreate()
{
    $data = $this->data;
    $record = $this->record;

    if ($data['outcome_type'] === OutcomeType::VISIT->value) {

        // Verifichiamo se esiste già una visita per questo cliente in questo slot temporale
        $alreadyExists = Contact::where('client_id', $record->client_id)
            ->where('contact_type', \App\Enums\ContactType::VISIT)
            ->where('date', $data['visit_date'])
            ->where('time', $data['visit_time'])
            ->exists();

        if (!$alreadyExists) {
            Contact::create([
                'client_id'    => $record->client_id,
                'contact_type' => \App\Enums\ContactType::VISIT,
                'date'         => $data['visit_date'],
                'time'         => $data['visit_time'],
                'services'      => $record->services,
                'note'         => "Generata da chiamata del " .
                                    Carbon::parse($record->date)->format('d/m/Y').
                                    " ore " .
                                    Carbon::parse($record->time)->format('H:i'),
                'user_id'      => Auth::id(),
            ]);

            Notification::make()
                ->success()
                ->title('Visita creata')
                ->body('L\'appuntamento è stato inserito correttamente.')
                ->send();
        } else {
            // Se esiste già, avvisiamo l'utente senza creare un duplicato
            Notification::make()
                ->warning()
                ->title('Visita già presente')
                ->body('Esiste già una visita per questo cliente alla stessa ora e data. Non è stato creato alcun duplicato.')
                ->persistent() // La notifica non scompare finché non la chiude
                ->send();
        }
    }
}

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
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
                return CallResource::getUrl('index');
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
