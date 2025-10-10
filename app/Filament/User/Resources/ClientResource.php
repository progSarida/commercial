<?php

namespace App\Filament\User\Resources;

use App\Enums\ClientType;
use App\Filament\User\Resources\ClientResource\Pages;
use App\Filament\User\Resources\ClientResource\RelationManagers;
use App\Filament\User\Resources\ClientResource\RelationManagers\ContactsRelationManager;
use App\Models\City;
use App\Models\Client;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    public static ?string $pluralModelLabel = 'Elenco clienti';

    public static ?string $modelLabel = 'Cliente';

    protected static ?string $navigationIcon = 'fas-users';

    public static function form(Form $form): Form
    {
        $italyId = State::where('name', 'Italy')->first()->id;
        return $form
            ->schema([
                TextInput::make('name')->label('Nome')
                    ->required(),
                Select::make('client_type')->label('Tipo cliente')
                    ->options(ClientType::class)
                    ->required(),
                TextInput::make('phone')->label('Telefono')
                    ->tel()
                    ->required(),
                TextInput::make('email')->label('Email')
                    ->email()
                    ->required(),
                Select::make('state_id')->label('Paese')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'state', titleAttribute: 'name')
                    ->default($italyId),
                Select::make('region_id')->label('Regione')
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->relationship(name: 'region', titleAttribute: 'name')
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
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
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
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
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
                TextInput::make('place')->label('Luogo')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') !== $italyId),
                TextInput::make('zip_code')->label('CAP')
                    ->required()
                    ->visible(fn (callable $get) => $get('state_id') === $italyId),
                TextInput::make('site')->label('Sito'),
                TextInput::make('address')->label('Indirizzo')
                    ->required(),
                TextInput::make('civic')->label('Civico')
                    ->required(),
                Textarea::make('note')->label('Note'),
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
            ContactsRelationManager::class
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
}
