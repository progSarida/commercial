<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ClientType: string implements HasLabel
{
    case CITY = "city";
    case PROVINCE = "province";
    case CITIES_UNION = "cities_union";
    case MUNICIPAL = "municipal";
    case OTHER = "other";

    public function getLabel(): string
    {
        return match($this) {
            self::CITY => 'Comune',
            self::PROVINCE => 'Provincia',
            self::CITIES_UNION => 'Unione di Comuni',
            self::MUNICIPAL => 'Municipalizzata',
            self::OTHER => 'Altro',
        };
    }
}
