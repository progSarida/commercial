<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderItemProcessingState: string implements HasLabel
{
    case DRAFTED = "drafted";
    case APPROVED = "approved";

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFTED => 'Predisposto',
            self::APPROVED => 'Controllato',
        };
    }
}
