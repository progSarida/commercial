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
            TextInput::make('clientName')->label('Ente')->columnSpan(4)->disabled(),
            TextInput::make('residents')->label('Abitanti')->columnSpan(2)->disabled(),
            TextInput::make('clientAddress')->label('Indirizzo')->columnSpan(6)->disabled(),
            Placeholder::make('')->columnSpan(4),
            TextInput::make('clientZipcode')->label('CAP')->columnSpan(2)->disabled(),
            TextInput::make('clientProvince')->label('Provincia')->columnSpan(3)->disabled(),
            TextInput::make('clientRegion')->label('Regione')->columnSpan(3)->disabled(),
            Placeholder::make('')->columnSpan(2),
            TextInput::make('biddingCig')->label('CIG')->columnSpan(3)->disabled(),
            TextInput::make('clientPhone')->label('Telefono')->columnSpan(3)->disabled(),
            TextInput::make('clientEmail')->label('Email')->columnSpan(4)->disabled(),
            TextInput::make('manage_current')
                ->label('Gestione attuale')
                ->columnSpan(6),
            TextInput::make('manage_offer')
                ->label('Gestione offerta')
                ->columnSpan(6),
            TextInput::make('revenue')
                ->label('Gettito')
                ->columnSpan(3),
            TextInput::make('conditions')
                ->label('Condizioni')
                ->columnSpan(9),
            CheckboxList::make('serviceTypes')
                ->label('Gara relativa al servizio di')
                ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                ->columns(6)
                ->columnSpan(12)
                ->gridDirection('row')
                ->disabled()
                ->dehydrated(false),
            Checkbox::make('invitation_require_check')
                ->label('Richiesta invito')
                ->columnSpan(2),
            Checkbox::make('biddingInspection')
                ->label('Sopralluogo obbligatorio')
                ->disabled()
                ->columnSpan(3),
            TextInput::make('biddingDuration')
                ->label('Durata')
                ->columnSpan(4)
                ->disabled(),
            Placeholder::make('')->columnSpan(3),
            DatePicker::make('biddingSendDate')
                ->label('Data consegna')
                ->columnSpan(3)
                ->disabled(),
            TimePicker::make('biddingSendTime')
                ->label('Orario consegna')
                ->columnSpan(3)
                ->disabled(),
            TextInput::make('mode')
                ->label('Modalità')
                ->columnSpan(4),
            Placeholder::make('')->columnSpan(2),
            DatePicker::make('biddingOpeningDate')
                ->label('Data apertura offerte')
                ->columnSpan(4)
                ->disabled(),
            TimePicker::make('biddingOpeningTime')
                ->label('Orario apertura offerte')
                ->columnSpan(4)
                ->disabled(),
        ];
    }
}
