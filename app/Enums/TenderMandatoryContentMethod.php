<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderMandatoryContentMethod: string implements HasLabel
{
    case DETAILED = "detailed";
    case SUMMARY = "summary";

    public function getLabel(): string
    {
        return match($this) {
            self::DETAILED => 'Analitico',
            self::SUMMARY => 'Sintetico',
        };
    }
}
