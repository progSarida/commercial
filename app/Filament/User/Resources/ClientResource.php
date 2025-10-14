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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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

    public static function form(Form $form): Form
    {
        $italyId = State::where('name', 'Italy')->first()?->id;
        return $form
            ->columns(12)
            ->schema([
                Select::make('client_type')->label('Tipo cliente')
                    ->options(ClientType::class)
                    ->required()
                    ->live()
                    ->columnSpan(3),

                // Select per name (visibile solo per CITY e PROVINCE)
                Select::make('name')->label('Nome')
                    ->options(function (callable $get, $record) {
                        $type = $get('client_type');
                        // Ottieni i nomi già usati nella tabella clients per il tipo selezionato
                        $usedNames = Client::where('client_type', $type)->pluck('name')->toArray();
                        // In modalità edit, includi il nome del cliente corrente (se esiste)
                        $currentName = $record ? $record->name : null;

                        if ($type === ClientType::CITY->value) {
                            return City::whereNotIn('name', $usedNames)
                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                ->pluck('name', 'name')
                                ->toArray();
                        } elseif ($type === ClientType::PROVINCE->value) {
                            return Province::whereNotIn('name', $usedNames)
                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                ->pluck('name', 'name')
                                ->toArray();
                        }
                        return [];
                    })
                    ->visible(fn (callable $get) => in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                    ->searchable()
                    ->required()
                    ->live()
                    ->columnSpan(6)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $type = $get('client_type');
                        $italyId = State::where('name', 'Italy')->first()?->id;

                        if ($type === ClientType::CITY->value) {
                            $city = City::with('province.region')->where('name', $state)->first();
                            if ($city) {
                                $set('city_id', $city->id);
                                $set('province_id', $city->province_id);
                                $set('region_id', $city->province?->region_id);
                                $set('zip_code', $city->zip_code);
                                $set('state_id', $italyId);
                            }
                        } elseif ($type === ClientType::PROVINCE->value) {
                            $province = Province::with('region')->where('name', $state)->first();
                            if ($province) {
                                $set('province_id', $province->id);
                                $set('region_id', $province->region_id);
                                $set('state_id', $italyId);
                            }
                        }
                    }),
                TextInput::make('name')->label('Nome')
                    ->visible(fn (callable $get) => !in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                    ->required()
                    ->rules(function ($record) {
                        return ['unique:clients,name' . ($record ? ',' . $record->id : '')];
                    })
                    ->columnSpan(6),
                Select::make('state_id')->label('Paese')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'state', titleAttribute: 'name')
                    ->default($italyId)
                    ->afterStateUpdated(function (callable $set, callable $get, ) {
                        $newStateId = $get('state_id');
                        $italyId = State::where('name', 'Italy')->first()?->id;
                        // Pulisci i campi solo se cambi verso uno stato diverso da Italy
                        if ($newStateId !== $italyId) {
                            $set('place', null);
                            $set('region_id', null);
                            $set('province_id', null);
                            $set('city_id', null);
                            $set('zip_code', null);
                        } else {
                            // Se torni a Italy, pulisci solo il 'place'
                            $set('place', null);
                        }
                    })
                    ->columnSpan(3),
                Select::make('region_id')->label('Regione')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'region', titleAttribute: 'name')
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(3),
                Select::make('province_id')->label('Provincia')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(
                        name: 'province',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, callable $get) => $get('region_id') ? $query->where('region_id', $get('region_id')) : $query->whereRaw('1 = 1')
                    )
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(3),
                Select::make('city_id')->label('Comune')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(
                        name: 'city',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, callable $get) => $get('province_id') ? $query->where('province_id', $get('province_id')) : $query->whereRaw('1 = 1')
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $city = City::with(['province.region'])->find($state);
                            $set('zip_code', $city?->zip_code);
                            $set('province_id', $city?->province_id);
                            $set('region_id', $city?->province?->region_id);
                        } else {
                            $set('zip_code', null);
                            $set('province_id', null);
                            $set('region_id', null);
                        }
                    })
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(5),
                TextInput::make('place')->label('Luogo')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') !== $italyId)
                    ->columnSpan(12),
                TextInput::make('zip_code')->label('CAP')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
                Placeholder::make('')
                    ->label('')
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(4),
                Placeholder::make('')
                    ->label('')
                    ->visible(fn (callable $get) => $get('state_id') !== $italyId)
                    ->columnSpan(4),
                TextInput::make('address')->label('Indirizzo')
                    ->required()
                    ->columnSpan(6),
                TextInput::make('civic')->label('Civico')
                    ->required()
                    ->columnSpan(2),
                TextInput::make('phone')->label('Telefono')
                    ->tel()
                    ->required()
                    ->columnSpan(3),
                TextInput::make('email')->label('Email')
                    ->email()
                    ->required()
                    ->columnSpan(5),
                TextInput::make('site')->label('Sito')
                    ->columnSpan(4),
                Textarea::make('note')->label('Note')
                    ->columnSpan(12),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('address')->label('Indirizzo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('civic')->label('Civico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden($isMobile),
                TextColumn::make('note')->label('Note')
                    ->searchable()
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
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Clienti';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function modalForm(Form $form): Form
    {
        $italyId = State::where('name', 'Italy')->first()->id;

        return $form
            ->columns(12)
            ->schema([
                Select::make('client_type')->label('Tipo cliente')
                    ->options(ClientType::class)
                    ->required()
                    ->live()
                    ->columnSpan(3),

                // Select per name (visibile solo per CITY e PROVINCE)
                Select::make('name')->label('Nome')
                    ->options(function (callable $get, $record) {
                        $type = $get('client_type');
                        // Ottieni i nomi già usati nella tabella clients per il tipo selezionato
                        $usedNames = Client::where('client_type', $type)->pluck('name')->toArray();
                        // In modalità edit, includi il nome del cliente corrente (se esiste)
                        $currentName = $record ? $record->name : null;

                        if ($type === ClientType::CITY->value) {
                            return City::whereNotIn('name', $usedNames)
                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                ->pluck('name', 'name')
                                ->toArray();
                        } elseif ($type === ClientType::PROVINCE->value) {
                            return Province::whereNotIn('name', $usedNames)
                                ->when($currentName, fn ($query) => $query->orWhere('name', $currentName))
                                ->pluck('name', 'name')
                                ->toArray();
                        }
                        return [];
                    })
                    ->visible(fn (callable $get) => in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                    ->searchable()
                    ->required()
                    ->live()
                    ->columnSpan(6)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $type = $get('client_type');
                        $italyId = State::where('name', 'Italy')->first()?->id;

                        if ($type === ClientType::CITY->value) {
                            $city = City::with('province.region')->where('name', $state)->first();
                            if ($city) {
                                $set('city_id', $city->id);
                                $set('province_id', $city->province_id);
                                $set('region_id', $city->province?->region_id);
                                $set('zip_code', $city->zip_code);
                                $set('state_id', $italyId);
                            }
                        } elseif ($type === ClientType::PROVINCE->value) {
                            $province = Province::with('region')->where('name', $state)->first();
                            if ($province) {
                                $set('province_id', $province->id);
                                $set('region_id', $province->region_id);
                                $set('state_id', $italyId);
                            }
                        }
                    }),
                TextInput::make('name')->label('Nome')
                    ->visible(fn (callable $get) => !in_array($get('client_type'), [ClientType::CITY->value, ClientType::PROVINCE->value]))
                    ->required()
                    ->rules(function ($record) {
                        return ['unique:clients,name' . ($record ? ',' . $record->id : '')];
                    })
                    ->columnSpan(6),
                Select::make('state_id')->label('Paese')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'state', titleAttribute: 'name')
                    ->default($italyId)
                    ->afterStateUpdated(function (callable $set, callable $get, ) {
                        $newStateId = $get('state_id');
                        $italyId = State::where('name', 'Italy')->first()?->id;
                        // Pulisci i campi solo se cambi verso uno stato diverso da Italy
                        if ($newStateId !== $italyId) {
                            $set('place', null);
                            $set('region_id', null);
                            $set('province_id', null);
                            $set('city_id', null);
                            $set('zip_code', null);
                        } else {
                            // Se torni a Italy, pulisci solo il 'place'
                            $set('place', null);
                        }
                    })
                    ->columnSpan(3),
                Select::make('region_id')->label('Regione')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'region', titleAttribute: 'name')
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(3),
                Select::make('province_id')->label('Provincia')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(
                        name: 'province',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, callable $get) => $get('region_id') ? $query->where('region_id', $get('region_id')) : $query->whereRaw('1 = 1')
                    )
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(3),
                Select::make('city_id')->label('Comune')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(
                        name: 'city',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, callable $get) => $get('province_id') ? $query->where('province_id', $get('province_id')) : $query->whereRaw('1 = 1')
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $city = City::with(['province.region'])->find($state);
                            $set('zip_code', $city?->zip_code);
                            $set('province_id', $city?->province_id);
                            $set('region_id', $city?->province?->region_id);
                        } else {
                            $set('zip_code', null);
                            $set('province_id', null);
                            $set('region_id', null);
                        }
                    })
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(5),
                TextInput::make('place')->label('Luogo')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') !== $italyId)
                    ->columnSpan(12),
                TextInput::make('zip_code')->label('CAP')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
                Placeholder::make('')
                    ->label('')
                    ->visible(fn (callable $get) => $get('state_id') === $italyId)
                    ->columnSpan(4),
                Placeholder::make('')
                    ->label('')
                    ->visible(fn (callable $get) => $get('state_id') !== $italyId)
                    ->columnSpan(4),
                TextInput::make('address')->label('Indirizzo')
                    ->required()
                    ->columnSpan(6),
                TextInput::make('civic')->label('Civico')
                    ->required()
                    ->columnSpan(2),
                TextInput::make('phone')->label('Telefono')
                    ->tel()
                    ->required()
                    ->columnSpan(3),
                TextInput::make('email')->label('Email')
                    ->email()
                    ->required()
                    ->columnSpan(5),
                TextInput::make('site')->label('Sito')
                    ->columnSpan(4),
                Textarea::make('note')->label('Note')
                    ->columnSpan(12),
            ]);
    }
}
