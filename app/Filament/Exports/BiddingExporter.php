<?php

namespace App\Filament\Exports;

use App\Models\Bidding;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BiddingExporter extends Exporter
{
    protected static ?string $model = Bidding::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('#')
                ->enabledByDefault(false),
            ExportColumn::make('serviceTypes')
                ->label('Servizi')
                ->formatStateUsing(fn ($record) => $record->serviceTypes->pluck('name')->join(' - ') ?: 'N/D'),
            ExportColumn::make('description')
                ->label('Descrizione'),
            ExportColumn::make('client.name')
                ->label('Ente')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('province.name')
                ->label('Prov.')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('deadline_date')
                ->label('Gara')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('inspection_deadline_date')
                ->label('Sopralluogo')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('clarification_request_deadline_date')
                ->label('Chiarimenti')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('biddingType.name')
                ->label('Tipo gara')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('biddingState.name')
                ->label('Stato gara')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('amount')
                ->label('Importo')
                ->formatStateUsing(fn ($state) => $state ? '€ ' . number_format($state, 2, ',', '.') : 'N/D'),
            ExportColumn::make('residents')
                ->label('Abitanti')
                ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : 'N/D'),
            ExportColumn::make('bidding_processing_state')
                ->label('Stato lavorazione')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('bidding_priority_type')
                ->label('Priorità')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('bidding_procedure_type')
                ->label('Procedura')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('client_type')
                ->label('Tipo cliente')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('contact')
                ->label('Nome contatto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('contracting_station')
                ->label('Gestore appalto')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('region.name')
                ->label('Regione')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('procedure_portal')
                ->label('Portale procedura')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('cig')
                ->label('CIG')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('procedure_id')
                ->label('ID Procedura')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('day')
                ->label('Durata giorni')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('month')
                ->label('Durata mesi')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('year')
                ->label('Durata anni')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('renew')
                ->label('Rinnovo')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('deadline_time')
                ->label('Orario scadenza gara')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('inspection_deadline_time')
                ->label('Orario scadenza sopralluogo')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('clarification_request_deadline_time')
                ->label('Orario scadenza chiarimenti')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('send_date')
                ->label('Data invio offerta')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('send_time')
                ->label('Orario invio offerta')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('opening_date')
                ->label('Data apertura offerte')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('opening_time')
                ->label('Orario apertura offerte')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('assignedUser.name')
                ->label('Assegnato a')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('modifiedUser.name')
                ->label('Modificato da')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('source1.name')
                ->label('Fonte dati 1')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('source2.name')
                ->label('Fonte dati 2')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('source3.name')
                ->label('Fonte dati 3')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('bidding_note')
                ->label('Note gara')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('note')
                ->label('Note')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D')
                ->enabledByDefault(false),
            ExportColumn::make('mandatory_inspection')
                ->label('Sopralluogo obbligatorio')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No')
                ->enabledByDefault(false),
            ExportColumn::make('bidding_adjudication_type_id')
                ->label('Tipo aggiudicazione')
                ->formatStateUsing(fn ($record) => $record->biddingAdjudicationType?->name ?? 'N/D')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your bidding export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}