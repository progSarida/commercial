<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum InterestExpressionType: string implements HasLabel, HasColor
{
    case EXPRESSION = "expression";
    case INQUIRY = "inquiry";
    case REQUEST = "request";

    public function getLabel(): string
    {
        return match($this) {
            self::EXPRESSION => 'Manifestazione di interesse',
            self::INQUIRY => 'Indagine di mercato',
            self::REQUEST => 'Richiesta d\'invito',
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::EXPRESSION => Color::Blue,
            self::INQUIRY => Color::Cyan,
            self::REQUEST => Color::Orange,
        };
    }
}
