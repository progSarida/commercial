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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

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
                    ->searchable()
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
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('bidding.serviceTypes')
                    ->label('Servizi')
                    ->formatStateUsing(function ($record) {
                        return $record->bidding->serviceTypes->pluck('name')->join(' - ');
                    }),
                TextColumn::make('bidding.description')
                    ->label('Gara')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->bidding->description),
                TextColumn::make('bidding.deadline_date')
                    ->label('Scadenza')
                    ->date('d/m/Y'),
                TextColumn::make('bidding.inspection_deadline_date')
                    ->label('Sopralluogo')
                    ->date('d/m/Y'),
                TextColumn::make('open_procedure_check')
                    ->label('Procedura aperta')
                    ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No')
                    ->sortable(),
            ])
            ->filtersFormWidth('xl')
            ->filtersFormColumns(6)
            ->filters([
                SelectFilter::make('bidding_id')
                    ->label('Gara')
                    ->searchable()
                    ->preload()
                    ->columnSpan(4)
                    ->relationship(
                        name: 'bidding',
                        titleAttribute: 'description',
                        modifyQueryUsing: fn ($query) => $query
                            ->where('bidding_processing_state', '!=', BiddingProcessingState::PENDING)
                            ->whereExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('tenders')
                                    ->whereColumn('tenders.bidding_id', 'biddings.id');
                            })
                    ),
                SelectFilter::make('open_procedure_check')
                    ->label('Procedure')
                    ->options([
                        'open' => 'Aperte',
                        'close' => 'Chiuse',
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'open') {
                            $query->where('open_procedure_check', true);
                        } elseif ($data['value'] === 'close') {
                            $query->where('open_procedure_check', false);
                        }
                    }),
                Filter::make('invitation_request_check')
                    ->form([
                        Checkbox::make('show_invitation_request')
                            ->label('Richieste invito'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_invitation_request'])) {
                            $query->where('invitation_request_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_invitation_request']) ? '' : 'Richiesta invito';
                    }),
                Filter::make('partnership_require_check')
                    ->form([
                        Checkbox::make('show_partnership_require')
                            ->label('ATI necessaria'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_partnership_require'])) {
                            $query->where('partnership_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_partnership_require']) ? '' : 'ATI necessaria';
                    }),
                Filter::make('collection_require_check')
                    ->form([
                        Checkbox::make('show_collection_require')
                            ->label('Incassi'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_collection_require'])) {
                            $query->where('collection_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_collection_require']) ? '' : 'Incassi';
                    }),
                Filter::make('reliance_require_check')
                    ->form([
                        Checkbox::make('show_reliance_require')
                            ->label('Avvalimento necessario'),
                    ])
                    ->columnSpan(3)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_reliance_require'])) {
                            $query->where('reliance_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_reliance_require']) ? '' : 'Avvalimento necessario';
                    }),
                Filter::make('reliance_admit_check')
                    ->form([
                        Checkbox::make('show_reliance_admit')
                            ->label('Avvalimento permesso'),
                    ])
                    ->columnSpan(3)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_reliance_admit'])) {
                            $query->where('reliance_admit_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_reliance_admit']) ? '' : 'Avvalimento permesso';
                    }),
                Filter::make('service_reference_require_check')
                    ->form([
                        Checkbox::make('show_service_reference_require')
                            ->label('Referenze servizi'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_service_reference_require'])) {
                            $query->where('service_reference_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_service_reference_require']) ? '' : 'Referenze servizi';
                    }),
                Filter::make('bank_reference_require_check')
                    ->form([
                        Checkbox::make('show_bank_reference_require')
                            ->label('Referenze bancarie'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_bank_reference_require'])) {
                            $query->where('bank_reference_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_bank_reference_require']) ? '' : 'Referenze bancarie';
                    }),
                Filter::make('pass_oe_require_check')
                    ->form([
                        Checkbox::make('show_pass_oe_require')
                            ->label('PASS OE'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_pass_oe_require'])) {
                            $query->where('pass_oe_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_pass_oe_require']) ? '' : 'PASS OE';
                    }),
                Filter::make('deposit_require_check')
                    ->form([
                        Checkbox::make('show_deposit_require')
                            ->label('Cauzione'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_deposit_require'])) {
                            $query->where('deposit_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_deposit_require']) ? '' : 'Cauzione';
                    }),
                Filter::make('authority_tax_require_check')
                    ->form([
                        Checkbox::make('show_authority_tax_require')
                            ->label('Contributo'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_authority_tax_require'])) {
                            $query->where('authority_tax_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_authority_tax_require']) ? '' : 'Contributo';
                    }),
                Filter::make('project_require_check')
                    ->form([
                        Checkbox::make('show_project_require')
                            ->label('Progetto'),
                    ])
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['show_project_require'])) {
                            $query->where('project_require_check', true);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_project_require']) ? '' : 'Progetto';
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
            'view' => Pages\ViewTender::route('/{record}'),
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
