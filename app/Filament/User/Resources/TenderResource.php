<?php

namespace App\Filament\User\Resources;

use App\Enums\BiddingProcessingState;
use App\Filament\User\Resources\TenderResource\Tabs\GeneralDataTab;
use App\Filament\User\Resources\TenderResource\Tabs\ProcedureTypeTab;
use App\Filament\User\Resources\TenderResource\Tabs\RequiredDocumentsTab;
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

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('client_id')
                    ->label('Ente')
                    ->columnSpan(['sm' => 'full', 'md' => 4])
                    ->live()
                    ->preload()
                    ->disabled(fn (callable $get) => $get('bidding_id'))
                    ->dehydrated(fn (callable $get) => $get('bidding_id'))
                    ->relationship(name: 'client', titleAttribute: 'name'),
                Select::make('bidding_id')
                    ->label('Gara')
                    ->columnSpan(['sm' => 'full', 'md' => 6])
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
                            }
                            static::updateFormFields($bidding, $client, $set);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        $bidding = static::getBiddingModel($record, $state);
                        if ($bidding) {
                            $client = Client::find($bidding->client_id);
                            static::updateFormFields($bidding, $client, $set);
                        }
                    }),
                Select::make('biddingProcessingState')
                    ->label('Stato gara')
                    ->columnSpan(['sm' => 'full', 'md' => 2])
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
                            ->schema(GeneralDataTab::make()),
                        Tab::make('Procedura')
                            ->columns(12)
                            ->schema(ProcedureTypeTab::make()),
                        Tab::make('Documenti')
                            ->columns(12)
                            ->schema(RequiredDocumentsTab::make()),
                    ])
                    ->columnSpan(['sm' => 'full', 'md' => 12])
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

    /**
     * Updates form fields based on the selected bidding.
     *
     * @param Bidding|null $bidding The bidding model.
     * @param Client|null $client The client model.
     * @param callable $set Form state setter.
     * @return void
     */
    private static function updateFormFields(?Bidding $bidding, Client $client, callable $set): void
    {
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
        $set('biddingMandatoryInspection', $bidding->mandatory_inspection);
        $set('biddingMandatoryInspectionDeadline', $bidding->inspection_deadline_date);
    }
}