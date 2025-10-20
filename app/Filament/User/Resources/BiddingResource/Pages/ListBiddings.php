<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\Exports\BiddingExporter;
use App\Filament\User\Resources\BiddingResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;

class ListBiddings extends ListRecords
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->label('Stampa')
                    ->tooltip('Stampa elenco gare')
                    ->color('primary')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $filters = $livewire->tableFilters ?? [];
                        $search = $livewire->tableSearch ?? null;

                        return response()
                            ->streamDownload(function () use ($records, $search, $filters) {
                                echo Pdf::loadHTML(
                                    Blade::render('print.biddings', [
                                        'biddings' => $records,
                                        'search' => $search,
                                        'filters' => $filters,
                                    ])
                                )
                                    ->setPaper('A4', 'landscape')
                                    ->stream();
                            }, 'Gare.pdf');

                        Notification::make()
                            ->title('Stampa avviata')
                            ->success()
                            ->send();
                    }),
            ExportAction::make('esporta')
                ->icon('heroicon-s-table-cells')
                ->label('Esporta')
                ->tooltip('Esporta elenco clienti')
                ->color('primary')
                ->exporter(BiddingExporter::class)
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
