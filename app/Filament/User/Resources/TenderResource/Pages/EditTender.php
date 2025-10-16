<?php

namespace App\Filament\User\Resources\TenderResource\Pages;

use App\Filament\User\Resources\TenderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditTender extends EditRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null                                  // allarga la tabella a tutta pagina
    {
        return MaxWidth::Full;
    }
}
