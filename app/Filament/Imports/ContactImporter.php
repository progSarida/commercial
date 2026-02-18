<?php

namespace App\Filament\Imports;

use App\Enums\ClientType;
use App\Enums\ContactType;
use App\Models\Contact;
use App\Models\City;
use App\Models\Client;
use App\Models\Province;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContactImporter extends Importer
{
    protected static ?string $model = Contact::class;

    // Per gestire il punto e virgola del CSV
    public function getOptions(): array
    {
        return [
            'delimiter' => ';',
        ];
    }

    // public static function getColumns(): array
    // {
    //     return [
    //         ImportColumn::make('contact_type')
    //             ->rules(['max:255']),
    //         ImportColumn::make('client_id')
    //             ->requiredMapping()
    //             ->numeric()
    //             ->rules(['required', 'integer']),
    //         ImportColumn::make('date')
    //             ->rules(['date']),
    //         ImportColumn::make('time'),
    //         ImportColumn::make('note')
    //             ->rules(['max:255']),
    //         ImportColumn::make('outcome_type')
    //             ->rules(['max:255']),
    //         ImportColumn::make('services'),
    //         ImportColumn::make('user_id')
    //             ->requiredMapping()
    //             ->numeric()
    //             ->rules(['required', 'integer']),
    //     ];
    // }

    public static function getColumns(): array
    {
        return [
            // Mappiamo le colonne che ci servono per l'anteprima
            // ImportColumn::make('Descrizione')
            //     ->requiredMapping(),
            // ImportColumn::make('Comune')
            //     ->requiredMapping(),
            // ImportColumn::make('Indirizzo Mail')
            //     ->requiredMapping(),
            // ImportColumn::make('Consegna')
            //     ->requiredMapping(),
        ];
    }

    // public function resolveRecord(): ?Contact
    // {
    //     // return Contact::firstOrNew([
    //     //     // Update existing records, matching them by `$this->data['column_name']`
    //     //     'email' => $this->data['email'],
    //     // ]);

    //     return new Contact();
    // }

    public function resolveRecord(): ?Contact
    {
Log::info("Tentativo elaborazione riga:", $this->data);
        // 1. FILTRO: Se 'Consegna' non è esattamente 'CONSEGNA', scarta la riga
        if (($this->data['Consegna'] ?? '') !== 'CONSEGNA') {
Log::info("Mancata consegna => Ignorata");
            return null; // Ritorna null per saltare l'importazione di questa riga
        }

        $descrizione = $this->data['Descrizione'] ?? '';
        $comuneExcel = $this->data['Comune'] ?? '';
        $indirizzoMail = $this->data['Indirizzo Mail'] ?? '';
Log::info("Importazione {$descrizione}------------------------------------------------------");
Log::info("Comune: {$comuneExcel}");
Log::info("Indirizzo: {$indirizzoMail}");
        $clientId = null;
        $targetType = ClientType::OTHER;
        $targetName = trim($descrizione);

        // 2. DETERMINAZIONE TIPO E NOME PULITO
        if (Str::contains($descrizione, 'Comune di')) {                             // comune
            $targetType = ClientType::CITY;
            $targetName = trim(Str::after($descrizione, 'Comune di'));
        } elseif (Str::contains($descrizione, 'Provincia')) {                       // provincia
            $targetType = ClientType::PROVINCE;
        } elseif (Str::contains($descrizione, 'etropolitana')) {                    // città metropolitana
            $targetType = ClientType::METRO;
        } elseif (Str::contains($descrizione, 'Unione')) {                          // unione di comuni
            $targetType = ClientType::CITIES_UNION;
        } elseif (Str::contains($descrizione, 'Comunit', ignoreCase: true)) {       // comunità montana (Gestisce Comunità/Comunita)
            $targetType = ClientType::MOUNTAIN;
        }
Log::info("Tipo: {$targetType->getLabel()}");
        // 3. RICERCA CLIENTE ESISTENTE
        $client = Client::where('name', $targetName)
            ->where('client_type', $targetType)
            ->first();

        if ($client) {
            $clientId = $client->id;
Log::info("Id cliente esistente: {$clientId}");
        } else {
            // 4. CREAZIONE NUOVO CLIENTE (Se non esiste)
            $city = City::where('name', $comuneExcel)->first();

            // Recupero dati geografici in cascata (usando l'operatore safe navigation ?)
            if (!$city) {
                // OPZIONE A: Logga l'errore e scarta la riga (consigliato se il comune è vitale)
                // Log::warning("Comune non trovato: {$comuneExcel} per il record {$descrizione}");
                // return null;

                // OPZIONE B: Crea il cliente con i soli dati disponibili
                $cityId = null;
                $zipCode = null;
                $provinceId = null;
                $regionId = null;
            } else {
                $cityId = $city->id;
                $zipCode = $city->zip_code;
                $provinceId = $city->province_id;
                $province = Province::find($provinceId);
                $regionId = $province?->region_id;
            }
Log::info("Creazione nuovo cliente");
            $newClient = Client::create([
                'client_type' => $targetType,
                'name'        => $targetName,
                'state_id'    => 111,                                                       // Italia
                'region_id'   => $regionId,
                'province_id' => $provinceId,
                'city_id'     => $cityId,
                'zip_code'    => $zipCode,
                'email'       => $indirizzoMail,
            ]);

            $clientId = $newClient->id;
Log::info("Id nuovo cliente: {$clientId}");
        }
Log::info("Creazione nuovo contatto");
        // 5. CREAZIONE RECORD
        $contact = new Contact();
        $contact->client_id = $clientId;
        $contact->contact_type = ContactType::CALL;
        $contact->date = null;
        $contact->time = null;
        $contact->outcome_type = null;
        $contact->services = null;
        $contact->note = null;
        $contact->user_id = Auth::id();

        return $contact;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your contact import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
