<?php

namespace App\Filament\Resources\BiddingStateResource\Pages;

use App\Filament\Resources\BiddingStateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBiddingStates extends ListRecords
{
    protected static string $resource = BiddingStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
