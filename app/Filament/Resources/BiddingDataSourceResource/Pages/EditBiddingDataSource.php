<?php

namespace App\Filament\Resources\BiddingDataSourceResource\Pages;

use App\Filament\Resources\BiddingDataSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBiddingDataSource extends EditRecord
{
    protected static string $resource = BiddingDataSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
