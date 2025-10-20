<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('#'),
            ExportColumn::make('client_type')
                ->label('Tipo cliente')
                ->formatStateUsing(fn ($state) => $state?->getLabel() ?? null),
            ExportColumn::make('name')
                ->label('Cliente'),
            ExportColumn::make('address')
                ->label('Indirizzo'),
            ExportColumn::make('city.name')
                ->label('Città'),
            ExportColumn::make('zip_code')
                ->label('CAP'),
            ExportColumn::make('province.code')
                ->label('Provincia'),
            ExportColumn::make('region.name')
                ->label('Regione'),
            ExportColumn::make('place')
                ->label('Luogo'),
            ExportColumn::make('civic')
                ->label('Civico'),
            ExportColumn::make('state.name')
                ->label('Paese'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('phone')
                ->label('Telefono'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('site')
                ->label('Sito')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your client export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
