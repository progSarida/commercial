<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Enums\YesNo;
use App\Filament\Exports\BiddingExporter;
use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;

class ListBiddings extends ListRecords
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clean')
                ->hidden(function() {
                    $expiredBiddings = Bidding::where(function ($query) {
                            $query->whereNull('send_date')
                                ->whereRaw('CONCAT(deadline_date, " ", COALESCE(deadline_time, "23:59:59")) < ?',
                                            [now()->subDays(30)])
                                ->whereNotNull('attachment_path');
                        })
                        ->orWhere(function ($query) {
                            $query->whereNotNull('send_date')
                                ->where('awarded', YesNo::NO)
                                ->where('closure_date', '<', now()->subDays(90)->format('Y-m-d'))
                                ->whereNotNull('attachment_path');
                        })
                        ->get();

                    $i = 0;
                    foreach($expiredBiddings as $bidding){
                        Storage::disk('public')->deleteDirectory($bidding->attachment_path);
                        $bidding->update([
                            'attachment_path' => null,
                        ]);
                        $i++;
                    }

                    if($i > 0){
                        $suffix = '';
                        switch($i){
                            case 1:
                                $suffix = 'a';
                                break;
                            default:
                                $suffix = 'e';
                        }
                        Notification::make('cleaned')
                            ->title('Sono stati cancellati gli allegati di ' . $i . ' gar' . $suffix . ' scadut' . $suffix . '.')
                            ->color('info')
                            ->persistent()
                            ->send();
                    }

                    return true;
                }),
            Actions\CreateAction::make(),
            Actions\Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->label('Stampa')
                    ->tooltip('Stampa elenco gare')
                    ->color(Color::rgb('rgb(255, 0, 0)'))
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
                ->tooltip('Esporta elenco gare')
                ->color(Color::rgb('rgb(0, 153, 0)'))
                ->exporter(BiddingExporter::class)
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
