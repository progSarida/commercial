<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BiddingProcessingState: string implements HasLabel
{
    case PENDING = "pending";
    case TODO = "todo";
    case WORKING = "working";
    case COMPLETE = "complete";

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Non iniziata',
            self::TODO => 'Da fare',
            self::WORKING => 'In lavorazione',
            self::COMPLETE => 'Completato',
        };
    }
}
