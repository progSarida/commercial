<?php

namespace App\Filament\User\Resources\TenderResource\Tabs;

use App\Models\ServiceType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;

class GeneralDataTab
{
    public static function make(): array
    {
        return [
            TextInput::make('clientName')->label('Ente')->columnSpan(['sm' => 'full', 'md' => 4])->disabled(),
            TextInput::make('residents')->label('Abitanti')->extraInputAttributes(['class' => 'text-right'])->columnSpan(['sm' => 'full', 'md' => 2])->disabled(),
            TextInput::make('clientAddress')->label('Indirizzo')->columnSpan(['sm' => 'full', 'md' => 6])->disabled(),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>4]),
            TextInput::make('clientZipcode')->label('CAP')->columnSpan(['sm' => 'full', 'md' => 2])->disabled(),
            TextInput::make('clientProvince')->label('Provincia')->columnSpan(['sm' => 'full', 'md' => 3])->disabled(),
            TextInput::make('clientRegion')->label('Regione')->columnSpan(['sm' => 'full', 'md' => 3])->disabled(),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>2]),
            TextInput::make('biddingCig')->label('CIG')->columnSpan(['sm' => 'full', 'md' => 3])->disabled(),
            TextInput::make('clientPhone')->label('Telefono')->columnSpan(['sm' => 'full', 'md' => 3])->disabled(),
            TextInput::make('clientEmail')->label('Email')->columnSpan(['sm' => 'full', 'md' => 4])->disabled(),
            TextInput::make('manage_current')
                ->label('Gestione attuale')
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            TextInput::make('manage_offer')
                ->label('Gestione offerta')
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            TextInput::make('revenue')
                ->label('Gettito')
                ->columnSpan(['sm' => 'full', 'md' => 3]),
            TextInput::make('conditions')
                ->label('Condizioni')
                ->columnSpan(['sm' => 'full', 'md' => 9]),
            CheckboxList::make('serviceTypes')
                ->label('Gara relativa al servizio di')
                ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                ->columns(6)
                ->columnSpan(['sm' => 'full', 'md' => 12])
                ->gridDirection('row')
                ->disabled()
                ->dehydrated(false),
            Checkbox::make('invitation_require_check')
                ->label('Richiesta invito')
                ->columnSpan(['sm' => 'full', 'md' => 2]),
            Checkbox::make('biddingInspection')
                ->label('Sopralluogo obbligatorio')
                ->disabled()
                ->columnSpan(['sm' => 'full', 'md' => 3]),
            TextInput::make('biddingDuration')
                ->label('Durata')
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled(),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>3]),
            DatePicker::make('biddingSendDate')
                ->label('Data consegna')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 3])
                ->disabled(),
            TimePicker::make('biddingSendTime')
                ->label('Orario consegna')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 3])
                ->disabled(),
            TextInput::make('mode')
                ->label('Modalità')
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>2]),
            DatePicker::make('biddingOpeningDate')
                ->label('Data apertura offerte')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled(),
            TimePicker::make('biddingOpeningTime')
                ->label('Orario apertura offerte')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled(),
        ];
    }
}
