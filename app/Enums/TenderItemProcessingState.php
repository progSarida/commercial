<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TenderItemProcessingState: string implements HasLabel
{
    case REQUESTED = "requested";
    case DRAFTED = "drafted";
    case APPROVED = "approved";

    public function getLabel(): string
    {
        return match($this) {
            self::REQUESTED => 'Richiesto',
            self::DRAFTED => 'Predisposto',
            self::APPROVED => 'Controllato',
        };
    }

    public function getShowAll(): string
    {
        return match($this) {
            self::REQUESTED => true,
            self::DRAFTED => true,
            self::APPROVED => true,
        };
    }

    public function getShowSome(): string
    {
        return match($this) {
            self::REQUESTED => false,
            self::DRAFTED => true,
            self::APPROVED => true,
        };
    }
}
