<?php

namespace App\Filament\User\Resources\EstimateResource\Pages;

use App\Filament\User\Resources\EstimateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstimate extends EditRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
