<?php

namespace App\Filament\User\Resources\TenderResource\Pages;

use App\Filament\Exports\TenderExporter;
use App\Filament\User\Resources\TenderResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;

class ListTenders extends ListRecords
{
    protected static string $resource = TenderResource::class;

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

                        if(count($records) === 0){
                            Notification::make()
                                ->title('Nessun elemento da stampare')
                                ->warning()
                                ->send();
                            return false;
                        }

                        return response()
                            ->streamDownload(function () use ($records, $search, $filters) {
                                echo Pdf::loadHTML(
                                    Blade::render('print.tenders', [
                                        'tenders' => $records,
                                        'search' => $search,
                                        'filters' => $filters,
                                    ])
                                )
                                    ->setPaper('A4', 'landscape')
                                    ->stream();
                            }, 'Appalti.pdf');

                        Notification::make()
                            ->title('Stampa avviata')
                            ->success()
                            ->send();
                    }),
            ExportAction::make('esporta')
                ->icon('heroicon-s-table-cells')
                ->label('Esporta')
                ->tooltip('Esporta elenco appalti')
                ->color('primary')
                ->exporter(TenderExporter::class)
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
