<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BiddingDataSourceResource\Pages;
use App\Filament\Resources\BiddingDataSourceResource\RelationManagers;
use App\Models\BiddingDataSource;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BiddingDataSourceResource extends Resource
{
    protected static ?string $model = BiddingDataSource::class;
    public static ?string $pluralModelLabel = 'Fonti di dati';
    public static ?string $modelLabel = 'Fonte di dati';
    protected static ?string $navigationIcon = 'fas-list';
    protected static ?string $navigationGroup = 'Tabelle';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                TextInput::make('name')->label('Nome')
                    ->required()
                    ->columnSpan(1),
                TextInput::make('description')->label('Descrizione')
                    ->columnSpan(2),
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
            'index' => Pages\ListBiddingDataSources::route('/'),
            'create' => Pages\CreateBiddingDataSource::route('/create'),
            'edit' => Pages\EditBiddingDataSource::route('/{record}/edit'),
        ];
    }
}
