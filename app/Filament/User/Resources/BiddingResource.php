<?php

namespace App\Filament\User\Resources;

use App\Enums\BiddingFilter;
use App\Enums\BiddingPriorityType;
use App\Enums\BiddingProcedureType;
use App\Enums\BiddingProcessingState;
use App\Enums\ClientType;
use App\Enums\FeasibilityType;
use App\Enums\InterestExpressionType;
use App\Enums\SendModeType;
use App\Enums\YesNo;
use App\Filament\User\Resources\BiddingResource\Pages;
use App\Filament\User\Resources\TenderResource\Tabs\ProcedureTypeTab;
use App\Filament\User\Resources\TenderResource\Tabs\RequiredDocumentsTab;
use App\Models\Bidding;
use App\Models\BiddingDataSource;
use App\Models\BiddingState;
use App\Models\Client;
use App\Models\ServiceType;
use App\Models\Tender;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BiddingResource extends Resource
{
    protected static ?string $model = Bidding::class;

    public static ?string $pluralModelLabel = 'Gare';

    public static ?string $modelLabel = 'Gara';

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationGroup = 'Gestione';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(24)
            ->schema([
                Tabs::make('Dettagli Operativi Appalto')
                    // ->relationship('tender') // <--- IL SEGRETO È QUI
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Dati Gara')
                        ->columns(24)
                        ->schema([
                            Fieldset::make('Informazioni generali')
                                ->columns(24)
                                ->schema([
                                CheckboxList::make('serviceTypes')
                                    ->label('Gara relativa al servizio di')
                                    ->required()
                                    ->relationship('serviceTypes', 'name')
                                    ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                                    ->columns(6)
                                    ->columnSpan(['sm' => 'full', 'md' => 24])
                                    ->gridDirection('row'),
                                Select::make('client_type')
                                    ->label('Tipo')
                                    ->required()
                                    ->live()
                                    ->options(ClientType::class)
                                    ->columnSpan(['sm' => 'full', 'md' => 5]),
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
                                    ->columnSpan(['sm' => 'full', 'md' => 9]),
                                Select::make('province_id')
                                    ->label('Provincia')
                                    ->required()
                                    ->relationship( name: 'province', titleAttribute: 'name', )
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(['sm' => 'full', 'md' => 5]),
                                Select::make('region_id')
                                    ->label('Regione')
                                    ->required()
                                    ->relationship( name: 'region', titleAttribute: 'name', )
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(['sm' => 'full', 'md' => 5]),
                                Textarea::make('description')
                                    ->label('Descrizione')
                                    ->columnSpan(['sm' => 'full', 'md' => 24]),
                                TextInput::make('amount')
                                    ->label('Importo')
                                    ->prefix('€')
                                    ->columnSpan(['sm' => 'full', 'md' => 4])
                                    ->live(onBlur: true)
                                    ->inputMode('decimal')
                                    ->extraInputAttributes(['class' => 'text-right'])
                                    // 1. Quando il record viene caricato → formatta con . e ,
                                    ->formatStateUsing(fn ($state) =>
                                        $state !== null && $state !== ''
                                            ? number_format((float) $state, 2, ',', '.')
                                            : ''
                                    )
                                    // 2. Ogni volta che l'utente digita → riformatta ISTANTANEAMENTE
                                    ->afterStateUpdated(function ($state, $component) {
                                        if (blank($state)) {
                                            $component->state('');
                                            return;
                                        }

                                        // Pulizia: accetta solo numeri, punto, virgola e -
                                        $clean = preg_replace('/[^\d,\.-]/', '', $state);

                                        // Convertiamo in float per gestire correttamente la virgola come decimale
                                        $number = str_replace(',', '.', $clean);
                                        $float = floatval($number);

                                        // Riformatta come vuoi tu: 1.234.567,89
                                        $formatted = number_format($float, 2, ',', '.');

                                        // Ri-aggiorna il campo con il valore formattato
                                        $component->state($formatted);
                                    })
                                    // 3. Al salvataggio → converte "1.234.567,89" → 1234567.89 (float)
                                    ->dehydrateStateUsing(fn ($state): ?float =>
                                        blank($state)
                                            ? null
                                            : (float) str_replace(['.', ','], ['', '.'], $state)
                                ),
                                TextInput::make('residents')
                                    ->label('Abitanti')
                                    ->columnSpan(['sm' => 'full', 'md' => 3])
                                    ->inputMode('numeric')
                                    ->extraInputAttributes(['class' => 'text-right'])
                                    ->formatStateUsing(fn ($state) => $state ? number_format((int)$state, 0, ',', '.') : '')
                                    ->dehydrateStateUsing(fn ($state) => $state ? (int)str_replace(['.', ','], '', $state) : null),
                                Select::make('feasibility_type')
                                    ->label('Fattibilità')
                                    ->required()
                                    ->live()
                                    ->options(FeasibilityType::class)
                                    ->default('evaluate')
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                                Select::make('bidding_state_id')
                                    ->label('Dettaglio fattibilità')
                                    ->relationship(
                                        name: 'biddingState',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query, Get $get) => $query->where('feasibility_type', $get('feasibility_type'))->orderBy('position')
                                    )
                                    ->columnSpan(['sm' => 'full', 'md' => 5]),
                                Select::make('bidding_processing_state')
                                    ->label('Stato lavorazione')
                                    ->live()
                                    ->options(BiddingProcessingState::class)
                                    ->suffixIcon(fn ($state) => $state ? BiddingProcessingState::from($state)->getIcon() : null)
                                    ->columnSpan(['sm' => 'full', 'md' => 6]),
                                Select::make('bidding_priority_type')
                                    ->label('Priorità')
                                    ->live()
                                    ->options(BiddingPriorityType::class)
                                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                                Textarea::make('bidding_note')
                                    ->label('Note gara')
                                    ->columnSpan(['sm' => 'full', 'md' => 24]),
                            ]),
                            Fieldset::make('Manifestazione d\'interesse')
                                ->columns(25)
                                ->schema([
                                    Select::make('interest_expression_type')
                                        ->label('Manifestazione d\'interesse')
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if($state)
                                                $set('interest_deadline_time', '06:00');
                                            else
                                                $set('interest_deadline_time', null);
                                        })
                                        ->options(InterestExpressionType::class)
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    DatePicker::make('interest_deadline_date')
                                        ->label('Data scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->disabled(fn (callable $get) => !$get('interest_expression_type'))
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    TimePicker::make('interest_deadline_time')
                                        ->label('Orario scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default(fn (callable $get) => $get('interest_expression_type') ? '06:00' : null)
                                        ->disabled(fn (callable $get) => !$get('interest_expression_type'))
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    DatePicker::make('interest_send_date')
                                        ->label('Data invio')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->disabled(fn (callable $get) => !$get('interest_expression_type'))
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    TimePicker::make('interest_send_time')
                                        ->label('Orario invio')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default(fn (callable $get) => $get('interest_expression_type') ? '06:00' : null)
                                        ->disabled(fn (callable $get) => !$get('interest_expression_type'))
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Select::make('interest_send_mode_type')
                                        ->label('Modalità d\'invio')
                                        ->options(SendModeType::class)
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                ]),
                            Fieldset::make('Sopralluogo')
                                ->columns(25)
                                ->schema([
                                    Checkbox::make('mandatory_inspection')
                                        ->label('Sopralluogo obbligatorio')
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if($state)
                                                $set('inspection_deadline_time', '06:00');
                                            else
                                                $set('inspection_deadline_time', null);
                                        })
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    DatePicker::make('inspection_deadline_date')
                                        ->label('Data scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    TimePicker::make('inspection_deadline_time')
                                        ->label('Orario scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default(fn (callable $get) => $get('mandatory_inspection') ? '06:00' : null)
                                        ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    DatePicker::make('inspection_date')
                                        ->label('Data sopralluogo')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    // TimePicker::make('inspection_time')
                                    //     ->label('Orario sopralluogo')
                                    //     ->extraInputAttributes(['class' => 'text-center'])
                                    //     ->default(fn (callable $get) => $get('mandatory_inspection') ? '06:00' : null)
                                    //     ->disabled(fn (callable $get) => !$get('mandatory_inspection'))
                                    //     ->columnSpan(['sm' => 'full', 'md' => 5]),
                                ]),
                            Fieldset::make('Gara')
                                ->columns(25)
                                ->schema([
                                    Select::make('bidding_type_id')
                                        ->label('Tipo gara')
                                        ->relationship(
                                            name: 'biddingType',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn ($query) => $query->orderBy('position')
                                        )
                                        ->columnSpan(['sm' => 'full', 'md' => 7]),
                                    Select::make('bidding_adjudication_type_id')
                                        ->label('Tipo aggiudicazione')
                                        ->relationship(
                                            name: 'biddingAdjudicationType',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn ($query) => $query->orderBy('position')
                                        )
                                        ->columnSpan(['sm' => 'full', 'md' => 8]),
                                    DatePicker::make('clarification_request_deadline_date')
                                        ->label('Data scadenza chiarimenti')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    TimePicker::make('clarification_request_deadline_time')
                                        ->label('Orario scadenza chiarimenti')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default('06:00')
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),

                                    DatePicker::make('deadline_date')
                                        ->label('Data scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->required(fn (Get $get) => $get('bidding_type_id'))
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    TimePicker::make('deadline_time')
                                        ->label('Orario scadenza')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->required(fn (Get $get) => $get('bidding_type_id'))
                                        ->default('06:00')
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    DatePicker::make('send_date')
                                        ->label('Data invio offerta')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    TimePicker::make('send_time')
                                        ->label('Orario invio offerta')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default('06:00')
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    // TextInput::make('send_mode')
                                    //     ->label('Modalità invio')
                                    //     ->columnSpan(['sm' => 'full', 'md' => 6]),
                                    Select::make('send_mode_type')
                                        ->label('Modalità d\'invio')
                                        ->options(SendModeType::class)
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),

                                    DatePicker::make('opening_date')
                                        ->label('Data apertura offerte')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    TimePicker::make('opening_time')
                                        ->label('Orario apertura offerte')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->default('06:00')
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    DatePicker::make('closure_date')
                                        ->label('Data chiusura procedura')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Select::make('awarded')
                                        ->label('Aggiudicata')
                                        ->options(YesNo::class)
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    TextInput::make('contact')
                                        ->label('Nome contatto')
                                        ->columnSpan(['sm' => 'full', 'md' => 9]),

                                    TextInput::make('note')
                                        ->label('Note')
                                        ->columnSpan(['sm' => 'full', 'md' => 'full']),

                                    TextInput::make('contracting_station')
                                        ->label('Gestore appalto')
                                        ->columnSpan(['sm' => 'full', 'md' => 18]),
                                    Select::make('bidding_procedure_type')
                                        ->label('Procedura')
                                        ->live()
                                        ->options(BiddingProcedureType::class)
                                        ->default(BiddingProcedureType::TELEMATIC)
                                        ->columnSpan(['sm' => 'full', 'md' => 7]),

                                    TextInput::make('procedure_portal')
                                        ->label('Portale procedura')
                                        ->columnSpan(['sm' => 'full', 'md' => 10]),
                                    TextInput::make('cig')
                                        ->label('CIG')
                                        ->extraInputAttributes(['class' => 'text-right'])
                                        ->columnSpan(['sm' => 'full', 'md' => 5]),
                                    TextInput::make('procedure_id')
                                        ->label('ID Procedura')
                                        ->columnSpan(['sm' => 'full', 'md' => 10]),

                                    TextInput::make('year')
                                        ->label('Durata anni')
                                        ->extraInputAttributes(['class' => 'text-right'])
                                        ->columnSpan(['sm' => 'full', 'md' => 6]),
                                    TextInput::make('month')
                                        ->label('Durata mesi')
                                        ->extraInputAttributes(['class' => 'text-right'])
                                        ->columnSpan(['sm' => 'full', 'md' => 6]),
                                    TextInput::make('day')
                                        ->label('Durata giorni')
                                        ->extraInputAttributes(['class' => 'text-right'])
                                        ->columnSpan(['sm' => 'full', 'md' => 6]),
                                    DatePicker::make('renew')
                                        ->label('Rinnovo')
                                        ->extraInputAttributes(['class' => 'text-center'])
                                        ->columnSpan(['sm' => 'full', 'md' => 7]),

                                    Select::make('source1_id')
                                        ->label('Fonte dati 1')
                                        ->relationship('source1', 'name')
                                        ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Select::make('source2_id')
                                        ->label('Fonte dati 2')
                                        ->relationship('source2', 'name')
                                        ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Select::make('source3_id')
                                        ->label('Fonte dati 3')
                                        ->relationship('source3', 'name')
                                        ->options(BiddingDataSource::orderBy('position')->pluck('name', 'id')->toArray())
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),

                                    // Placeholder::make('')
                                    //     ->visible(fn (callable $get) => $get('modified_user_id') === null)
                                    //     ->columnSpan(['sm' => 0, 'md' =>16]),
                                    Select::make('assigned_user_id')
                                        ->label('Assegnato a')
                                        ->relationship('assignedUser', 'name')
                                        ->options(User::pluck('name', 'id')->toArray())
                                        ->columns(6)
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Select::make('modified_user_id')
                                        ->label('Modificato da')
                                        ->relationship('modifiedUser', 'name')
                                        ->options(User::pluck('name', 'id')->toArray())
                                        // ->columns(6)
                                        ->disabled()
                                        ->dehydrated()
                                        ->visible(fn ($state) => $state !== null)
                                        ->columnSpan(['sm' => 'full', 'md' => 4]),
                                    Placeholder::make('')
                                        ->label('Ultima modifica: ')
                                        ->content(fn ($record) => $record?->updated_at?->format('d/m/Y') ?? '')
                                        ->visible(fn (callable $get) => $get('modified_user_id') !== null)
                                        ->columnSpan(['sm' => 0, 'md' => 5]),

                                    // FileUpload::make('temp_zip')
                                    //     ->label('Carica ZIP con allegati')
                                    //     ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                                    //     ->maxSize(102400)
                                    //     // ->disk('public')
                                    //     ->directory('biddings-temp')
                                    //     // ->visibility('public')
                                    //     ->multiple(false)
                                    //     ->preserveFilenames()
                                    //     ->columnSpanFull()
                                    //     ->visible(fn ($record) => blank($record?->attachment_path)), // mostra solo se non già caricato

                                    // 1. SOLO IN CREAZIONE o se non c'è attachment_path
                                    FileUpload::make('temp_zip')
                                        ->label('Carica ZIP con allegati')
                                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                                        // ->maxSize(102400)
                                        ->directory('biddings-temp')
                                        ->dehydrated(false)
                                        ->visible(fn ($livewire, $record) => $livewire instanceof \Filament\Resources\Pages\CreateRecord ||
                                                                                !$record->attachment_path)
                                        ->columnSpanFull(),


                                    Section::make('Allegati')
                                        ->collapsed()
                                        ->visible(fn($record) => $record && $record->attachment_path)
                                        ->visible(fn ($record) =>
                                            $record &&
                                            $record->attachment_path &&
                                            !collect(Storage::disk(config('filesystems.default', 'public'))->allFiles($record->attachment_path))->isEmpty()
                                        )
                                        ->headerActions([
                                            Action::make('delete')
                                                ->label('Svuota allegati')
                                                ->icon('heroicon-o-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->action(function ($record) {
                                                    $directory = $record->attachment_path;
                                                    $disk = config('filesystems.default', 'public');

                                                    if (!$directory || !Storage::disk($disk)->exists($directory)) {
                                                        return;
                                                    }

                                                    // 1. Recupera tutti i file all'interno della cartella
                                                    $files = Storage::disk($disk)->allFiles($directory);

                                                    // 2. Elimina i file
                                                    Storage::disk($disk)->delete($files);

                                                    // 3. Se ci sono sottocartelle, allFiles non le elimina.
                                                    // Per eliminare anche le sottocartelle ma mantenere la "root":
                                                    $directories = Storage::disk($disk)->allDirectories($directory);
                                                    foreach ($directories as $subDir) {
                                                        Storage::disk($disk)->deleteDirectory($subDir);
                                                    }

                                                    Notification::make()
                                                        ->title('Cartella svuotata con successo')
                                                        ->success()
                                                        ->send();

                                                    return redirect(request()->header('Referer'));
                                                }),
                                        ])
                                        ->schema([
                                            Placeholder::make('attachments')
                                                ->key('attachments_list')
                                                ->label('')
                                                ->hintAction(
                                                    Action::make('downloadAll')
                                                        ->label('Scarica tutti (ZIP)')
                                                        ->icon('heroicon-o-arrow-down-tray')
                                                        ->action(function ($record) {
                                                            $services = '';
                                                            for($i = 0; $i < count($record->serviceTypes); $i++) {
                                                                if($i != 0) $services .= ' ';
                                                                $services .= $record->serviceTypes[$i]->name;
                                                            }
                                                            return response()->streamDownload(function () use ($record) {
                                                                $zip = new ZipArchive();
                                                                $path = tempnam(sys_get_temp_dir(), 'zip');

                                                                $zip->open($path, ZipArchive::CREATE);

                                                                $disk = config('filesystems.default', 'public');
                                                                $files = Storage::disk($disk)->allFiles($record->attachment_path);

                                                                foreach ($files as $file) {
                                                                    // Legge il file dal disco e lo aggiunge allo ZIP
                                                                    $fileContent = Storage::disk($disk)->get($file);
                                                                    $zip->addFromString(basename($file), $fileContent);
                                                                }

                                                                $zip->close();
                                                                readfile($path);
                                                                unlink($path);
                                                            }, "Allegati gara {$record->client->name} - {$record->cig} ({$services}).zip");
                                                        })
                                                )
                                                ->content(function ($record) {
                                                    if (!$record || !$record->attachment_path) {
                                                        return 'Nessun allegato.';
                                                    }

                                                    // dd([
                                                    //     'path' => $record->attachment_path,
                                                    //     'default_disk' => config('filesystems.default'),
                                                    //     'files_on_public' => Storage::disk('public')->files($record->attachment_path),
                                                    //     'files_on_default' => Storage::files($record->attachment_path),
                                                    //     'exists_public' => Storage::disk('public')->exists($record->attachment_path),
                                                    // ]);

                                                    $disk = config('filesystems.default', 'public');

                                                    // Usa allFiles per prendere anche file in sottocartelle
                                                    $files = Storage::disk($disk)->allFiles($record->attachment_path);

                                                    if (empty($files)) {
                                                        return 'Nessuna cartella allegati trovata.';
                                                    }

                                                    return new \Illuminate\Support\HtmlString(
                                                        collect($files)
                                                            ->sort()
                                                            ->map(function ($file) use ($disk) {
                                                                $name = basename($file);

                                                                // Genera URL in base al tipo di disco
                                                                // try {
                                                                //     if ($disk === 's3' || config("filesystems.disks.{$disk}.driver") === 's3') {
                                                                //         // Per S3 usa temporaryUrl
                                                                //         $url = Storage::disk($disk)->temporaryUrl($file, now()->addMinutes(5));
                                                                //     } else {
                                                                //         // Per locale usa url normale
                                                                //         $url = Storage::disk($disk)->url($file);
                                                                //     }
                                                                // } catch (\Exception $e) {
                                                                //     // Fallback se temporaryUrl non è supportato
                                                                //     $url = Storage::disk($disk)->url($file);
                                                                // }

                                                                $url = Storage::temporaryUrl($file, now()->addMinutes(5));

                                                                return <<<HTML
                                                                <div class="flex items-center gap-3 py-1">
                                                                    <a href="{$url}" target="_blank" download class="text-primary-600 hover:underline font-medium">
                                                                        {$name}
                                                                    </a>
                                                                </div>
                                                                HTML;
                                                            })
                                                            ->implode('')
                                                    );
                                                })
                                                ->extraAttributes(['style' => 'line-height:1.8'])
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpan(['sm' => 'full', 'md' => 24]),

                                    FileUpload::make('restore_zip')
                                        ->label('Carica ZIP con allegati')
                                        ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                                        ->directory('biddings-temp')
                                        ->dehydrated(false)
                                        ->visible(fn ($record) =>
                                            $record &&
                                            $record->attachment_path &&
                                            collect(Storage::disk(config('filesystems.default', 'public'))->allFiles($record->attachment_path))->isEmpty()
                                        )
                                        ->columnSpanFull(),
                                ]),
                        ]),

                        // Tab::make('Dati Appalto')
                        //     // ->hidden(fn ($get) => static::hideTenderTabs($get))
                        //     ->hidden(fn ($record) => static::hideTenderTabs($record))
                        //     ->schema([
                        //         Group::make()
                        //             ->relationship('tender')
                        //             ->columns(12)
                        //             ->schema(GeneralDataTab::make()),
                        //     ]),

                        Tab::make('Dati Procedura')
                            // ->hidden(fn ($get) => static::hideTenderTabs($get))
                            ->hidden(fn ($record) => static::hideTenderTabs($record))
                            ->schema([
                                Group::make()
                                    ->relationship('tender')
                                    ->columns(12)
                                    ->schema(ProcedureTypeTab::make()),
                            ]),

                        Tab::make('Documenti')
                            // ->hidden(fn ($get) => static::hideTenderTabs($get))
                            ->hidden(fn ($record) => static::hideTenderTabs($record))
                            ->schema([
                                Group::make()
                                    ->relationship('tender')
                                    ->columns(24)
                                    ->schema(RequiredDocumentsTab::make()),
                            ]),
                    ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'asc')
            ->persistFiltersInSession()                                 // Persistenza filtri
            ->persistSortInSession()                                    // Persistenza ordinamento
            // ->persistSearchInSession()                                  // Persistenza barra di ricerca globale
            ->persistColumnSearchesInSession()                          // Persistenza ricerche nelle singole colonne
            ->columns([
                TextColumn::make('deadline_status')
                    ->label('')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        // Individuo la data di riferimento secondo la gerarchia indicata
                        $referenceDate = static::getReferenceDate($record);

                        if (!$referenceDate) return 'N/D';

                        $daysUntil = now()->startOfDay()->diffInDays(
                            Carbon::parse($referenceDate)->startOfDay(),
                            false
                        );

                        return match (true) {
                            $daysUntil < 0 => $record->interest_send_date ? 'In attesa gara' : 'Scaduta',
                            $daysUntil === 0 => 'Oggi',
                            default => $daysUntil . ' giorni',
                        };
                    })
                    ->color(function ($record) {
                        // Ripeto la stessa logica per la data di riferimento
                        $referenceDate = static::getReferenceDate($record);

                        if (!$referenceDate) return 'gray';

                        $daysUntil = now()->startOfDay()->diffInDays(
                            Carbon::parse($referenceDate)->startOfDay(),
                            false
                        );

                        return match (true) {
                            $daysUntil <= 0 => $record->interest_send_date ? 'gray' : 'danger',
                            $daysUntil <= 3 => 'warning',
                            $daysUntil <= 7 => 'info',
                            $daysUntil <= 15 => 'success',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('serviceTypes')
                    ->label('Servizi')
                    ->limit(20)
                    ->tooltip(function ($record) {
                        return $record->serviceTypes->pluck('name')->join(' - ');
                    })
                    ->formatStateUsing(function ($record) {
                        return $record->serviceTypes->pluck('name')->join(' - ');
                    }),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('client.name')
                    ->label('Ente')
                    ->searchable(),
                TextColumn::make('province.code')
                    ->label('Prov.'),
                TextColumn::make('region.name')
                    ->label('Regione'),
                TextColumn::make('date')
                    ->label('Scadenza')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("COALESCE(deadline_date, interest_deadline_date) {$direction}");
                    })
                    ->state(function ($record) {
                        return $record->deadline_date ?? $record->interest_deadline_date;
                    })
                    ->date('d/m/Y'),
                TextColumn::make('inspection_deadline_date')
                    ->label('Sopralluogo')
                    ->sortable()
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('clarification_request_deadline_date')
                    ->label('Chiarimenti')
                    ->sortable()
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('biddingType.name')
                    ->label('Tipo')
                    ->state(function ($record) {
                        return $record->biddingType?->name ?? $record->interest_expression_type?->getLabel();
                    }),
                TextColumn::make('feasibility_type')
                    ->label('Fattibilità')
                    ->badge()
                    ->tooltip(fn($record) => BiddingState::find($record?->bidding_state_id)?->name) ?? '',
                TextColumn::make('biddingState.name')
                    ->label('Stato gara')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filtersFormWidth('5xl')
            ->filtersFormColumns(4)
            ->persistFiltersInSession()
            ->filters([
                // Filtri rapidi selezione multipla
                SelectFilter::make('bidding_filter')
                    ->label('Filtri rapidi')
                    ->options(BiddingFilter::class)
                    ->multiple()
                    ->preload()
                    ->columnSpan(2)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->where(function ($q) use ($data) {
                                foreach ($data['values'] as $value) {
                                    switch ($value) {
                                        case BiddingFilter::TENDER30->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('deadline_date')
                                                        ->whereDate('deadline_date', '>=', Carbon::today())
                                                        ->whereDate('deadline_date', '<=', Carbon::today()->addDays(30));
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('deadline_date')
                                                        ->whereNotNull('interest_deadline_date')
                                                        ->whereDate('interest_deadline_date', '>=', Carbon::today())
                                                        ->whereDate('interest_deadline_date', '<=', Carbon::today()->addDays(30));
                                                });
                                            });
                                            break;
                                        case BiddingFilter::INSPECTION30->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->whereNotNull('inspection_deadline_date')
                                                    ->whereDate('inspection_deadline_date', '>=', Carbon::today())
                                                    ->whereDate('inspection_deadline_date', '<=', Carbon::today()->addDays(30));
                                            });
                                            break;
                                        case BiddingFilter::TENDER15->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('deadline_date')
                                                        ->whereDate('deadline_date', '>=', Carbon::today())
                                                        ->whereDate('deadline_date', '<=', Carbon::today()->addDays(15));
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('deadline_date')
                                                        ->whereNotNull('interest_deadline_date')
                                                        ->whereDate('interest_deadline_date', '>=', Carbon::today())
                                                        ->whereDate('interest_deadline_date', '<=', Carbon::today()->addDays(15));
                                                });
                                            });
                                            break;
                                        case BiddingFilter::INSPECTION15->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->whereNotNull('inspection_deadline_date')
                                                    ->whereDate('inspection_deadline_date', '>=', Carbon::today())
                                                    ->whereDate('inspection_deadline_date', '<=', Carbon::today()->addDays(15));
                                            });
                                            break;
                                        case BiddingFilter::SEND30->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('send_date')
                                                        ->whereDate('send_date', '>=', Carbon::today()->subDays(30))
                                                        ->whereDate('send_date', '<', Carbon::today());
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('send_date')
                                                        ->whereNotNull('interest_send_date')
                                                        ->whereDate('interest_send_date', '>=', Carbon::today()->subDays(30))
                                                        ->whereDate('interest_send_date', '<', Carbon::today());
                                                });
                                            });
                                            break;
                                        case BiddingFilter::SEND60->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('send_date')
                                                        ->whereDate('send_date', '>=', Carbon::today()->subDays(60))
                                                        ->whereDate('send_date', '<', Carbon::today());
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('send_date')
                                                        ->whereNotNull('interest_send_date')
                                                        ->whereDate('interest_send_date', '>=', Carbon::today()->subDays(60))
                                                        ->whereDate('interest_send_date', '<', Carbon::today());
                                                });
                                            });
                                            break;
                                        case BiddingFilter::SEND90->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('send_date')
                                                        ->whereDate('send_date', '>=', Carbon::today()->subDays(90))
                                                        ->whereDate('send_date', '<', Carbon::today());
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('send_date')
                                                        ->whereNotNull('interest_send_date')
                                                        ->whereDate('interest_send_date', '>=', Carbon::today()->subDays(90))
                                                        ->whereDate('interest_send_date', '<', Carbon::today());
                                                });
                                            });
                                            break;
                                        case BiddingFilter::SEND180->value:
                                            $q->orWhere(function ($subQ) {
                                                $subQ->where(function ($deepQ) {
                                                    $deepQ->whereNotNull('send_date')
                                                        ->whereDate('send_date', '>=', Carbon::today()->subDays(180))
                                                        ->whereDate('send_date', '<', Carbon::today());
                                                })->orWhere(function ($deepQ) {
                                                    $deepQ->whereNull('send_date')
                                                        ->whereNotNull('interest_send_date')
                                                        ->whereDate('interest_send_date', '>=', Carbon::today()->subDays(180))
                                                        ->whereDate('interest_send_date', '<', Carbon::today());
                                                });
                                            });
                                            break;
                                    }
                                }
                            });
                        }
                    }),
                SelectFilter::make('feasibility_type')
                        ->label('Fattibilità')
                        ->multiple()
                        ->options(array_merge(
                            ['none' => 'Nessuna'],
                            collect(FeasibilityType::cases())
                                ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
                                ->toArray()
                        ))
                        ->query(function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return $query;
                            }

                            return $query->where(function ($q) use ($data) {
                                foreach ($data['values'] as $value) {
                                    if ($value === 'none') {
                                        $q->orWhereNull('feasibility_type');
                                    } else {
                                        $q->orWhere('feasibility_type', $value);
                                    }
                                }
                            });
                        }),
                Filter::make('past_deadline')
                    ->form([
                        Checkbox::make('show_past_deadline')
                            ->label('Mostra scadute'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['show_past_deadline'])) {
                            $query->upcoming(); // Usa lo scope definito nel modello
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return empty($data['show_past_deadline']) ? '' : 'Incluse scadute';
                    }),
                Filter::make('inspection_date_range')
                    ->columns(2)
                    ->columnSpan(2)
                    ->form([
                        DatePicker::make('inspection_from_date')
                            ->label('Sopralluogo da')
                            ->columnSpan(1),
                        DatePicker::make('inspection_to_date')
                            ->label('Sopralluogo a')
                            ->columnSpan(1),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['inspection_from_date'])) {
                            $query->whereDate('inspection_deadline_date', '>=', $data['inspection_from_date']);
                        }
                        if (! empty($data['inspection_to_date'])) {
                            $query->whereDate('inspection_deadline_date', '<=', $data['inspection_to_date']);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['inspection_from_date'] && $data['inspection_to_date']) {
                            return "Sopralluoghi dal {$data['inspection_from_date']} al {$data['inspection_to_date']}";
                        }
                        if ($data['inspection_from_date']) {
                            return "Sopralluoghi dal {$data['inspection_from_date']}";
                        }
                        if ($data['inspection_to_date']) {
                            return "Sopralluoghi al {$data['inspection_to_date']}";
                        }
                        return null;
                    }),
                Filter::make('deadline_date_range')
                    ->columns(2)
                    ->columnSpan(2)
                    ->form([
                        DatePicker::make('deadline_from_date')
                            ->label('Scadenza da')
                            ->columnSpan(1),
                        DatePicker::make('deadline_to_date')
                            ->label('Scadenza a')
                            ->columnSpan(1),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['deadline_from_date'])) {
                            $query->whereDate('deadline_date', '>=', $data['deadline_from_date']);
                        }
                        if (! empty($data['deadline_to_date'])) {
                            $query->whereDate('deadline_date', '<=', $data['deadline_to_date']);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['deadline_from_date'] && $data['deadline_to_date']) {
                            return "Scadenze dal {$data['deadline_from_date']} al {$data['deadline_to_date']}";
                        }
                        if ($data['deadline_from_date']) {
                            return "Scadenze da {$data['deadline_from_date']}";
                        }
                        if ($data['deadline_to_date']) {
                            return "Scadenze al {$data['deadline_to_date']}";
                        }
                        return null;
                    }),
                SelectFilter::make('services')
                    ->label('Gara relativa al servizio di')
                    ->relationship('serviceTypes', 'name')
                    ->multiple()
                    ->preload()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            foreach ($data['values'] as $value) {
                                $query->whereHas('serviceTypes', function (Builder $subQuery) use ($value) {
                                    $subQuery->where('service_types.id', $value); // Specifica la tabella
                                });
                            }
                        }
                    }),
                SelectFilter::make('bidding_type_id')->label('Tipo gara')
                    ->relationship(
                        name: 'biddingType',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('position')
                    )
                    ->multiple()->preload(),
                SelectFilter::make('bidding_state_id')->label('Dettaglio fattibilità')
                    ->relationship(
                        name: 'biddingState',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('position')
                    )
                    ->multiple()->preload(),
                SelectFilter::make('bidding_processing_state')->label('Stato lavorazione')
                    ->options(function () {
                        // Creiamo l'array con il valore personalizzato
                        $options = ['da_assegnare' => 'Da assegnare'];

                        // Aggiungiamo i casi dell'Enum
                        foreach (BiddingProcessingState::cases() as $state) {
                            // Se l'enum è di tipo 'Backed' (es: string), usiamo ->value e ->name (o un'etichetta)
                            $options[$state->value] = $state->getLabel();
                        }

                        return $options;
                    })->query(function (Builder $query, array $data): Builder {
                        $values = $data['values'];

                        if (empty($values)) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($values) {
                            // Controlliamo se 'da_assegnare' è tra i valori selezionati
                            if (in_array('da_assegnare', $values)) {
                                // Rimuoviamo 'da_assegnare' per gestire i valori reali dell'Enum
                                $realEnumValues = array_diff($values, ['da_assegnare']);

                                $query->whereNull('bidding_processing_state');

                                // Se c'erano anche altri stati selezionati, usiamo orWhereIn
                                if (!empty($realEnumValues)) {
                                    $query->orWhereIn('bidding_processing_state', $realEnumValues);
                                }
                            } else {
                                // Se 'da_assegnare' non è selezionato, procedi normalmente
                                $query->whereIn('bidding_processing_state', $values);
                            }
                        });
                    })
                    ->multiple(),
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
            'index' => Pages\ListBiddings::route('/'),
            'create' => Pages\CreateBidding::route('/create'),
            'edit' => Pages\EditBidding::route('/{record}/edit'),
            'view' => Pages\ViewBidding::route('/{record}'),
        ];
    }

    private static function getReferenceDateOld($record)
    {
        return match (true) {
            // Se c'è solo la data di scadenza della gara usa quella
            !$record->inspection_deadline_date && $record->deadline_date => $record->deadline_date,
            // Se le scadenze principali non ci sono, uso l'interesse
            !$record->inspection_deadline_date && !$record->deadline_date => $record->interest_deadline_date,
            // Se c'è la scdenza del sopralluogo ed è nel futuro (o oggi), uso quello
            $record->inspection_deadline_date && Carbon::parse($record->inspection_deadline_date)->isFuture() || Carbon::parse($record->inspection_deadline_date)->isToday() => $record->inspection_deadline_date,
            // Se c'è la scadenza del sopralluogo ed è nel passato e non ho la data del sopralluogo
            $record->inspection_deadline_date && Carbon::parse($record->inspection_deadline_date)->isPast() && !$record->inspection_date => $record->inspection_deadline_date,
            // Altrimenti ripiego sulla deadline finale
            default => $record->deadline_date,
        };
    }

    private static function getReferenceDate($record)
    {
        $inspectionDeadline = $record->inspection_deadline_date
            ? Carbon::parse($record->inspection_deadline_date)
            : null;

        return match (true) {
            // Se manca la scadenza del sopralluogo, uso la scadenza gara (se esiste), altrimenti quella dell amanifestazione di interesse
            !$inspectionDeadline => $record->deadline_date ?? $record->interest_deadline_date,
            // Se c'è la scadenza del sopralluogo ed è oggi o nel futuro, la uso
            $inspectionDeadline->isAfter(now()->subDay()) => $record->inspection_deadline_date,
            // Se c'è la scadenza del sopralluogo ed è passato ma non c'è la data in cui è effettuato, la uso
            $inspectionDeadline->isPast() && !$record->inspection_date => $record->inspection_deadline_date,
            // In tutti gli altri casi
            default => $record->deadline_date,
        };
    }

    public static function saveClient(array $data, Client $client): void
    {
        $client->client_type = $data['client_type'];
        $client->name = $data['name'];
        $client->state_id = $data['state_id'];
        if (isset($data['region_id'])) {
            $client->region_id = $data['region_id'];
        }
        if (isset($data['province_id'])) {
            $client->province_id = $data['province_id'];
        }
        if (isset($data['city_id'])) {
            $client->city_id = $data['city_id'];
        }
        if (isset($data['zip_code'])) {
            $client->zip_code = $data['zip_code'];
        }
        if (isset($data['place'])) {
            $client->place = $data['place'];
        }
        $client->address = $data['address'];
        $client->civic = $data['civic'];
        $client->phone = $data['phone'];
        $client->email = $data['email'];
        $client->site = $data['site'];
        $client->note = $data['note'];
        $client->save();

        Notification::make()
            ->title('Cliente salvato con successo')
            ->success()
            ->send();
    }

    protected static function hideTenderTabs($bidding): bool
    {
        // return $get('bidding_processing_state') === null
        //         || $get('bidding_processing_state') === BiddingProcessingState::PENDING->value;
        // dd($bidding->tender);
        return !Tender::where('bidding_id', $bidding?->id)->exists();
    }
}
