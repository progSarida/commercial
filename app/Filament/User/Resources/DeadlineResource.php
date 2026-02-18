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
                Select::make('client_id')
                    ->label('Cliente')
                    ->required()
                    ->searchable()
                    ->relationship( name: 'client', titleAttribute: 'name')
                    ->columnSpan(['sm' => 'full', 'md' => 20]),
                Select::make('outcome_type')
                    ->label('Esito')
                    // ->options(OutcomeType::class)
                    ->options(OutcomeType::getOptionsByContactType(ContactType::DEADLINE))
                    ->afterStateUpdated( function(Set $set) {
                        $set('date', now()->format('Y-m-d'));
                        $set('time', now()->format('H:i'));
                    })
                    ->columnSpan(['sm' => 'full', 'md' => 5]),
                DatePicker::make('date')
                    ->label('Data')
                    ->extraInputAttributes(['class' => 'text-center'])
                    ->required()
                    ->columnSpan(['sm' => 'full', 'md' => 4]),
                TimePicker::make('time')
                    ->label('Orario')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                Select::make('services')
                    ->label('Servizi')
                    ->options(ServiceType::pluck('name', 'id'))
                    ->multiple()
                    ->live()
                    ->required(fn (Get $get) =>
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
                    ->label('Data')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('time')
                    ->label('Orario')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('outcome_type')
                    ->label('Esito'),
                Tables\Columns\IconColumn::make('note')
                    ->label('Note')
                    ->icon(fn ($state): string => filled($state) ? 'fas-note-sticky' : '')
                    ->color(fn ($state): string => filled($state) ? 'grey' : '')
                    ->tooltip(fn ($record) => $record->note ?? '')
                    ->alignCenter()
                    ->width('1%'),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label('Regione')
                    ->options(fn () => Region::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value) {
                            $query->whereHas('client', fn (Builder $q) => $q->where('region_id', $value));
                        }
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('province_id')
                    ->label('Provincia')
                    ->options(fn () => Province::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value) {
                            $query->whereHas('client', fn (Builder $q) => $q->where('province_id', $value));
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
                    ->query(function (Builder $query, array $data): Builder {
                        // Se l'utente sceglie la nostra opzione personalizzata
                        if ($data['value'] === 'void') {
                            return $query->whereNull('outcome_type');
                        }

                        // Se l'utente sceglie un'opzione dell'Enum
                        if (!empty($data['value'])) {
                            return $query->where('outcome_type', $data['value']);
                        }

                        return $query;
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
                        if (! empty($data['from_date'])) {
                            $query->whereDate('date', '>=', $data['from_date']);
                        }
                        if (! empty($data['to_date'])) {
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
