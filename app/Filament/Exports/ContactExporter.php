<?php

namespace App\Filament\Exports;

use App\Models\Contact;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ContactExporter extends Exporter
{
    protected static ?string $model = Contact::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('#'),
            ExportColumn::make('contact_type')
                ->label('Tipo contatto')
                ->formatStateUsing(function ($state) {
                    if ($state instanceof \App\Enums\ContactType) {
                        return $state->getLabel();
                    }
                    return $state ? \App\Enums\ContactType::tryFrom($state)?->getLabel() ?? $state : '';
                }),
            ExportColumn::make('client.name')
                ->label('Cliente'),
            ExportColumn::make('date')
                ->label('Data')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : ''),
            ExportColumn::make('time')
                ->label('Orario')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : ''),
            ExportColumn::make('note')
                ->label('Note')
                ->enabledByDefault(false),
            ExportColumn::make('outcome_type')
                ->label('Esito')
                ->formatStateUsing(function ($state) {
                    if ($state instanceof \App\Enums\OutcomeType) {
                        return $state->getLabel();
                    }
                    return $state ? \App\Enums\OutcomeType::tryFrom($state)?->getLabel() ?? $state : '';
                }),
            ExportColumn::make('user.name')
                ->label('Utente'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your contact export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
