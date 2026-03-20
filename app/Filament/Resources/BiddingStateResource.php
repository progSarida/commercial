<?php

namespace App\Filament\Resources;

use App\Enums\FeasibilityType;
use App\Filament\Resources\BiddingStateResource\Pages;
use App\Filament\Resources\BiddingStateResource\RelationManagers;
use App\Models\BiddingState;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BiddingStateResource extends Resource
{
    protected static ?string $model = BiddingState::class;
    public static ?string $pluralModelLabel = 'Dettagli fattibilità';
    public static ?string $modelLabel = 'Dettaglio fattibilità';
    protected static ?string $navigationIcon = 'fas-list';
    protected static ?string $navigationGroup = 'Tabelle';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('feasibility_type')
                    ->label('Fattibilità')
                    ->required()
                    ->options(FeasibilityType::class)
                    ->columnSpan(3),
                TextInput::make('name')->label('Nome')
                    ->required()
                    ->columnSpan(3),
                TextInput::make('description')->label('Descrizione')
                    ->columnSpan(5),
                TextInput::make('position')->label('Posizione')
                    ->required()
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position', 'asc')
            ->columns([
                TextColumn::make('position')->label('Posizione')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('feasibility_type')->label('Fattibilità')
                    ->searchable()
                    ->badge()
                    ->sortable(),
                TextColumn::make('name')->label('Nome')
                    ->searchable(),
                TextColumn::make('description')->label('Descrizione')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListBiddingStates::route('/'),
            'create' => Pages\CreateBiddingState::route('/create'),
            'edit' => Pages\EditBiddingState::route('/{record}/edit'),
        ];
    }
}
