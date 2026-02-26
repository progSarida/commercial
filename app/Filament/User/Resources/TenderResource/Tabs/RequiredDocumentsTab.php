<?php

namespace App\Filament\User\Resources\TenderResource\Tabs;

use App\Enums\TenderItemProcessingState;
use App\Enums\TenderMandatoryContentMethod;
use App\Enums\TenderMandatoryContentUtility;
use App\Enums\TenderProjectFormat;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class RequiredDocumentsTab
{
    public static function make(): array
    {
        return [
            Checkbox::make('service_reference_require_check')
                ->label('Necessità di referenze relative a svolgimento servizi analoghi')
                ->live()
                ->default(false)
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            TextInput::make('service_reference_number')
                ->label('Quante')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Select::make('service_reference_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                // ->options(TenderItemProcessingState::class))
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowAll())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('service_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 14]),
            TextInput::make('service_reference_1')
                ->label('Servizio 1')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            DatePicker::make('service_reference_date_1')
                ->label('Data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => $get('service_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 6]),
            TextInput::make('service_refernce_2')
                ->label('Servizio 2')
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            DatePicker::make('service_reference_date_2')
                ->label('Data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('service_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => $get('service_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 6]),
            Checkbox::make('bank_reference_require_check')
                ->label('Necessità di referenze bancarie')
                ->live()
                ->default(false)
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            TextInput::make('bank_reference_number')
                ->label('Quante')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Select::make('bank_reference_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowAll())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('bank_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 14]),
            TextInput::make('bank_reference_1')
                ->label('Banca 1')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            DatePicker::make('bank_reference_date_1')
                ->label('Data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => $get('bank_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 6]),
            TextInput::make('bank_reference_2')
                ->label('Banca 2')
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            DatePicker::make('bank_reference_date_2')
                ->label('Data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('bank_reference_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Placeholder::make('')->visible(fn (callable $get) => $get('bank_reference_require_check'))->columnSpan(['sm' => 0, 'md' => 6]),
            Checkbox::make('pass_oe_require_check')
                ->label('E\' previsto il PASS OE')
                ->live()
                ->default(false)
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('pass_oe_require_check'))->columnSpan(['sm' => 0, 'md' => 12]),
            DatePicker::make('pass_oe_require_deadline_date')
                ->label('Da effettuarsi in data/entro il')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('pass_oe_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Select::make('pass_oe_require_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('pass_oe_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Checkbox::make('bidding_inspection_virtual')
                ->label('E\' previsto il sopralluogo')
                ->disabled()
                ->live()
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('bidding_inspection_virtual'))->columnSpan(['sm' => 0, 'md' => 12]),
            DatePicker::make('bidding_inspection_deadline_virtual')
                ->label('Da effettuarsi in data/entro il')
                ->extraInputAttributes(['class' => 'text-center'])
                ->disabled()
                ->visible(fn (callable $get) => $get('bidding_inspection_virtual'))
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Select::make('inspection_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('bidding_inspection_virtual'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Checkbox::make('deposit_require_check')
                ->label('E\' richiesta la cauzione provvisoria')
                ->live()
                ->default(false)
                ->columnSpan(['sm' => 'full', 'md' => 12]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('deposit_require_check'))->columnSpan(['sm' => 0, 'md' => 12]),
            TextInput::make('deposit_require_amount')
                ->label('Importo')
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            DatePicker::make('deposit_require_date')
                ->label('Richiesta in data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('deposit_require_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('deposit_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowAll())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Checkbox::make('authority_tax_require_check')
                ->label('E\' previsto il versamento del contributo all\'autorità di vigilanza  (OBBLIGATORIO per gli appalti di valore superiore a €150.000)')
                ->live()
                ->default(false)
                ->columnSpan(['sm' => 'full', 'md' => 7]),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' => 5]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('authority_tax_require_check'))->columnSpan(['sm' => 0, 'md' => 12]),
            TextInput::make('authority_tax_require_amount')
                ->label('Importo')
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            DatePicker::make('authority_tax_payment_date')
                ->label('Effettuato in data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('authority_tax_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('authority_tax_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Checkbox::make('project_require_check')
                ->label('E\' prevista la realizzazione di un progetto di gestione')
                ->live()
                ->afterStateHydrated(function (Checkbox $component, $record, $state) {
                    $isVantaggiosa = $record?->bidding?->biddingAdjudicationType?->name === "Offerta Economicamente più vantaggiosa";
                    if ($isVantaggiosa) {
                        $component->state(true);
                    }
                })
                ->disabled(fn ($record) =>
                    $record?->bidding?->biddingAdjudicationType?->name === "Offerta Economicamente più vantaggiosa"
                )
                ->columnSpan(['sm' => 'full', 'md' => 10]),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>2]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('project_require_check'))->columnSpan(['sm' => 0, 'md' => 12]),
            Select::make('tender_project_format')
                ->label('Formato')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderProjectFormat::class)
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Select::make('project_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('project_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            Textarea::make('project_points')
                ->label('Punti principali del  progetto di gestione')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 'full']),
            TextInput::make('project_max_page')
                ->label('N.ro max pagine')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('project_format')
                ->label('Formato')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('project_character')
                ->label('Carattere')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('project_dimension')
                ->label('Dimensione')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('project_spacing')
                ->label('Interlinea')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('project_printed')
                ->label('Stampato')
                ->extraInputAttributes(['class' => 'text-right'])
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Placeholder::make('')
                ->label('')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')
                ->label('')
                ->content('Contenuto obbligatorio  dell\'offerta economica')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 16]),
            Placeholder::make('')
                ->label('Contenuto 1')
                ->content('Oneri di sicurezza')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 0, 'md' => 4]),
            Select::make('security_utility')
                ->label('Utilità')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentUtility::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('security_method')
                ->label('Metodo')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentMethod::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('security_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('project_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Placeholder::make('')
                ->label('Contenuto 2')
                ->content('Costo del  personale')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' => 4]),
            Select::make('staff_utility')
                ->label('Utilità')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentUtility::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('staff_method')
                ->label('Metodo')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentMethod::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('staff_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('project_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('other')
                ->label('Contenuto 3')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' => 4]),
            Select::make('other_utility')
                ->label('Utilità')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentUtility::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('other_method')
                ->label('Metodo')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->options(TenderMandatoryContentMethod::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('other_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('project_require_check'))
                // ->options(TenderItemProcessingState::class)
                ->options(
                    collect(TenderItemProcessingState::cases())
                        ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                        ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                        ->toArray()
                )
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Textarea::make('note')
                ->label('Note')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 'full']),
            Placeholder::make('')
                ->label('')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('necessaryDocsTitle')
                ->label('')
                ->content('Riepilogo dei documenti necessari per la partecipazione all\'appalto')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->extraAttributes(['class' => 'text-center font-semibold text-lg'])
                ->columnSpan(['sm' => 'full', 'md' => 'full']),
            Repeater::make('necessaryDocs')
                ->label('')
                ->visible(fn (callable $get) => $get('project_require_check'))
                ->relationship('necessaryDocs')
                ->schema([
                    TextInput::make('doc')
                        ->label('Documento')
                        ->required()
                        ->columnSpan(['sm' => 'full', 'md' => 8]),
                    Select::make('doc_processing_state')
                        ->label('Stato lavorazione')
                        // ->options(TenderItemProcessingState::class)
                        ->options(
                            collect(TenderItemProcessingState::cases())
                                ->filter(fn (TenderItemProcessingState $state) => $state->getShowSome())
                                ->mapWithKeys(fn ($state) => [$state->value => $state->getLabel()])
                                ->toArray()
                        )
                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                ])
                ->columns(12)
                ->defaultItems(0)
                ->addActionLabel('Aggiungi documento')
                ->collapsible()
                ->columnSpan(['sm' => 'full', 'md' => 'full']),
        ];
    }
}
