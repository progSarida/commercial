<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderMandatoryContentUtility: string implements HasLabel
{
    case NEED = "need";
    case NONEED = "noneed";

    public function getLabel(): string
    {
        return match($this) {
            self::NEED => 'Serve',
            self::NONEED => 'Non serve',
        };
    }
}
