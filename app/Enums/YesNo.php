<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum YesNo: string implements HasLabel
{
    case YES = "yes";
    case NO = "no";

    public function getLabel(): string
    {
        return match($this) {
            self::YES => 'Si',
            self::NO => 'No',
        };
    }
}
