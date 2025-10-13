<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EstimateState: string implements HasLabel
{
    case PROPOSED = "proposed";
    case APPROVED = "approved";

    public function getLabel(): string
    {
        return match($this) {
            self::PROPOSED => 'Proposto',
            self::APPROVED => 'Approvato',
        };
    }

    public function getFile(): string                   // tipi file permessi per lo stato
    {
        return match($this) {
            self::PROPOSED => 'doc,docx',
            self::APPROVED => 'pdf',
        };
    }
}
