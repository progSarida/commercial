<?php

namespace App\Filament\User\Resources;

use App\Enums\BiddingPriorityType;
use App\Enums\BiddingProcedureType;
use App\Enums\BiddingProcessingState;
use App\Enums\ClientType;
use App\Filament\User\Resources\BiddingResource\Pages;
use App\Filament\User\Resources\BiddingResource\RelationManagers;
use App\Models\Bidding;
use App\Models\BiddingDataSource;
use App\Models\Client;
use App\Models\Province;
use App\Models\ServiceType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BiddingResource extends Resource
{
    protected static ?string $model = Bidding::class;

    public static ?string $pluralModelLabel = 'Gare';

    public static ?string $modelLabel = 'Gara';

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(24)
            ->schema([
                CheckboxList::make('serviceTypes')
                    ->label('Gara relativa al servizio di')
                    ->relationship('serviceTypes', 'name')
                    ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                    ->columns(6)
                    ->columnSpan(24)
                    ->gridDirection('row'),
                Select::make('client_type')
                    ->label('Tipo')
                    ->required()
                    ->live()
                    ->options(ClientType::class)
                    ->columnSpan(5),
                Select::make('client_id')
                    ->label('Ente')
                    ->hintAction(
                        Action::make('Nuovo')
                            ->icon('heroicon-o-user')
                            ->form(fn(Form $form) => ClientResource::modalForm($form))
                            ->modalHeading('Nuovo Cliente')
                            ->modalWidth('6xl')
                            ->action(function (array $data, callable $set) {
                                $client = new Client();
                                BiddingResource::saveClient($data, $client);
                                $set('client_type', $client->client_type);
                                $set('client_id', $client->id);
                                $set('province_id', $client->province_id);
                                $set('region_id', $client->region_id);
                            })
                    )
                    ->required()
                    ->relationship(
                        name: 'client', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, callable $get) => $get('client_type') ? $query->where('client_type', $get('client_type')) : $query->whereRaw('1 = 0')
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        $client = Client::find($state);
                        $set('province_id', $client->province_id);
                        $set('region_id', $client->region_id);
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->columnSpan(9),
                Select::make('province_id')
                    ->label('Provincia')
                    ->required()
                    ->relationship( name: 'province', titleAttribute: 'name', )
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(5),
                Select::make('region_id')
                    ->label('Regione')
                    ->required()
                    ->relationship( name: 'region', titleAttribute: 'name', )
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(5),
                Textarea::make('description')
                    ->label('Descrizione')
                    ->columnSpan(24)
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Importo')
                    ->columnSpan(4)
                    ->prefix('€')
                    ->inputMode('decimal')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float)$state, 2, ',', '.') : '')
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state) return null;
                        if (str_contains($state, ',')) {
                            $value = str_replace('.', '', $state);
                            $value = str_replace(',', '.', $value);
                        } else {
                            $value = $state;
                        }
                        return (float)$value;
                    }),
                TextInput::make('residents')
                    ->label('Abitanti')
                    ->columnSpan(4)
                    ->inputMode('numeric')
                    ->formatStateUsing(fn ($state) => $state ? number_format((int)$state, 0, ',', '.') : '')
                    ->dehydrateStateUsing(fn ($state) => $state ? (int)str_replace(['.', ','], '', $state) : null),
                Select::make('bidding_state_id')
                    ->label('Stato gara')
                    ->relationship( 
                        name: 'biddingState', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn ($query) => $query->orderBy('position')
                    )
                    ->columnSpan(5),
                Select::make('bidding_processing_state')
                    ->label('Stato lavorazione')
                    ->live()
                    ->options(BiddingProcessingState::class)
                    ->columnSpan(6),
                Select::make('bidding_priority_type')
                    ->label('Priorità')
                    ->live()
                    ->options(BiddingPriorityType::class)
                    ->columnSpan(5),
                Textarea::make('bidding_note')
                    ->label('Note gara')
                    ->columnSpan(24)
                    ->maxLength(255),
                Select::make('bidding_type_id')
                    ->label('Tipo gara')
                    ->relationship( 
                        name: 'biddingType', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn ($query) => $query->orderBy('position')
                    )
                    ->columnSpan(6),
                Select::make('bidding_adjudication_type_id')
                    ->label('Tipo aggiudicazione')
                    ->relationship( 
                        name: 'biddingAdjudicationType', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn ($query) => $query->orderBy('position')
                    )
                    ->columnSpan(8),
                DatePicker::make('clarification_request_deadline_date')
                    ->label('Data scadenza chiarimenti')
                    ->columnSpan(5),
                TimePicker::make('clarification_request_deadline_time')
                    ->label('Orario scadenza chiarimenti')
                    ->default('06:00')
                    ->columnSpan(5),
                Checkbox::make('mandatory_inspection')
                    ->label('Sopralluogo obbligatorio')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if($state)
                            $set('inspection_deadline_time', '06:00');
                        else
                            $set('inspection_deadline_time', null);
                    })
                    ->columnSpan(5),
                DatePicker::make('inspection_deadline_date')
                    ->label('Data scadenza sopralluogo')
                    ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                    ->columnSpan(5),
                TimePicker::make('inspection_deadline_time')
                    ->label('Orario scadenza sopralluogo')
                    ->default(fn (callable $get) => $get('mandatory_inspection') ? '06:00' : null)
                    ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                    ->columnSpan(5),
                DatePicker::make('deadline_date')
                    ->label('Data scadenza gara')
                    ->required()
                    ->columnSpan(4),
                TimePicker::make('deadline_time')
                    ->label('Orario scadenza gara')
                    ->default('06:00')
                    ->required()
                    ->columnSpan(5),
                DatePicker::make('send_date')
                    ->label('Data invio offerta')
                    ->columnSpan(6),
                TimePicker::make('send_time')
                    ->label('Orario invio offerta')
                    ->default('06:00')
                    ->columnSpan(6),
                DatePicker::make('opening_date')
                    ->label('Data apertura offerte')
                    ->columnSpan(6),
                TimePicker::make('opening_time')
                    ->label('Orario apertura offerte')
                    ->default('06:00')
                    ->columnSpan(6),
                TextInput::make('contact')
                    ->label('Nome contatto')
                    ->columnSpan(10),
                TextInput::make('note')
                    ->label('Note')
                    ->columnSpan(14),
                TextInput::make('contracting_station')
                    ->label('Gestore appalto')
                    ->columnSpan(17),
                Select::make('bidding_procedure_type')
                    ->label('Procedura')
                    ->live()
                    ->options(BiddingProcedureType::class)
                    ->columnSpan(7),
                TextInput::make('procedure_portal')
                    ->label('Portale procedura')
                    ->columnSpan(8),
                TextInput::make('cig')
                    ->label('CIG')
                    ->columnSpan(8),
                TextInput::make('procedure_id')
                    ->label('ID Procedura')
                    ->columnSpan(8),
                TextInput::make('year')
                    ->label('Durata anni')
                    ->columnSpan(6),
                TextInput::make('month')
                    ->label('Durata mesi')
                    ->columnSpan(6),
                TextInput::make('day')
                    ->label('Durata giorni')
                    ->columnSpan(6),
                DatePicker::make('renew')
                    ->label('Rinnovo')
                    ->columnSpan(6),
                Select::make('assigned_user_id')
                    ->label('Assegnato a')
                    ->relationship('assignedUser', 'name')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->columns(6)
                    ->columnSpan(8),
                Select::make('modified_user_id')
                    ->label('Modificato da')
                    ->relationship('modifiedUser', 'name')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->columns(6)
                    ->disabled()
                    ->dehydrated()
                    ->visible(fn ($state) => $state !== null)
                    ->columnSpan(8),
                Placeholder::make('')
                    ->label('Ultima modifica: ')
                    ->content(fn ($record) => $record?->updated_at?->format('d/m/Y') ?? '')
                    ->visible(fn (callable $get) => $get('modified_user_id') !== null)
                    ->columnSpan(8),
                Placeholder::make('')
                    ->visible(fn (callable $get) => $get('modified_user_id') === null)
                    ->columnSpan(16),
                Select::make('source1_id')
                    ->label('Fonte dati 1')
                    ->relationship('source1', 'name')
                    ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                    ->columnSpan(8),
                Select::make('source2_id')
                    ->label('Fonte dati 2')
                    ->relationship('source2', 'name')
                    ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                    ->columnSpan(8),
                Select::make('source3_id')
                    ->label('Fonte dati 3')
                    ->relationship('source3', 'name')
                    ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                    ->columnSpan(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceTypes')
                    ->label('Servizi')
                    ->formatStateUsing(function ($record) {
                        return $record->serviceTypes->pluck('name')->join(' - ');
                    }),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('client.name')
                    ->label('Ente'),
                TextColumn::make('province.name')
                    ->label('Prov.'),
                TextColumn::make('deadline_date')
                    ->label('Gara')
                    ->date('d/m/Y'),
                TextColumn::make('inspection_deadline_date')
                    ->label('Sopralluogo')
                    ->date('d/m/Y'),
                TextColumn::make('clarification_request_deadline_date')
                    ->label('Chiarimenti')
                    ->date('d/m/Y'),
                TextColumn::make('biddingType.name')
                    ->label('Tipo gara'),
                TextColumn::make('biddingState.name')
                    ->label('Stato gara'),
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
            'index' => Pages\ListBiddings::route('/'),
            'create' => Pages\CreateBidding::route('/create'),
            'edit' => Pages\EditBidding::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestione';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function saveClient(array $data, Client $client): void
    {
        $client->name = $data['type'];
        $client->client_type = $data['client_type'];
        $client->phone = $data['phone'];
        $client->email = $data['email'];
        $client->site = $data['site'];
        $client->state_id = $data['state_id'];
        $client->region_id = $data['region_id'];
        $client->province_id = $data['province_id'];
        $client->city_id = $data['city_id'];
        $client->place = $data['place'];
        $client->zip_code = $data['zip_code'];
        $client->address = $data['address'];
        $client->civic = $data['civic'];
        $client->note = $data['note'];
        $client->save();
        Notification::make()
            ->title('Cliente salvato con successo')
            ->success()
            ->send();
    }
}
