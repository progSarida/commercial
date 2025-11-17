<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BiddingPriorityType: string implements HasLabel
{
    case HIGH = "high";
    case MID = "mid";
    case LOW = "low";

    public function getLabel(): string
    {
        return match($this) {
            self::HIGH => 'Alta',
            self::MID => 'Media',
            self::LOW => 'Bassa',
        };
    }
}
