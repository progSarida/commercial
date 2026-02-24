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
use Illuminate\Database\Eloquent\Builder;
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
        $currentCall = $this->record;
        $type = '';
        if($currentCall->date && $currentCall->time) {
            $type = ' (data)';
            $previousCall = Contact::where('contact_type', ContactType::CALL)
                                ->where(function (Builder $query) use ($currentCall) {
                                    $query->where('date', '<', $currentCall->date) // Tutti i giorni passati
                                        ->orWhere(function (Builder $q) use ($currentCall) {
                                            $q->where('date', $currentCall->date)    // Oppure oggi...
                                                ->where('time', '<', $currentCall->time); // ...ma prima di quest'ora
                                        });
                                })
                                ->orderBy('date', 'desc')
                                ->orderBy('time', 'desc')
                                ->first();
            $nextCall = Contact::where('contact_type', ContactType::CALL)
                                ->where(function (Builder $query) use ($currentCall) {
                                    $query->where('date', '>', $currentCall->date) // Tutti i giorni futuri
                                        ->orWhere(function (Builder $q) use ($currentCall) {
                                            $q->where('date', $currentCall->date)    // Oppure oggi...
                                                ->where('time', '>', $currentCall->time); // ...ma dopo quest'ora
                                        });
                                })
                                ->orderBy('date', 'asc')
                                ->orderBy('time', 'asc')
                                ->first();
        } else {
            $type = ' (cliente)';
            $previousCall = Contact::where('contact_type', ContactType::CALL)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '<', $currentCall->client->name)
                                    ->orderBy('clients.name', 'desc')
                                    ->select('contacts.*')
                                    ->first();
            $nextCall = Contact::where('contact_type', ContactType::CALL)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '>', $currentCall->client->name)
                                    ->orderBy('clients.name', 'asc')
                                    ->select('contacts.*')
                                    ->first();
        }
        return [
            // Actions\DeleteAction::make(),
            // Scorrimento
            Actions\Action::make('previous_d_call')
                ->label("Precedente {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(function () use ($previousCall) { return $previousCall;})
                ->action(function () use ($previousCall) {
                    $this->redirect(CallResource::getUrl('edit', ['record' => $previousCall->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextCall) { return $nextCall;})
                ->action(function () use ($nextCall) {
                    $this->redirect(CallResource::getUrl('edit', ['record' => $nextCall->id]));
                }),
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
