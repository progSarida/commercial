<?php

namespace App\Filament\User\Resources\TenderResource\Pages;

use App\Filament\User\Resources\TenderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;

class CreateTender extends CreateRecord
{
    protected static string $resource = TenderResource::class;

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
