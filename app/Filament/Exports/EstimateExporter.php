<?php

namespace App\Filament\Exports;

use App\Models\Estimate;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EstimateExporter extends Exporter
{
    protected static ?string $model = Estimate::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('#'),
            ExportColumn::make('contact_type')
                ->label('Tipo Contatto')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('client.name')
                ->label('Cliente')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('date')
                ->label('Data')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'N/D'),
            ExportColumn::make('clientServices')
                ->label('Servizi')
                ->formatStateUsing(fn (Estimate $record) => $record->getFormattedPrintClientServices()),
            ExportColumn::make('estimate_state')
                ->label('Stato')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? 'N/D'),
            ExportColumn::make('done')
                ->label('Chiuso')
                ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
            ExportColumn::make('userRequest.name')
                ->label('Richiesto da')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('userDone.name')
                ->label('Chiuso da')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('userState.name')
                ->label('Stato Modificato da')
                ->formatStateUsing(fn ($state) => $state ?? 'N/D'),
            ExportColumn::make('path')
                ->label('File')
                ->formatStateUsing(fn ($state) => $state ? basename($state) : 'Nessun file'),
            ExportColumn::make('contact_id')
                ->label('ID Contatto')
                ->enabledByDefault(false),
            ExportColumn::make('site')
                ->label('Sito')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your estimate export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}