<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum FeasibilityType: string implements HasLabel, HasColor
{
    case EVALUATE = "evaluate";
    case FEASIBLE = "feasible";
    case NOT_FEASIBLE = "not_feasible";

    public function getLabel(): string
    {
        return match($this) {
            self::EVALUATE => 'Da valutare',
            self::FEASIBLE => 'Fattibile',
            self::NOT_FEASIBLE => 'Non fattibile',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::EVALUATE => 'info',
            self::FEASIBLE => 'success',
            self::NOT_FEASIBLE => 'danger',
        };
    }
}
