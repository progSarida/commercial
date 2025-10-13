<?php

namespace App\Filament\Resources\BiddingStateResource\Pages;

use App\Filament\Resources\BiddingStateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiddingState extends EditRecord
{
    protected static string $resource = BiddingStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
