<?php

namespace App\Filament\Resources\BiddingTypeResource\Pages;

use App\Filament\Resources\BiddingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBiddingTypes extends ListRecords
{
    protected static string $resource = BiddingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
