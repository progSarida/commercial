<?php

namespace App\Filament\User\Resources;

use App\Enums\ClientType;
use App\Filament\User\Resources\ClientResource\Pages;
use App\Filament\User\Resources\ClientResource\RelationManagers;
use App\Filament\User\Resources\ClientResource\RelationManagers\ClientServicesRelationManager;
use App\Filament\User\Resources\ClientResource\RelationManagers\ContactsRelationManager;
use App\Models\City;
use App\Models\Client;
use App\Models\Province;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Jenssegers\Agent\Agent;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    public static ?string $pluralModelLabel = 'Elenco clienti';
    public static ?string $modelLabel = 'Cliente';
    protected static ?string $navigationIcon = 'fas-users';
    protected static ?string $navigationGroup = 'Clienti';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        // Recupero l'ID con una fallback sicura
        $italyId = State::where('name', 'Italy')->first()?->id;

        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Tabs::make('Schede')
                    ->tabs([
                        // TAB 1: CONTATTO
                        Forms\Components\Tabs\Tab::make('Dati cliente')
                            ->schema([
                                Select::make('client_type')->label('Tipo cliente')
                                    ->options(ClientType::class)
                                    ->required()
                                    ->live()
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                // Select dinamica per Nome (se Comune o Provincia)
                                Select::make('name')->label('Nome')
                                    ->options(function (callable $get, $record) {
                                        $type = $get('client_type');
                                        $usedNames = Client::where('client_type', $type)->pluck('name')->toArray();
                                        $currentName = $record ? $record->name : null;

                                        if ($type === ClientType::CITY->value) {
                                            return City::whereNotIn('name', $usedNames)
                                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                                ->pluck('name', 'name')->toArray();
                                        } elseif ($type === ClientType::PROVINCE->value) {
                                            return Province::whereNotIn('name', $usedNames)
                                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                                ->pluck('name', 'name')->toArray();
                                        }
                                        return [];
                                    })
                                    ->visible(fn (callable $get) => in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(['sm' => 'full', 'md' => 6])
                                    ->afterStateUpdated(function ($state, callable $set) use ($italyId) {
                                        // Logica di auto-compilazione
                                        $city = City::with('province.region')->where('name', $state)->first();
                                        if ($city) {
                                            $set('city_id', $city->id);
                                            $set('province_id', $city->province_id);
                                            $set('region_id', $city->province?->region_id);
                                            $set('zip_code', $city->zip_code);
                                            $set('state_id', $italyId);
                                        }
                                    }),

                                // Input di testo per Nome (se altri tipi)
                                TextInput::make('name')->label('Nome')
                                    ->visible(fn (callable $get) => !in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                                    ->required()
                                    ->rules(fn ($record) => ['unique:clients,name' . ($record ? ',' . $record->id : '')])
                                    ->columnSpan(['sm' => 'full', 'md' => 6]),

                                Select::make('state_id')->label('Paese')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->relationship(name: 'state', titleAttribute: 'name')
                                    ->default($italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('region_id')->label('Regione')
                                    ->required()
                                    ->live()
                                    ->relationship(name: 'region', titleAttribute: 'name')
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('province_id')->label('Provincia')
                                    ->required()
                                    ->live()
                                    ->relationship(
                                        name: 'province',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query, callable $get) => $get('region_id') ? $query->where('region_id', $get('region_id')) : $query
                                    )
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('city_id')->label('Comune')
                                    ->required()
                                    ->live()
                                    ->relationship(
                                        name: 'city',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query, callable $get) => $get('province_id') ? $query->where('province_id', $get('province_id')) : $query
                                    )
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3])
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $city = City::find($state);
                                            $set('zip_code', $city?->zip_code);
                                        }
                                    }),

                                TextInput::make('place')->label('Luogo')
                                    ->visible(fn (callable $get) => $get('state_id') != $italyId)
                                    ->columnSpanFull(),

                                TextInput::make('zip_code')->label('CAP')
                                    ->columnSpan(['sm' => 'full', 'md' => 2]),

                                TextInput::make('address')->label('Indirizzo')->columnSpan(6),
                                TextInput::make('civic')->label('Civico')->columnSpan(2),
                                TextInput::make('phone')->tel()->label('Telefono')->columnSpan(3),
                                TextInput::make('email')->email()->label('Email')->columnSpan(5),
                                TextInput::make('site')->label('Sito')->columnSpan(4),
                                Textarea::make('note')->label('Note')->columnSpanFull(),
                            ])->columns(12),

                        // TAB 2: REFERENTI
                        Forms\Components\Tabs\Tab::make('Referenti')
                            ->schema([
                                Forms\Components\Repeater::make('referents')
                                    ->label('')
                                    ->relationship('referents')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')->label('Nome')->required()->columnSpan(4),
                                        Forms\Components\TextInput::make('title')->label('Qualifica')->columnSpan(4),
                                        Forms\Components\TextInput::make('phone')->label('Telefono')->columnSpan(2),
                                        Forms\Components\TextInput::make('smart')->label('Cellulare')->columnSpan(2),
                                        Forms\Components\TextInput::make('email')->email()->columnSpanFull(),
                                        Forms\Components\Textarea::make('note')->rows(2)->columnSpanFull(),
                                    ])
                                    ->columns(12)
                                    ->collapsed(fn ($record) => $record)
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuovo Referente'),
                            ]),
                    ])->columnSpanFull(), // Fine Tabs
            ]);
    }

    public static function table(Table $table): Table
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile();

        return $table
            ->columns([
                TextColumn::make('region.name')->label('Regione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('province.name')->label('Provincia')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('client_type')->label('Tipo cliente')
                    // ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')->label('Nome cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')->label('Telefono')
                    ->searchable(),
                TextColumn::make('email')->label('Email')
                    ->searchable(),
                TextColumn::make('state.name')->label('Paese')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('city_id')->label('Comune')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('place')->label('Luogo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('zip_code')->label('CAP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('site')->label('Sito')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('address')->label('Indirizzo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('civic')->label('Civico')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('note')->label('Note')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
            ])
            ->filters([
                SelectFilter::make('client_type')->label('Tipo cliente')
                    ->options(ClientType::class)
                    ->multiple()->preload(),
                SelectFilter::make('region_id')->label('Regione')
                    ->relationship(name: 'region', titleAttribute: 'name')
                    ->searchable()
                    ->preload()->optionsLimit(5),
                SelectFilter::make('province_id')->label('Provincia')
                    ->relationship(name: 'province', titleAttribute: 'name')
                    ->searchable()
                    ->preload()->optionsLimit(5),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            ClientServicesRelationManager::class,
            ContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }

    public static function modalForm(Form $form): Form
    {
        $italyId = State::where('name', 'Italy')->first()?->id;

        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Tabs::make('Schede')
                    ->tabs([
                        // TAB 1: CONTATTO
                        Forms\Components\Tabs\Tab::make('Dati cliente')
                            ->schema([
                                Forms\Components\Hidden::make('id')
                                    ->default(fn ($record) => $record?->client?->id),

                                Select::make('client_type')->label('Tipo cliente')
                                    ->options(ClientType::class)
                                    ->required()
                                    ->live()
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                // Select dinamica per Nome (se Comune o Provincia)
                                Select::make('name')->label('Nome')
                                    ->options(function (callable $get, $record) {
                                        $type = $get('client_type');
                                        $usedNames = Client::where('client_type', $type)->pluck('name')->toArray();
                                        $currentName = $record ? $record->name : null;

                                        if ($type === ClientType::CITY->value) {
                                            return City::whereNotIn('name', $usedNames)
                                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                                ->pluck('name', 'name')->toArray();
                                        } elseif ($type === ClientType::PROVINCE->value) {
                                            return Province::whereNotIn('name', $usedNames)
                                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                                ->pluck('name', 'name')->toArray();
                                        }
                                        return [];
                                    })
                                    ->visible(fn (callable $get) => in_array($get('client_type'), [ClientType::CITY, ClientType::PROVINCE]))
                                    // ->visible(function (callable $get) { dd($get('client_type')); })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(['sm' => 'full', 'md' => 6])
                                    ->afterStateUpdated(function ($state, callable $set) use ($italyId) {
                                        // Logica di auto-compilazione
                                        $city = City::with('province.region')->where('name', $state)->first();
                                        if ($city) {
                                            $set('city_id', $city->id);
                                            $set('province_id', $city->province_id);
                                            $set('region_id', $city->province?->region_id);
                                            $set('zip_code', $city->zip_code);
                                            $set('state_id', $italyId);
                                        }
                                    }),

                                // Input di testo per Nome (se altri tipi)
                                TextInput::make('name')->label('Nome')
                                    ->visible(fn (callable $get) => !in_array($get('client_type'), [ClientType::CITY, ClientType::PROVINCE]))
                                    ->required()
                                    ->rules(function (Get $get) {
                                        $clientId = $get('id'); // Prende l'ID dal campo nascosto
                                        return [
                                            \Illuminate\Validation\Rule::unique('clients', 'name')->ignore($clientId)
                                        ];
                                    })
                                    ->columnSpan(['sm' => 'full', 'md' => 6]),

                                Select::make('state_id')->label('Paese')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->relationship(name: 'state', titleAttribute: 'name')
                                    ->default($italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('region_id')->label('Regione')
                                    ->required()
                                    ->live()
                                    ->relationship(name: 'region', titleAttribute: 'name')
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('province_id')->label('Provincia')
                                    ->required()
                                    ->live()
                                    ->relationship(
                                        name: 'province',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query, callable $get) => $get('region_id') ? $query->where('region_id', $get('region_id')) : $query
                                    )
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),

                                Select::make('city_id')->label('Comune')
                                    ->required()
                                    ->live()
                                    ->relationship(
                                        name: 'city',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query, callable $get) => $get('province_id') ? $query->where('province_id', $get('province_id')) : $query
                                    )
                                    ->visible(fn (callable $get) => $get('state_id') == $italyId)
                                    ->columnSpan(['sm' => 'full', 'md' => 3])
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $city = City::find($state);
                                            $set('zip_code', $city?->zip_code);
                                        }
                                    }),

                                TextInput::make('place')->label('Luogo')
                                    ->visible(fn (callable $get) => $get('state_id') != $italyId)
                                    ->columnSpanFull(),

                                TextInput::make('zip_code')->label('CAP')
                                    ->columnSpan(['sm' => 'full', 'md' => 2]),

                                TextInput::make('address')->label('Indirizzo')->columnSpan(6),
                                TextInput::make('civic')->label('Civico')->columnSpan(2),
                                TextInput::make('phone')->tel()->label('Telefono')->columnSpan(3),
                                TextInput::make('email')->email()->label('Email')->columnSpan(5),
                                TextInput::make('site')->label('Sito')->columnSpan(4),
                                Textarea::make('note')->label('Note')->columnSpanFull(),
                            ])->columns(12),

                        // TAB 2: REFERENTI
                        Forms\Components\Tabs\Tab::make('Referenti')
                            ->schema([
                                Forms\Components\Repeater::make('referents')
                                    ->label('')
                                    // ->relationship('referents')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')->label('Nome')->required()->columnSpan(4),
                                        Forms\Components\TextInput::make('title')->label('Qualifica')->columnSpan(4),
                                        Forms\Components\TextInput::make('phone')->label('Telefono')->columnSpan(2),
                                        Forms\Components\TextInput::make('smart')->label('Cellulare')->columnSpan(2),
                                        Forms\Components\TextInput::make('email')->email()->columnSpanFull(),
                                        Forms\Components\Textarea::make('note')->rows(2)->columnSpanFull(),
                                    ])
                                    ->columns(12)
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuovo Referente'),
                            ]),
                    ])->columnSpanFull(), // Fine Tabs
            ]);
    }
}
