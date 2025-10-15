<?php

namespace App\Filament\User\Resources;

use App\Enums\BiddingProcessingState;
use App\Filament\User\Resources\TenderResource\Pages;
use App\Filament\User\Resources\TenderResource\RelationManagers;
use App\Models\Bidding;
use App\Models\Client;
use App\Models\ServiceType;
use App\Models\Tender;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenderResource extends Resource
{
    protected static ?string $model = Tender::class;

    public static ?string $pluralModelLabel = 'Appalti';

    public static ?string $modelLabel = 'appalto';

    protected static ?string $navigationIcon = 'fas-suitcase';

    /**
     * Helper function to retrieve the Bidding model based on record or bidding_id.
     *
     * @param mixed $record The Tender record (null in create mode).
     * @param mixed $biddingId The bidding_id from the form state (optional).
     * @return Bidding|null
     */
    protected static function getBiddingModel($record = null, $biddingId = null): ?Bidding
    {
        $id = $biddingId ?? ($record->bidding_id ?? null);
        return $id ? Bidding::find($id) : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('client_id')
                    ->label('')
                    ->prefix('Ente')
                    ->columnSpan(4)
                    ->live()
                    ->preload()
                    ->disabled(fn (callable $get) => $get('bidding_id'))
                    ->dehydrated(fn (callable $get) => $get('bidding_id'))
                    ->relationship(name: 'client', titleAttribute: 'name'),
                Select::make('bidding_id')
                    ->label('')
                    ->prefix('Gara')
                    ->columnSpan(6)
                    ->live()
                    ->searchable()
                    ->preload()
                    ->relationship(
                        name: 'bidding', 
                        titleAttribute: 'description',
                        modifyQueryUsing: fn ($query, $get) => $query
                            ->where('bidding_processing_state', '!=', BiddingProcessingState::PENDING)
                            ->when($get('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
                    )
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $bidding = static::getBiddingModel(null, $state);
                        if ($bidding) {
                            $client = Client::find($bidding->client_id);
                            if (!$get('client_id')) {
                                $set('client_id', $client->id);
                                $set('clientName', $client->name);
                                $set('clientAddress', $client->address);
                                $set('clientZipcode', $client->zip_code);
                                $set('clientProvince', $client->province->name);
                                $set('clientRegion', $client->region->name);
                                $set('clientPhone', $client->phone);
                                $set('clientEmail', $client->email);
                            }
                            $set('residents', $bidding->residents);
                            $set('biddingCig', $bidding->cig);
                            $set('serviceTypes', $bidding->serviceTypes->pluck('id')->toArray());
                            $set('biddingInspection', $bidding->mandatory_inspection);
                            $string = '';
                            if ($bidding->year && $bidding->year > 0) $string .= $bidding->year . ' anni ';
                            if ($bidding->month && $bidding->month > 0) $string .= $bidding->month . ' mesi ';
                            if ($bidding->day && $bidding->day > 0) $string .= $bidding->day . ' giorni ';
                            $set('biddingDuration', $string);
                            $set('biddingSendDate', $bidding->send_date);
                            $set('biddingSendTime', $bidding->send_time);
                            $set('biddingOpeningDate', $bidding->opening_date);
                            $set('biddingOpeningTime', $bidding->opening_time);
                            $set('biddingProcessingState', $bidding->bidding_processing_state);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        $bidding = static::getBiddingModel($record, $state);
                        if ($bidding) {
                            $client = Client::find($bidding->client_id);
                            $set('client_id', $client->id);
                            $set('clientName', $client->name);
                            $set('clientAddress', $client->address);
                            $set('clientZipcode', $client->zip_code);
                            $set('clientProvince', $client->province->name);
                            $set('clientRegion', $client->region->name);
                            $set('clientPhone', $client->phone);
                            $set('clientEmail', $client->email);
                            $set('residents', $bidding->residents);
                            $set('biddingCig', $bidding->cig);
                            $set('serviceTypes', $bidding->serviceTypes->pluck('id')->toArray());
                            $set('biddingInspection', $bidding->mandatory_inspection);
                            $string = '';
                            if ($bidding->year && $bidding->year > 0) $string .= $bidding->year . ' anni ';
                            if ($bidding->month && $bidding->month > 0) $string .= $bidding->month . ' mesi ';
                            if ($bidding->day && $bidding->day > 0) $string .= $bidding->day . ' giorni ';
                            $set('biddingDuration', $string);
                            $set('biddingSendDate', $bidding->send_date);
                            $set('biddingSendTime', $bidding->send_time);
                            $set('biddingOpeningDate', $bidding->opening_date);
                            $set('biddingOpeningTime', $bidding->opening_time);
                            $set('biddingProcessingState', $bidding->bidding_processing_state);
                        }
                    }),
                Select::make('biddingProcessingState')
                    ->label('')
                    ->columnSpan(2)
                    ->options(BiddingProcessingState::class)
                    ->default(BiddingProcessingState::TODO)
                    ->afterStateUpdated(function ($state, callable $get) {
                        $biddingId = $get('bidding_id');
                        if ($biddingId && $state) {
                            $bidding = Bidding::find($biddingId);
                            if ($bidding && $bidding->bidding_processing_state !== $state) {
                                $bidding->bidding_processing_state = $state;
                                $bidding->save();
                            }
                        }
                    }),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Dati Generali')
                            ->columns(12)
                            ->schema([
                                TextInput::make('clientName')->label('')->prefix('Ente')->columnSpan(4)->disabled(),
                                TextInput::make('residents')->label('')->prefix('Abitanti')->columnSpan(2)->disabled(),
                                TextInput::make('clientAddress')->label('')->prefix('Indirizzo')->columnSpan(6)->disabled(),
                                Placeholder::make('')->columnSpan(4),
                                TextInput::make('clientZipcode')->label('')->prefix('CAP')->columnSpan(2)->disabled(),
                                TextInput::make('clientProvince')->label('')->prefix('Provincia')->columnSpan(3)->disabled(),
                                TextInput::make('clientRegion')->label('')->prefix('Regione')->columnSpan(3)->disabled(),
                                Placeholder::make('')->columnSpan(2),
                                TextInput::make('biddingCig')->label('')->prefix('CIG')->columnSpan(3)->disabled(),
                                TextInput::make('clientPhone')->label('')->prefix('Telefono')->columnSpan(3)->disabled(),
                                TextInput::make('clientEmail')->label('')->prefix('Email')->columnSpan(4)->disabled(),
                                TextInput::make('manage_current')->label('')->prefix('Gestione attuale')->columnSpan(6),
                                TextInput::make('manage_offer')->label('')->prefix('Gestione offerta')->columnSpan(6),
                                TextInput::make('revenue')->label('')->prefix('Gettito')->columnSpan(3),
                                TextInput::make('conditions')->label('')->prefix('Condizioni')->columnSpan(9),
                                CheckboxList::make('serviceTypes')->label('Gara relativa al servizio di')
                                    ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                                    ->columns(6)->columnSpan(12)->gridDirection('row')->disabled()->dehydrated(false),
                                Checkbox::make('invitation_require_check')->label('Richiesta invito')->columnSpan(2),
                                Checkbox::make('biddingInspection')->label('Sopralluogo obbligatorio')->disabled()->columnSpan(3),
                                TextInput::make('biddingDuration')->label('')->prefix('Durata')->columnSpan(4)->disabled(),
                                Placeholder::make('')->columnSpan(3),
                                DatePicker::make('biddingSendDate')->label('')->prefix('Data consegna')->columnSpan(3)->disabled(),
                                TimePicker::make('biddingSendTime')->label('')->prefix('Orario consegna')->columnSpan(3)->disabled(),
                                TextInput::make('mode')->label('')->prefix('Modalità')->columnSpan(4),
                                Placeholder::make('')->columnSpan(2),
                                DatePicker::make('biddingOpeningDate')->label('')->prefix('Data apertura offerte')->columnSpan(4)->disabled(),
                                TimePicker::make('biddingOpeningTime')->label('')->prefix('Orario apertura offerte')->columnSpan(4)->disabled(),
                            ]),
                        Tab::make('Tipo Procedura')
                            ->schema([
                                // ...
                            ]),
                        Tab::make('Documenti Richiesti')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->columnSpan(12)
                    ->activeTab(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestione';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}