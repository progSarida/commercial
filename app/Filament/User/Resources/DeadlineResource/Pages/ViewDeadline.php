<?php

namespace App\Filament\User\Resources\DeadlineResource\Pages;

use App\Enums\ContactType;
use App\Filament\User\Resources\DeadlineResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;

class ViewDeadline extends ViewRecord
{
    protected static string $resource = DeadlineResource::class;

    protected function getHeaderActions(): array
    {
        $currentDeadline = $this->record;
        $type = '';
        if($currentDeadline->date && $currentDeadline->time) {
            $type = ' (data)';
            $previousDeadline = Contact::where('contact_type', ContactType::DEADLINE)
                                ->where(function (Builder $query) use ($currentDeadline) {
                                    $query->where('date', '<', $currentDeadline->date) // Tutti i giorni passati
                                        ->orWhere(function (Builder $q) use ($currentDeadline) {
                                            $q->where('date', $currentDeadline->date)    // Oppure oggi...
                                                ->where('time', '<', $currentDeadline->time); // ...ma prima di quest'ora
                                        });
                                })
                                ->orderBy('date', 'desc')
                                ->orderBy('time', 'desc')
                                ->first();
            $nextDeadline = Contact::where('contact_type', ContactType::DEADLINE)
                                ->where(function (Builder $query) use ($currentDeadline) {
                                    $query->where('date', '>', $currentDeadline->date) // Tutti i giorni futuri
                                        ->orWhere(function (Builder $q) use ($currentDeadline) {
                                            $q->where('date', $currentDeadline->date)    // Oppure oggi...
                                                ->where('time', '>', $currentDeadline->time); // ...ma dopo quest'ora
                                        });
                                })
                                ->orderBy('date', 'asc')
                                ->orderBy('time', 'asc')
                                ->first();
        } else {
            $type = ' (cliente)';
            $previousDeadline = Contact::where('contact_type', ContactType::DEADLINE)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '<', $currentDeadline->client->name)
                                    ->orderBy('clients.name', 'desc')
                                    ->select('contacts.*')
                                    ->first();
            $nextDeadline = Contact::where('contact_type', ContactType::DEADLINE)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '>', $currentDeadline->client->name)
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
                ->visible(function () use ($previousDeadline) { return $previousDeadline;})
                ->action(function () use ($previousDeadline) {
                    $this->redirect(DeadlineResource::getUrl('view', ['record' => $previousDeadline->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextDeadline) { return $nextDeadline;})
                ->action(function () use ($nextDeadline) {
                    $this->redirect(DeadlineResource::getUrl('view', ['record' => $nextDeadline->id]));
                }),
            Actions\EditAction::make(),
        ];
    }
}
