<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BiddingFilter: string implements HasLabel
{
    case TENDER30 = "tender30";
    case INSPECTION30 = "inspection30";
    case TENDER15 = "tender15";
    case INSPECTION15 = "inspection15";
    case SEND30 = "send30";
    case SEND60 = "send60";
    case SEND90 = "send90";
    case SEND180 = "send180";

    public function getLabel(): string
    {
        return match($this) {
            self::TENDER30 => 'Gare prossimi 30 giorni',
            self::INSPECTION30 => 'Sopralluoghi prossimi 30 giorni',
            self::TENDER15 => 'Gare prossimi 15 giorni',
            self::INSPECTION15 => 'Sopralluoghi prossimi 15 giorni',
            self::SEND30 => 'Inviate in attesa di riscontro ultimi 30 giorni',
            self::SEND60 => 'Inviate in attesa di riscontro ultimi 60 giorni',
            self::SEND90 => 'Inviate in attesa di riscontro ultimi 90 giorni',
            self::SEND180 => 'Inviate in attesa di riscontro ultimi 6 mesi',
        };
    }
}
