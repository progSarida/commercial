<?php
namespace App\Filament\User\Resources;
use App\Enums\ContactType;
use App\Enums\OutcomeType;
use App\Filament\User\Resources\DeadlineResource\Pages;
use App\Filament\User\Resources\DeadlineResource\RelationManagers;
use App\Models\Contact;
use App\Models\Province;
use App\Models\Region;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
class DeadlineResource extends Resource
{
    protected static ?string $model = Contact::class;
    public static ?string $pluralModelLabel = 'Scadenze';
    public static ?string $modelLabel = 'Scadenza';
    protected static ?string $navigationIcon = 'fas-calendar-day';
    protected static ?string $navigationGroup = 'Contatti';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->columns(24)
            ->schema([
                Select::make('contact_type')
                    ->label('Tipo contatto')
                    ->options(ContactType::class)
                    ->default(ContactType::DEADLINE)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 4]),

                // Select::make('client_id')
                //     ->label('Cliente')
                //     ->required()
                //     ->searchable()
                //     ->relationship( name: 'client', titleAttribute: 'name')
                //     ->columnSpan(['sm' => 'full', 'md' => 20]),

                Select::make('client_id')
                    ->label('Cliente')
                    ->required()
                    ->live()
                    ->searchable()
                    // ->relationship( name: 'client', titleAttribute: 'name')
                    ->relationship(
                        name: 'client',
                        titleAttribute: 'name',
                        // Carichiamo anche il campo phone nella query per averlo disponibile
                        modifyQueryUsing: fn(Builder $query) => $query->select(['id', 'name', 'phone'])
                    )
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $phone = $record->phone ?? 'Nessun numero';
                        return "{$record->name} - {$phone}";
                    })
                    ->hintAction(
                        Forms\Components\Actions\Action::make('open_client')
                            ->label('Modifica')
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->color('gray')
                            ->visible(fn($get) => filled($get('client_id')))
                            ->url(function ($get) {
                                $clientId = $get('client_id');
                                if (!$clientId)
                                    return null;

                                // Genera l'URL per l'edit del cliente (cambia ClientResource con il nome reale della tua risorsa)
                                return ClientResource::getUrl('view', ['record' => $clientId]);
                            })
                            ->openUrlInNewTab() // Fondamentale per non perdere il lavoro sulla chiamata
                    )
                    ->columnSpan(['sm' => 'full', 'md' => 16]),

                // Select::make('outcome_type')
                //     ->label('Esito')
                //     // ->options(OutcomeType::class)
                //     ->options(OutcomeType::getOptionsByContactType(ContactType::DEADLINE))
                //     ->columnSpan(['sm' => 'full', 'md' => 5]),

                DatePicker::make('date')
                    ->label('Data scadenza')
                    ->extraInputAttributes(['class' => 'text-center'])
                    ->required()
                    ->columnSpan(['sm' => 'full', 'md' => 4]),

                // TimePicker::make('time')
                //     ->label('Orario')
                //     ->required()
                //     ->seconds(false)
                //     ->displayFormat('H:i')
                //     ->columnSpan(['sm' => 'full', 'md' => 3]),

                Select::make('services')
                    ->label('Servizi')
                    ->options(ServiceType::pluck('name', 'id'))
                    ->multiple()
                    ->live()
                    ->required(
                        fn(Get $get) =>
                        $get('outcome_type') !== null &&
                        $get('outcome_type') !== OutcomeType::NEGATIVE->value
                    )
                    ->searchable()
                    ->columnSpan(['sm' => 'full', 'md' => 'full']),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpan(['sm' => 'full', 'md' => 20]),
                Select::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->default(Auth::user()->id)
                    // ->disabled(!Auth::user()->is_admin)
                    ->disabled(!Auth::user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 4]),
                Forms\Components\Placeholder::make('client_referents')
                    ->label('Referenti del Cliente')
                    ->visible(fn(Get $get) => filled($get('client_id')))
                    ->content(function (Get $get) {
                        $clientId = $get('client_id');
                        if (!$clientId)
                            return 'Seleziona un cliente';

                        $referents = \App\Models\Referent::where('client_id', $clientId)->get();

                        if ($referents->isEmpty())
                            return 'Nessun referente registrato.';

                        // Crea una lista testuale o HTML
                        return new \Illuminate\Support\HtmlString(
                            '<ul class="list-disc ml-4">' .
                            $referents->map(fn($r) => "<li><strong>{$r->name}</strong>" . ($r->phone ? " - {$r->phone}" : "") . "</li>")->implode('') .
                            '</ul>'
                        );
                    })
                    ->columnSpan(['sm' => 'full', 'md' => 'full']),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(Contact::deadlines())
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->label('Cliente'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Data scadenza')
                    ->date('d/m/Y'),

                // Tables\Columns\TextColumn::make('time')
                //     ->label('Orario')
                //     ->time('H:i'),
                // Tables\Columns\TextColumn::make('outcome_type')
                //     ->label('Esito'),

                // Tables\Columns\IconColumn::make('note')
                //     ->label('Note')
                //     ->icon(fn ($state): string => filled($state) ? 'fas-note-sticky' : '')
                //     ->color(fn ($state): string => filled($state) ? 'grey' : '')
                //     ->tooltip(fn ($record) => $record->note ?? '')
                //     ->alignCenter()
                //     ->width('1%'),
                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->note ?? '')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label('Regione')
                    ->options(fn() => Region::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value) {
                            $query->whereHas('client', fn(Builder $q) => $q->where('region_id', $value));
                        }
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('province_id')
                    ->label('Provincia')
                    ->options(fn() => Province::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value) {
                            $query->whereHas('client', fn(Builder $q) => $q->where('province_id', $value));
                        }
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('outcome_type')
                    ->label('Esito')
                    ->options(function () {
                        // Creiamo l'array partendo da quello che vogliamo noi
                        $options = [
                            'void' => 'Nessun esito',
                        ];

                        // Aggiungiamo i casi dell'Enum
                        foreach (OutcomeType::cases() as $case) {
                            // Usa $case->getLabel() se hai implementato HasLabel,
                            // altrimenti usa $case->name o $case->value
                            $options[$case->value] = $case->getLabel();
                        }

                        return $options;
                    })
                    ->multiple()
                    // ->query(function (Builder $query, array $data): Builder {
                    //     // Se l'utente sceglie la nostra opzione personalizzata
                    //     if ($data['value'] === 'void') {
                    //         return $query->whereNull('outcome_type');
                    //     }

                    //     // Se l'utente sceglie un'opzione dell'Enum
                    //     if (!empty($data['value'])) {
                    //         return $query->where('outcome_type', $data['value']);
                    //     }

                    //     return $query;
                    // })
                    ->query(function (Builder $query, array $data): Builder {
                        // Se non c'è nulla di selezionato, usciamo subito
                        if (empty($data['values'])) {
                            return $query;
                        }

                        $selectedValues = $data['values'];

                        return $query->where(function (Builder $q) use ($selectedValues) {
                            // Controlliamo se 'void' è tra le opzioni selezionate
                            if (in_array('void', $selectedValues)) {
                                // Filtriamo per i valori NULL...
                                $q->whereNull('outcome_type');

                                // ...e aggiungiamo in OR gli altri valori Enum selezionati (se esistono)
                                $enumValues = array_diff($selectedValues, ['void']);
                                if (!empty($enumValues)) {
                                    $q->orWhereIn('outcome_type', $enumValues);
                                }
                            } else {
                                // Se 'void' non c'è, facciamo una semplice WhereIn
                                $q->whereIn('outcome_type', $selectedValues);
                            }
                        });
                    }),
                SelectFilter::make('date_status')
                    ->label('Stato Data')
                    ->options([
                        'no_date' => 'Senza data',
                        'date' => 'Con data programmata',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'no_date' => $query->whereNull('date'),
                            'date' => $query->whereNotNull('date'),
                            default => $query,
                        };
                    }),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Da data'),
                        DatePicker::make('to_date')
                            ->label('A data'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['from_date'])) {
                            $query->whereDate('date', '>=', $data['from_date']);
                        }
                        if (!empty($data['to_date'])) {
                            $query->whereDate('date', '<=', $data['to_date']);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['from_date'] && $data['to_date']) {
                            return "Dal {$data['from_date']} al {$data['to_date']}";
                        }
                        if ($data['from_date']) {
                            return "Da {$data['from_date']}";
                        }
                        if ($data['to_date']) {
                            return "Fino a {$data['to_date']}";
                        }
                        return null;
                    }),
                SelectFilter::make('user_id')->label('Utente')
                    ->relationship(name: 'user', titleAttribute: 'name')
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
            //
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeadlines::route('/'),
            'create' => Pages\CreateDeadline::route('/create'),
            'edit' => Pages\EditDeadline::route('/{record}/edit'),
            'view' => Pages\ViewDeadline::route('/{record}'),
        ];
    }
}
