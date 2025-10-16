<?php
namespace App\Filament\User\Resources;
use App\Enums\ContactType;
use App\Enums\OutcomeType;
use App\Filament\User\Resources\DeadlineResource\Pages;
use App\Filament\User\Resources\DeadlineResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('contact_type')
                    ->label('Tipo contatto')
                    ->options(ContactType::class)
                    ->default(ContactType::DEADLINE)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                TimePicker::make('time')
                    ->label('Orario')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                Select::make('outcome_type')
                    ->label('Esito')
                    ->options(OutcomeType::class)
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpan(['sm' => 'full', 'md' => 9]),
                Select::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->default(Auth::user()->id)
                    ->disabled(!Auth::user()->is_admin)
                    ->dehydrated()
                    ->columnSpan(['sm' => 'full', 'md' => 3]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(Contact::deadlines())
            ->columns([
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
            'index' => Pages\ListDeadlines::route('/'),
            'create' => Pages\CreateDeadline::route('/create'),
            'edit' => Pages\EditDeadline::route('/{record}/edit'),
        ];
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Clienti';
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }
}