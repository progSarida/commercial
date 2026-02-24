<?php

namespace App\Filament\User\Resources\VisitResource\Pages;

use App\Enums\ContactType;
use App\Filament\User\Resources\VisitResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

class EditVisit extends EditRecord
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
            // Actions\DeleteAction::make(),
            // Scorrimento
            Actions\Action::make('previous_d_call')
                ->label("Precedente {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(function () use ($previousVisit) { return $previousVisit;})
                ->action(function () use ($previousVisit) {
                    $this->redirect(VisitResource::getUrl('edit', ['record' => $previousVisit->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextVisit) { return $nextVisit;})
                ->action(function () use ($nextVisit) {
                    $this->redirect(VisitResource::getUrl('edit', ['record' => $nextVisit->id]));
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
                return VisitResource::getUrl('index');
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
