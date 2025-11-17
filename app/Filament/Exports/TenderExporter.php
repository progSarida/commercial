<?php

namespace App\Filament\Exports;

use App\Models\Tender;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TenderExporter extends Exporter
{
    protected static ?string $model = Tender::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('#')
                ->enabledByDefault(false),
            ExportColumn::make('client.name')
                ->label('Ente')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bidding.description')
                ->label('Descrizione')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bidding.serviceTypes')
                ->label('Servizi')
                ->formatStateUsing(fn ($record) => $record->bidding->serviceTypes->pluck('name')->join(' - ') ?: 'N/D'),
            ExportColumn::make('bidding.bidding_processing_state')
                ->label('Stato lavorazione gara')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('manage_current')
                ->label('Gestione attuale')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('manage_offer')
                ->label('Gestione offerta')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('revenue')
                ->label('Gettito')
                ->formatStateUsing(fn ($state) => $state ? number_format($state, 2, ',', '.') : 'N/D'),
            ExportColumn::make('conditions')
                ->label('Condizioni')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bidding.send_date')
                ->label('Data invio offerta')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('bidding.send_time')
                ->label('Orario invio offerta')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('bidding.opening_date')
                ->label('Data apertura offerte')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('bidding.opening_time')
                ->label('Orario apertura offerte')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('bidding.mandatory_inspection')
                ->label('Sopralluogo obbligatorio')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('bidding.inspection_deadline_date')
                ->label('Data scadenza sopralluogo')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('invitation_require_check')
                ->label('Richiesta invito necessaria')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('mode')
                ->label('Modalità')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('open_procedure_check')
                ->label('Procedura aperta')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('invitation_request_check')
                ->label('Richiesta di invito inviata')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('invitation_request_date')
                ->label('Data invio richiesta di invito')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('invitation_request_processing_state')
                ->label('Stato lavorazione richiesta di invito')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('reliance_require_check')
                ->label('Necessità avvalimento')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('reliance_admit_check')
                ->label('Ammissione avvalimento')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('reliance_company')
                ->label('Partner avvalimento')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('reliance_date')
                ->label('Data avvalimento')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('reliance_processing_state')
                ->label('Stato lavorazione avvalimento')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('reliance_qualification')
                ->label('Requisiti richiesti avvalimento')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('partnership_require_check')
                ->label('Associazione Temporanea di Imprese necessaria')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('partnership_company')
                ->label('Partner ATI')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('partnership_processing_state')
                ->label('Stato lavorazione ATI')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('partnership_activities')
                ->label('Attività ATI')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('collection_require_check')
                ->label('Richiesta incassi necessaria')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('collection_request_date')
                ->label('Data invio richiesta incassi')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('collection_request_processing_state')
                ->label('Stato lavorazione richiesta incassi')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('service_reference_require_check')
                ->label('Necessità referenze servizi analoghi')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('service_reference_number')
                ->label('Numero referenze servizi')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('service_reference_processing_state')
                ->label('Stato lavorazione referenze servizi')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('service_reference_1')
                ->label('Referenza servizio 1')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('service_reference_date_1')
                ->label('Data referenza servizio 1')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('service_reference_2')
                ->label('Referenza servizio 2')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('service_reference_date_2')
                ->label('Data referenza servizio 2')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('bank_reference_require_check')
                ->label('Necessità referenze bancarie')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('bank_reference_number')
                ->label('Numero referenze bancarie')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bank_reference_processing_state')
                ->label('Stato lavorazione referenze bancarie')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('bank_reference_1')
                ->label('Referenza bancaria 1')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bank_reference_date_1')
                ->label('Data referenza bancaria 1')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('bank_reference_2')
                ->label('Referenza bancaria 2')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('bank_reference_date_2')
                ->label('Data referenza bancaria 2')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('pass_oe_require_check')
                ->label('Previsto PASS OE')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('pass_oe_require_deadline_date')
                ->label('Data scadenza PASS OE')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('pass_oe_require_processing_state')
                ->label('Stato lavorazione PASS OE')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('inspection_processing_state')
                ->label('Stato lavorazione sopralluogo')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('deposit_require_check')
                ->label('Richiesta cauzione provvisoria')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('deposit_require_amount')
                ->label('Importo cauzione provvisoria')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('deposit_require_date')
                ->label('Data richiesta cauzione provvisoria')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('deposit_require_processing_state')
                ->label('Stato lavorazione cauzione provvisoria')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('authority_tax_require_check')
                ->label('Richiesta contributo autorità di vigilanza')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('authority_tax_require_amount')
                ->label('Importo contributo autorità di vigilanza')
                ->formatStateUsing(fn ($state) => $state ? number_format($state, 2, ',', '.') : 'N/D'),
            ExportColumn::make('authority_tax_payment_date')
                ->label('Data versamento contributo autorità')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('authority_tax_processing_state')
                ->label('Stato lavorazione contributo autorità')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('project_require_check')
                ->label('Richiesta progetto di gestione')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('tender_project_format')
                ->label('Formato progetto di gestione')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('project_processing_state')
                ->label('Stato lavorazione progetto di gestione')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('project_points')
                ->label('Punti principali progetto di gestione')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_max_page')
                ->label('Numero massimo pagine progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_format')
                ->label('Formato progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_character')
                ->label('Carattere progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_dimension')
                ->label('Dimensione progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_spacing')
                ->label('Interlinea progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('project_printed')
                ->label('Stampa progetto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('security_utility')
                ->label('Utilità oneri di sicurezza')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('security_method')
                ->label('Metodo oneri di sicurezza')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('security_processing_state')
                ->label('Stato lavorazione oneri di sicurezza')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('staff_utility')
                ->label('Utilità costo del personale')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('staff_method')
                ->label('Metodo costo del personale')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('staff_processing_state')
                ->label('Stato lavorazione costo del personale')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('other')
                ->label('Altro contenuto obbligatorio')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('other_utility')
                ->label('Utilità altro')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('other_method')
                ->label('Metodo altro')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('other_processing_state')
                ->label('Stato lavorazione altro')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('note')
                ->label('Note')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('modified_user_id')
                ->label('ID ultimo utente modificatore')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('modified_date')
                ->label('Data ultima modifica')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('created_at')
                ->label('Data creazione')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('updated_at')
                ->label('Data aggiornamento')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : 'N/D')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your tender export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
