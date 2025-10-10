<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OutcomeType: string implements HasLabel
{
    case POSITIVE = "positive";
    case NEGATIVE = "negative";
    case ESTIMATE = "estimate";
    case POSTPONED = "postponed";

    public function getLabel(): string
    {
        return match($this) {
            self::POSITIVE => 'Positivo',
            self::NEGATIVE => 'Negativo',
            self::ESTIMATE => 'Preventivo',
            self::POSTPONED => 'Rimandato',
        };
    }
}
