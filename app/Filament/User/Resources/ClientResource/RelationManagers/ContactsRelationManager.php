<?php

namespace App\Filament\User\Resources\ClientResource\RelationManagers;

use App\Enums\ContactType;
use App\Enums\OutcomeType;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    protected static ?string $title = 'Contatti';

    public static ?string $modelLabel = 'Contatto';

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('contact_type')
                    ->label('Tipo contatto')
                    ->options(ContactType::class)
                    ->columnSpan(3),
                DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->columnSpan(3),
                TimePicker::make('time')
                    ->label('Orario')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->columnSpan(3),
                Select::make('outcome_type')
                    ->label('Esito')
                    ->options(OutcomeType::class)
                    ->columnSpan(3),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpan(9),
                Select::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->default(Auth::user()->id)
                    // ->disabled(!Auth::user()->is_admin)
                    ->disabled(!Auth::user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->columnSpan(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('contact_type')
                    ->label('Tipo contatto'),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
