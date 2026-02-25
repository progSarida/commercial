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
            TextInput::make('client_name_virtual')
                ->label('Ente')
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled()
                ->dehydrated(false),
            TextInput::make('residents_virtual')
                ->label('Abitanti')
                ->extraInputAttributes(['class' => 'text-right'])
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TextInput::make('client_address_virtual')
                ->label('Indirizzo')
                ->columnSpan(['sm' => 'full', 'md' => 6])
                ->disabled(),
            // Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>4]),
            //
            TextInput::make('client_zipcode_virtual')
                ->label('CAP')
                ->columnSpan(['sm' => 'full', 'md' => 1])
                ->disabled(),
            TextInput::make('client_province_virtual')
                ->label('Provincia')
                ->columnSpan(['sm' => 'full', 'md' => 3])
                ->disabled(),
            TextInput::make('client_region_virtual')
                ->label('Regione')
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TextInput::make('client_phone_virtual')
                ->label('Telefono')
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TextInput::make('client_email_virtual')
                ->label('Email')
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled(),
            // Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>2]),
            //
            TextInput::make('manage_current')
                ->label('Gestione attuale')
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            TextInput::make('manage_offer')
                ->label('Gestione offerta')
                ->columnSpan(['sm' => 'full', 'md' => 6]),
            //
            TextInput::make('bidding_cig_virtual')
                ->label('CIG')
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TextInput::make('revenue')
                ->label('Gettito')
                ->columnSpan(['sm' => 'full', 'md' => 2]),
            TextInput::make('conditions')
                ->label('Condizioni')
                ->columnSpan(['sm' => 'full', 'md' => 8]),
            CheckboxList::make('bidding_service_types_virtual')
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
            Checkbox::make('bidding_inspection_virtual')
                ->label('Sopralluogo obbligatorio')
                ->disabled()
                ->columnSpan(['sm' => 'full', 'md' => 3]),
            TextInput::make('bidding_duration_virtual')
                ->label('Durata')
                ->columnSpan(['sm' => 'full', 'md' => 4])
                ->disabled(),
            Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>3]),
            DatePicker::make('bidding_send_date_virtual')
                ->label('Data consegna')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TimePicker::make('bidding_send_time_virtual')
                ->label('Orario consegna')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TextInput::make('mode')
                ->label('Modalità')
                ->columnSpan(['sm' => 'full', 'md' => 4]),
            // Placeholder::make('')->columnSpan(['sm' => 0, 'md' =>2]),
            DatePicker::make('bidding_opening_date_virtual')
                ->label('Data apertura offerte')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
            TimePicker::make('bidding_opening_time_virtual')
                ->label('Orario apertura offerte')
                ->extraInputAttributes(['class' => 'text-center'])
                ->columnSpan(['sm' => 'full', 'md' => 2])
                ->disabled(),
        ];
    }
}
