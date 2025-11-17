<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderProjectFormat: string implements HasLabel
{
    case FREE = "free";
    case FIXED = "fixed";

    public function getLabel(): string
    {
        return match($this) {
            self::FREE => 'Libero',
            self::FIXED => 'Vincolato',
        };
    }
}
