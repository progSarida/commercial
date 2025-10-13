<?php

namespace App\Filament\Resources\BiddingAdjudicationTypeResource\Pages;

use App\Filament\Resources\BiddingAdjudicationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiddingAdjudicationType extends EditRecord
{
    protected static string $resource = BiddingAdjudicationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
