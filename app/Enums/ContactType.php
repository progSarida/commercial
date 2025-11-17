<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactType: string implements HasLabel
{
    case CALL = "call";
    case VISIT = "visit";
    case DEADLINE = "deadline";

    public function getLabel(): string
    {
        return match($this) {
            self::CALL => 'Chiamata',
            self::VISIT => 'Visita',
            self::DEADLINE => 'Scadenza',
        };
    }
}
