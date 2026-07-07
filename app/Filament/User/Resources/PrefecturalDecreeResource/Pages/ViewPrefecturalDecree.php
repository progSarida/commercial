<?php

namespace App\Filament\User\Resources\PrefecturalDecreeResource\Pages;

use App\Filament\User\Resources\PrefecturalDecreeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrefecturalDecree extends ViewRecord
{
    protected static string $resource = PrefecturalDecreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
