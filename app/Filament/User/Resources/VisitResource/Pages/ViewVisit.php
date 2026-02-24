<?php

namespace App\Filament\User\Resources\VisitResource\Pages;

use App\Enums\ContactType;
use App\Filament\User\Resources\VisitResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;

class ViewVisit extends ViewRecord
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        $currentVisit = $this->record;
        $type = '';
        if($currentVisit->date && $currentVisit->time) {
            $type = ' (data)';
            $previousVisit = Contact::where('contact_type', ContactType::VISIT)
                                ->where(function (Builder $query) use ($currentVisit) {
                                    $query->where('date', '<', $currentVisit->date) // Tutti i giorni passati
                                        ->orWhere(function (Builder $q) use ($currentVisit) {
                                            $q->where('date', $currentVisit->date)    // Oppure oggi...
                                                ->where('time', '<', $currentVisit->time); // ...ma prima di quest'ora
                                        });
                                })
                                ->orderBy('date', 'desc')
                                ->orderBy('time', 'desc')
                                ->first();
            $nextVisit = Contact::where('contact_type', ContactType::VISIT)
                                ->where(function (Builder $query) use ($currentVisit) {
                                    $query->where('date', '>', $currentVisit->date) // Tutti i giorni futuri
                                        ->orWhere(function (Builder $q) use ($currentVisit) {
                                            $q->where('date', $currentVisit->date)    // Oppure oggi...
                                                ->where('time', '>', $currentVisit->time); // ...ma dopo quest'ora
                                        });
                                })
                                ->orderBy('date', 'asc')
                                ->orderBy('time', 'asc')
                                ->first();
        } else {
            $type = ' (cliente)';
            $previousVisit = Contact::where('contact_type', ContactType::VISIT)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '<', $currentVisit->client->name)
                                    ->orderBy('clients.name', 'desc')
                                    ->select('contacts.*')
                                    ->first();
            $nextVisit = Contact::where('contact_type', ContactType::VISIT)
                                    ->join('clients', 'contacts.client_id', '=', 'clients.id')
                                    ->where('clients.name', '>', $currentVisit->client->name)
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
                ->visible(function () use ($previousVisit) { return $previousVisit;})
                ->action(function () use ($previousVisit) {
                    $this->redirect(VisitResource::getUrl('view', ['record' => $previousVisit->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextVisit) { return $nextVisit;})
                ->action(function () use ($nextVisit) {
                    $this->redirect(VisitResource::getUrl('view', ['record' => $nextVisit->id]));
                }),
            Actions\EditAction::make(),
        ];
    }
}
