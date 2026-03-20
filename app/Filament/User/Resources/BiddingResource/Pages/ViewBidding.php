<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use App\Models\Client;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewBidding extends ViewRecord
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        $currentBidding = $this->record;
        $currentDate = $currentBidding->deadline_date ?? $currentBidding->interest_deadline_date;
        $currentTime = $currentBidding->deadline_time ?? $currentBidding->interest_deadline_time;
        $dateField = "COALESCE(deadline_date, interest_deadline_date)";
        $timeField = "COALESCE(deadline_time, interest_deadline_time)";
        // Precedente per deadline_date: data precedente O stessa data con ID minore
        // $previousDeadline = Bidding::where(function ($query) use ($currentBidding) {
        //         $query->where('deadline_date', '<', $currentBidding->deadline_date)
        //             ->orWhere(function ($q) use ($currentBidding) {
        //                 $q->where('deadline_date', '=', $currentBidding->deadline_date)
        //                   ->where('id', '<', $currentBidding->id);
        //             });
        //     })
        //     ->orderBy('deadline_date', 'desc')->orderBy('id', 'desc')->first();
        $previousDeadline = Bidding::query()
            ->where('id', '!=', $currentBidding->id)
            ->where(function ($q) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                $q->whereRaw("$dateField < ?", [$currentDate])
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField < ?", [$currentTime]);
                })
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField = ?", [$currentTime])
                        ->where('id', '<', $currentBidding->id);
                });
            })
            ->orderByRaw("$dateField DESC")
            ->orderByRaw("$timeField DESC")
            ->orderBy('id', 'desc')
            ->first();
        // Successivo per deadline_date: data successiva O stessa data con ID maggiore
        // $nextDeadline = Bidding::where(function ($query) use ($currentBidding) {
        //         $query->where('deadline_date', '>', $currentBidding->deadline_date)
        //             ->orWhere(function ($q) use ($currentBidding) {
        //                 $q->where('deadline_date', '=', $currentBidding->deadline_date)
        //                   ->where('id', '>', $currentBidding->id);
        //             });
        //     })
        //     ->orderBy('deadline_date', 'asc')->orderBy('id', 'asc')->first();
        $nextDeadline = Bidding::query()
            ->where('id', '!=', $currentBidding->id)
            ->where(function ($q) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                $q->whereRaw("$dateField > ?", [$currentDate])
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField > ?", [$currentTime]);
                })
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField = ?", [$currentTime])
                        ->where('id', '>', $currentBidding->id);
                });
            })
            ->orderByRaw("$dateField ASC")
            ->orderByRaw("$timeField ASC")
            ->orderBy('id', 'asc')
            ->first();
        // Precedente per inspection_deadline_date: data precedente O stessa data con ID minore
        $previousInspection = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '<', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '<', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'desc')->orderBy('id', 'desc')->first();
        // Successivo per inspection_deadline_date: data successiva O stessa data con ID maggiore
        $nextInspection = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '>', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '>', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'asc')->orderBy('id', 'asc')->first();

        return [
            Actions\Action::make('back')
                ->label('Indietro')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
            // Scorrimento in base a data di scadenza
            Actions\Action::make('previous_deadline')
                ->label('Scadenza Prec.')
                ->color('success')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $previousDeadline !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('view', ['record' => $previousDeadline->id]))),

            Actions\Action::make('next_deadline')
                ->label('Scadenza Succ.')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $nextDeadline !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('view', ['record' => $nextDeadline->id]))),

            // Scorrimento in base a data di sopralluogo
            Actions\Action::make('previous_inspection')
                ->label('Sopralluogo Prec.')
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $previousInspection !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('view', ['record' => $previousInspection->id]))),

            Actions\Action::make('next_inspection')
                ->label('Sopralluogo Succ.')
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $nextInspection !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('view', ['record' => $nextInspection->id]))),
            Actions\EditAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
