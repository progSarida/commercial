<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BiddingProcessingState: string implements HasLabel
{
    case EVALUATE = "evaluate";
    case MAKE = "make";
    case WORKING = "working";
    case COMPLETE = "complete";

    public function getLabel(): string
    {
        return match($this) {
            self::EVALUATE => 'Da valutare',
            self::MAKE => 'Da fare',
            self::WORKING => 'In lavoraazione',
            self::COMPLETE => 'Completata',
        };
    }
}
