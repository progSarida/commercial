<?php

namespace App\Filament\User\Resources\TenderResource\Tabs;

use App\Enums\TenderItemProcessingState;
use App\Models\Bidding;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;

class RequiredDocumentsTab
{
    public static function make(): array
    {
        return [
            Checkbox::make('service_reference_require_check')
                ->label('Necessità di referenze relative a svolgimento servizi analoghi')
                ->live()
                ->default(false)
                ->columnSpan(6),
            TextInput::make('service_reference_number')
                ->label('Quante')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(3),
            Select::make('service_reference_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => !$get('service_reference_require_check'))->columnSpan(7),
            TextInput::make('service_refernce_1')
                ->label('Servizio 1')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(6),
            DatePicker::make('service_reference_date_1')
                ->label('Data')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => $get('service_reference_require_check'))->columnSpan(3),
            TextInput::make('service_refernce_2')
                ->label('Servizio 2')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(6),
            DatePicker::make('service_reference_date_2')
                ->label('Data')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => $get('service_reference_require_check'))->columnSpan(3),
            Checkbox::make('bank_reference_require_check')
                ->label('Necessità di referenze bancarie')
                ->live()
                ->default(false)
                ->columnSpan(6),
            TextInput::make('bank_reference_number')
                ->label('Quante')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(3),
            Select::make('bank_reference_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => !$get('bank_reference_require_check'))->columnSpan(7),
            TextInput::make('bank_refernce_1')
                ->label('Banca 1')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(6),
            DatePicker::make('bank_reference_date_1')
                ->label('Data')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => $get('bank_reference_require_check'))->columnSpan(3),
            TextInput::make('bank_refernce_2')
                ->label('Banca 2')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(6),
            DatePicker::make('bank_reference_date_2')
                ->label('Data')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(3),
            Placeholder::make('')->visible(fn (callable $get) => $get('bank_reference_require_check'))->columnSpan(3),
            Checkbox::make('pass_oe_require_check')
                ->label('E\' previsto il PASS OE')
                ->live()
                ->default(false)
                ->columnSpan(6),
            Placeholder::make('')->visible(fn (callable $get) => !$get('pass_oe_require_check'))->columnSpan(6),
            DatePicker::make('pass_oe_deadline_date')
                ->label('Da effettuarsi in data/entro il')
                ->visible(fn (callable $get) => $get('pass_oe_require_check'))
                ->columnSpan(3),
            Select::make('bank_reference_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('pass_oe_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(3),
            Checkbox::make('biddingMandatoryInspection')
                ->label('E\' previsto il sopralluogo')
                ->disabled()
                ->live()
                ->columnSpan(6),
            Placeholder::make('')->visible(fn (callable $get) => !$get('biddingMandatoryInspection'))->columnSpan(6),
            DatePicker::make('biddingMandatoryInspectionDeadline')
                ->label('Da effettuarsi in data/entro il')
                ->disabled()
                ->visible(fn (callable $get) => $get('biddingMandatoryInspection'))
                ->columnSpan(3),
            Select::make('inspection_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('biddingMandatoryInspection'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(3),
            Checkbox::make('deposit_require_check')
                ->label('E\' richiesta la cauzione provvisoria')
                ->live()
                ->default(false)
                ->columnSpan(6),
            Placeholder::make('')->visible(fn (callable $get) => !$get('deposit_require_check'))->columnSpan(6),
            TextInput::make('deposit_amount')
                ->label('Importo')
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                ->columnSpan(2),
            DatePicker::make('deposit_require_date')
                ->label('Richiesta in data')
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                ->columnSpan(2),
            Select::make('deposit_require_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(2),
            Checkbox::make('authority_tax_require_check')
                ->label('E\' previsto il versamento del contributo all\'autorità di vigilanza (OBBLIGATORIO per gli appalti di valore superiore a €150.000)')
                ->live()
                ->default(false)
                ->columnSpan(4),
            Placeholder::make('')->columnSpan(2),
            Placeholder::make('')->visible(fn (callable $get) => !$get('authority_tax_require_check'))->columnSpan(6),
            TextInput::make('authority_tax_require_amount')
                ->label('Importo')
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                ->columnSpan(2),
            DatePicker::make('authority_tax_payment_date')
                ->label('Effettuato in data')
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                ->columnSpan(2),
            Select::make('authority_tax_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(2),
            Checkbox::make('project_require_check')
                ->label('Necessità di referenze relative a svolgimento servizi analoghi')
                ->live()
                ->default(false)
                ->columnSpan(6),
            Placeholder::make('')->visible(fn (callable $get) => !$get('project_require_check'))->columnSpan(6),
        ];
    }
}
