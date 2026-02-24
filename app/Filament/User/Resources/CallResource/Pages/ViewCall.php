<?php

namespace App\Filament\User\Resources\CallResource\Pages;

use App\Enums\ContactType;
use App\Filament\User\Resources\CallResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;

class ViewCall extends ViewRecord
{
    protected static string $resource = CallResource::class;

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
            Actions\Action::make('back')
                ->label('Indietro')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
            // Scorrimento
            Actions\Action::make('previous_d_call')
                ->label("Precedente {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(function () use ($previousCall) { return $previousCall;})
                ->action(function () use ($previousCall) {
                    $this->redirect(CallResource::getUrl('view', ['record' => $previousCall->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextCall) { return $nextCall;})
                ->action(function () use ($nextCall) {
                    $this->redirect(CallResource::getUrl('view', ['record' => $nextCall->id]));
                }),
            Actions\EditAction::make(),
        ];
    }
}
