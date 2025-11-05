<?php
namespace App\Filament\User\Resources;
use App\Enums\ContactType;
use App\Enums\OutcomeType;
use App\Filament\User\Resources\CallResource\Pages;
use App\Filament\User\Resources\CallResource\RelationManagers;
use App\Models\Contact;
use App\Models\Province;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
class CallResource extends Resource
{
    protected static ?string $model = Contact::class;
    public static ?string $pluralModelLabel = 'Chiamate';
    public static ?string $modelLabel = 'Chiamata';
    protected static ?string $navigationIcon = 'fas-phone';
    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('contact_type')
                    ->label('Tipo contatto')
                    ->options(ContactType::class)
                    ->default(ContactType::CALL)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 2]),
                Select::make('client_id')
                    ->label('Cliente')
                    ->searchable()
                    ->relationship( name: 'client', titleAttribute: 'name')
                    ->columnSpan(['sm' => 'full', 'md' => 4]),
                DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->columnSpan(['sm' => 'full', 'md' => 2]),
                TimePicker::make('time')
                    ->label('Orario')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->columnSpan(['sm' => 'full', 'md' => 2]),
                Select::make('outcome_type')
                    ->label('Esito')
                    ->options(OutcomeType::class)
                    ->columnSpan(['sm' => 'full', 'md' => 2]),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpan(['sm' => 'full', 'md' => 9]),
                Select::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->default(Auth::user()->id)
                    // ->disabled(!Auth::user()->is_admin)
                    ->disabled(!Auth::user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(Contact::calls())
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
                Tables\Columns\TextColumn::make('note')
                    ->label('Note'),
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
                SelectFilter::make('user_id')->label('Utente')
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->searchable()
                    ->preload()->optionsLimit(5),
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
                    })
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
            'index' => Pages\ListCalls::route('/'),
            'create' => Pages\CreateCall::route('/create'),
            'edit' => Pages\EditCall::route('/{record}/edit'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Clienti';
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
