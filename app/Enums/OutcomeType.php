<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OutcomeType: string implements HasLabel
{
    case POSITIVE = "positive";
    case NEGATIVE = "negative";
    case VISIT = "visit";
    case ESTIMATE = "estimate";
    case POSTPONED = "postponed";

    public function getLabel(): string
    {
        return match($this) {
            self::POSITIVE => 'Positivo',
            self::NEGATIVE => 'Negativo',
            self::VISIT => 'Visita',
            self::ESTIMATE => 'Preventivo',
            self::POSTPONED => 'Rimandato',
        };
    }

    /**
     * Restituisce le opzioni filtrate in base al tipo di contatto.
     */
    public static function getOptionsByContactType(ContactType|string|null $contactType): array
    {
        // Se passiamo una stringa, la trasformiamo in Enum
        if (is_string($contactType)) {
            $contactType = ContactType::tryFrom($contactType);
        }

        $cases = match($contactType) {
            // Se è una chiamata, posso avere tutto
            ContactType::CALL => [
                self::POSITIVE,
                self::NEGATIVE,
                self::VISIT,
                self::ESTIMATE,
                self::POSTPONED,
            ],
            // Se è una visita, "Visita" come esito non ha senso
            ContactType::VISIT => [
                self::POSITIVE,
                self::NEGATIVE,
                self::ESTIMATE,
                self::POSTPONED,
            ],
            // Per le scadenze potresti volere solo esiti semplici
            ContactType::DEADLINE => [
                self::POSITIVE,
                self::NEGATIVE,
                self::POSTPONED,
            ],
            default => self::cases(),
        };

        // Trasformo i casi in un array [value => label] per Filament
        return collect($cases)->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel()
        ])->toArray();
    }
}
