<?php

namespace App\Filament\User\Resources\CallResource\Pages;

use App\Filament\User\Resources\CallResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCall extends ViewRecord
{
    protected static string $resource = CallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
