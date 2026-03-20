<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum SendModeType: string implements HasLabel, HasColor
{
    case PEC = "pec";
    case REGISTERED = "registered";
    case MAIL = "mail";
    case ORDINARY = "ordinary";
    case HAND = 'hand';

    case PORTAL = 'portal';

    public function getLabel(): string
    {
        return match($this) {
            self::PEC => 'PEC',
            self::REGISTERED => 'Posta raccomandata',
            self::MAIL => 'Mail',
            self::ORDINARY => 'Posta ordinaria',
            self::HAND => 'Raccomandata a mano',
            self::PORTAL => 'Portale appalti',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::PEC => Color::Blue,
            self::REGISTERED => Color::Cyan,
            self::MAIL => Color::Orange,
            self::ORDINARY => Color::Amber,
            self::HAND => Color::Zinc,
            self::PORTAL => Color::Zinc,
        };
    }
}
