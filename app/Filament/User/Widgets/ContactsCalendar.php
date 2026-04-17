<?php

namespace App\Filament\User\Widgets;

use App\Models\Contact;
use App\Enums\ContactType;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class ContactsCalendar extends FullCalendarWidget
{
    /**
     * Model utilizzato dal calendario
     */
    public Model | string | null $model = Contact::class;

    /**
     * Fetch degli eventi dal database
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Contact::query()
            ->whereBetween('date', [$fetchInfo['start'], $fetchInfo['end']])
            ->with(['client', 'user'])
            ->whereIn('contact_type', [ContactType::VISIT, ContactType::DEADLINE])
            ->get()
            ->map(function (Contact $contact) {
                return [
                    'id' => $contact->id,
                    'title' => $this->getEventTitle($contact),
                    'start' => $contact->date . ' ' . ($contact->time ?? '00:00:00'),
                    'end' => $contact->date . ' ' . ($contact->time ?? '00:00:00'),
                    'backgroundColor' => $this->getEventColor($contact),
                    'borderColor' => $this->getEventColor($contact),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'contact_type' => $contact->contact_type->value,
                        'client_name' => $contact->client?->name,
                        'note' => $contact->note,
                        'outcome_type' => $contact->outcome_type?->value,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Gestione del click sull'evento
     */
    public function onEventClick($event): void
    {
        // Reindirizza alla pagina di modifica del contatto
        // $this->redirect(route('filament.user.resources.contacts.edit', [
        //     'record' => $event['id']
        // ]));
    }

    /**
     * Gestione del click su una data (per creare nuovo evento)
     */
    public function onDateClick($info): void
    {
        // Puoi aprire un modal o reindirizzare alla creazione
        // $this->redirect(route('filament.admin.resources.contacts.create', [
        //     'date' => $info['dateStr']
        // ]));
    }

    /**
     * Form schema per il modal di creazione/modifica (opzionale)
     */
    // public function getFormSchema(): array
    // {
    //     return [
    //         Forms\Components\Select::make('contact_type')
    //             ->label('Tipo Contatto')
    //             ->options([
    //                 ContactType::CALL->value => 'Chiamata',
    //                 ContactType::VISIT->value => 'Visita',
    //                 ContactType::DEADLINE->value => 'Scadenza',
    //             ])
    //             ->required(),

    //         Forms\Components\Select::make('client_id')
    //             ->label('Cliente')
    //             ->relationship('client', 'name')
    //             ->searchable()
    //             ->required(),

    //         Forms\Components\DatePicker::make('date')
    //             ->label('Data')
    //             ->required(),

    //         Forms\Components\TimePicker::make('time')
    //             ->label('Ora')
    //             ->seconds(false),

    //         Forms\Components\Textarea::make('note')
    //             ->label('Note')
    //             ->rows(3),

    //         Forms\Components\Select::make('outcome_type')
    //             ->label('Esito')
    //             ->options([
    //                 // Aggiungi qui i tuoi OutcomeType
    //             ]),
    //     ];
    // }

    /**
     * Risoluzione del record per il form
     */
    public function resolveEventRecord(array $data): Model
    {
        return Contact::find($data['id']);
    }

    /**
     * Genera il titolo dell'evento
     */
    protected function getEventTitle(Contact $contact): string
    {
        $type = match($contact->contact_type) {
            ContactType::CALL => '📞',
            ContactType::VISIT => '🚘​',
            ContactType::DEADLINE => '📅​',
            default => '📌'
        };

        $clientName = $contact->client?->name ?? 'Nessun cliente';
        $time = $contact->time ? ' - ' . substr($contact->time, 0, 5) : '';

        // return $type . ' ' . $clientName . $time;
        return $clientName;
    }

    /**
     * Determina il colore dell'evento in base al tipo
     */
    protected function getEventColor(Contact $contact): string
    {
        return match($contact->contact_type) {
            ContactType::CALL => '#3b82f6',      // Blu per chiamate
            ContactType::VISIT => '#10b981',     // Verde per visite
            ContactType::DEADLINE => '#ef4444',  // Rosso per scadenze
            default => '#6b7280'                 // Grigio default
        };
    }

    /**
     * Configurazione del calendario FullCalendar
     */
    // public function config(): array
    // {
    //     return [
    //         'locale' => 'it',
    //         'firstDay' => 1, // Lunedì come primo giorno
    //         'headerToolbar' => [
    //             'left' => 'prev,next today',
    //             'center' => 'title',
    //             'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    //         ],
    //         'buttonText' => [
    //             'today' => 'Oggi',
    //             'month' => 'Mese',
    //             'week' => 'Settimana',
    //             'day' => 'Giorno',
    //             'list' => 'Lista'
    //         ],
    //         'views' => [
    //             'dayGridMonth' => [
    //                 'titleFormat' => ['year' => 'numeric', 'month' => 'long']
    //             ],
    //         ],
    //         'initialView' => 'dayGridMonth',
    //         'navLinks' => true,
    //         'editable' => false,
    //         'selectable' => false,
    //         'droppable' => false,
    //         'eventStartEditable' => false,
    //         'eventDurationEditable' => false,
    //         'selectMirror' => true,
    //         'dayMaxEvents' => 3,
    //         'slotMinTime' => '08:00:00',
    //         'slotMaxTime' => '20:00:00',
    //         'nowIndicator' => true,
    //         'eventTimeFormat' => [
    //             'hour' => '2-digit',
    //             'minute' => '2-digit',
    //             'hour12' => false
    //         ],
    //         'slotLabelFormat' => [
    //             'hour' => '2-digit',
    //             'minute' => '2-digit',
    //             'hour12' => false
    //         ],
    //         'height' => 'auto',
    //     ];
    // }

    public function config(): array
    {
        // Determina la vista iniziale in base al user agent (lato server)
        $isMobile = request()->header('User-Agent') &&
                    preg_match('/(android|iphone|ipad|mobile)/i', request()->header('User-Agent'));

        return [
            'locale' => 'it',
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => $isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            ],
            'buttonText' => [
                'today' => 'Oggi',
                'month' => 'Mese',
                'week' => 'Settimana',
                'day' => 'Giorno',
                'list' => 'Lista'
            ],
            'views' => [
                'dayGridMonth' => [
                    'titleFormat' => ['year' => 'numeric', 'month' => 'long']
                ],
                'listMonth' => [
                    'titleFormat' => ['year' => 'numeric', 'month' => 'long']
                ],
            ],
            // Vista iniziale diversa per mobile
            'initialView' => $isMobile ? 'listDay' : 'dayGridMonth',

            'navLinks' => true,
            'editable' => false,
            'selectable' => false,
            'droppable' => false,
            'eventStartEditable' => false,
            'eventDurationEditable' => false,
            'selectMirror' => true,
            'dayMaxEvents' => $isMobile ? 2 : 3, // Meno eventi su mobile
            'slotMinTime' => '08:00:00',
            'slotMaxTime' => '20:00:00',
            'nowIndicator' => true,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false
            ],
            'slotLabelFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false
            ],
            'height' => 'auto',
            'contentHeight' => 'auto',
        ];
    }

    /**
     * Disabilito le azioni del calendario (rimuovo il pulsante di creazione)
     */
    protected function headerActions(): array
    {
        return [];
    }

    /**
     * Larghezza del widget
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Altezza del widget
     */
    protected ?string $height = '600px';
}
