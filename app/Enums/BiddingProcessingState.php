<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BiddingProcessingState: string implements HasLabel, HasIcon
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

    public function getIcon(): string
    {
        return match($this) {
            self::PENDING => 'heroicon-m-no-symbol',
            self::TODO => 'heroicon-o-arrow-right-circle',
            self::WORKING => 'heroicon-o-arrow-right-circle',
            self::COMPLETE => 'heroicon-o-arrow-right-circle',
        };
    }
}
