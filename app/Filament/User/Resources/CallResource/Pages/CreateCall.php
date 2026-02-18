<?php

namespace App\Filament\User\Resources\CallResource\Pages;

use App\Filament\User\Resources\CallResource;
use App\Enums\OutcomeType;
use App\Enums\ContactType;
use App\Models\Contact;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCall extends CreateRecord
{
    protected static string $resource = CallResource::class;

    protected function afterCreate(): void
    {
        // $this->data contiene TUTTI i campi del form, anche quelli non nel DB
        $data = $this->data;
        $record = $this->record;

        if ($data['outcome_type'] === OutcomeType::VISIT->value) {

            $alreadyExists = Contact::where('client_id', $record->client_id)
            ->where('contact_type', \App\Enums\ContactType::VISIT)
            ->where('date', $data['visit_date'])
            ->where('time', $data['visit_time'])
            ->exists();

            if (!$alreadyExists) {
                Contact::create([
                    'client_id'    => $record->client_id,
                    'contact_type' => ContactType::VISIT,
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
                    ->title('Visita creata con successo')
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
}
