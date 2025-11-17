<?php

namespace App\Filament\Resources\BiddingTypeResource\Pages;

use App\Filament\Resources\BiddingTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiddingType extends EditRecord
{
    protected static string $resource = BiddingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
