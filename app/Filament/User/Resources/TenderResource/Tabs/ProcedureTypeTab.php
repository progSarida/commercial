<?php

namespace App\Filament\User\Resources\TenderResource\Tabs;

use App\Enums\TenderItemProcessingState;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;

class ProcedureTypeTab
{
    public static function make(): array
    {
        return [
            Placeholder::make('')->label('')->columnSpan(['sm' => 0, 'md' =>5]),
            Checkbox::make('open_procedure_check')
                ->label('Procedura aperta')
                ->default(false)
                ->live()
                ->columnSpan(['sm' => 'full', 'md' => 7]),
            Checkbox::make('invitation_request_check')
                ->label('Richiesta di essere invitati')
                ->default(false)
                ->live()
                ->visible(fn (callable $get) => $get('open_procedure_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            DatePicker::make('invitation_request_date')
                ->label('Effettuata in data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('invitation_request_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('invitation_request_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('invitation_request_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('invitation_request_check'))->columnSpan(['sm' => 0, 'md' =>8]),
            Checkbox::make('reliance_require_check')
                ->label('E\' necessario l\'avvalimento')
                ->default(false)
                ->live()
                ->visible(fn (callable $get) => $get('open_procedure_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Checkbox::make('reliance_admit_check')
                ->label('E\' ammesso l\'avvalimento')
                ->default(false)
                ->live()
                ->visible(fn (callable $get) => $get('open_procedure_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Placeholder::make('')->visible(fn (callable $get) => $get('open_procedure_check'))->columnSpan(['sm' => 0, 'md' =>4]),
            TextInput::make('reliance_company')
                ->label('Con ditta')
                ->visible(fn (callable $get) => $get('reliance_require_check') || $get('reliance_admit_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            DatePicker::make('reliance_date')
                ->label('Documentazione predisposta in data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('reliance_require_check') || $get('reliance_admit_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('reliance_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('reliance_require_check') || $get('reliance_admit_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('reliance_qualification')
                ->label('Per i seguenti requisiti')
                ->visible(fn (callable $get) => $get('reliance_require_check') || $get('reliance_admit_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')->visible(fn (callable $get) => $get('reliance_require_check') || $get('reliance_admit_check'))->columnSpan(['sm' => 0, 'md' =>4]),
            Checkbox::make('partnership_require_check')
                ->label('E\' necessaria una ATI')
                ->default(false)
                ->live()
                ->visible(fn (callable $get) => $get('open_procedure_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('partnership_company')
                ->label('Con ditta')
                ->visible(fn (callable $get) => $get('partnership_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
             Select::make('partnership_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('partnership_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            TextInput::make('partnership_activities')
                ->label('Per le seguenti attività')
                ->visible(fn (callable $get) => $get('partnership_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            Placeholder::make('')->visible(fn (callable $get) => $get('partnership_require_check'))->columnSpan(['sm' => 0, 'md' =>4]),
            Placeholder::make('')->visible(fn (callable $get) => !$get('partnership_require_check'))->columnSpan(['sm' => 0, 'md' =>8]),
            Checkbox::make('collection_require_check')
                ->label('E\' necessario chiedere gli incassi')
                ->default(false)
                ->live()
                ->visible(fn (callable $get) => $get('open_procedure_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            DatePicker::make('collection_request_date')
                ->label('Chiesti in data')
                ->extraInputAttributes(['class' => 'text-center'])
                ->visible(fn (callable $get) => $get('collection_require_check'))
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Select::make('collection_request_processing_state')
                ->label('Stato lavorazione')
                ->visible(fn (callable $get) => $get('collection_require_check'))
                ->options(TenderItemProcessingState::class)
                ->columnSpan(['sm' => 'full', 'md' => 4]),
        ];
    }
}
