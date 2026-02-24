<?php

namespace App\Filament\User\Resources\DeadlineResource\Pages;

use App\Enums\ContactType;
use App\Filament\User\Resources\DeadlineResource;
use App\Models\Contact;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

class EditDeadline extends EditRecord
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
            // Actions\DeleteAction::make(),
            // Scorrimento
            Actions\Action::make('previous_d_call')
                ->label("Precedente {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(function () use ($previousDeadline) { return $previousDeadline;})
                ->action(function () use ($previousDeadline) {
                    $this->redirect(DeadlineResource::getUrl('edit', ['record' => $previousDeadline->id]));
                }),
            Actions\Action::make('next_d_cally')
                ->label("Successiva {$type}")
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(function () use ($nextDeadline) { return $nextDeadline;})
                ->action(function () use ($nextDeadline) {
                    $this->redirect(DeadlineResource::getUrl('edit', ['record' => $nextDeadline->id]));
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
                return DeadlineResource::getUrl('index');
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
