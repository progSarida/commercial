<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceState: string implements HasLabel
{
    case NO_MANAGE = "no_manage";
    case DIRECT = "direct";
    case THIRD = "third";
    case INTEREST = "interest";

    public function getLabel(): string
    {
        return match($this) {
            self::NO_MANAGE => 'Non gestita',
            self::DIRECT => 'Diretta',
            self::THIRD => 'Affidata a terzi',
            self::INTEREST => 'Interessati',
        };
    }
}
