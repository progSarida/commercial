<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BiddingProcedureType: string implements HasLabel
{
    case TELEMATIC = "telematic";
    case ORDINARY = "ordinary";

    public function getLabel(): string
    {
        return match($this) {
            self::TELEMATIC => 'Telematica',
            self::ORDINARY => 'Ordinaria',
        };
    }
}
