<?php

namespace App\Filament\User\Resources;

use App\Enums\ClientType;
use App\Filament\User\Resources\BiddingResource\Pages;
use App\Filament\User\Resources\BiddingResource\RelationManagers;
use App\Models\Bidding;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BiddingResource extends Resource
{
    protected static ?string $model = Bidding::class;

    public static ?string $pluralModelLabel = 'Gare';

    public static ?string $modelLabel = 'Gara';

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                CheckboxList::make('serviceTypes')
                    ->label('Gara relativa al servizio di')
                    ->relationship('serviceTypes', 'name')
                    ->options(ServiceType::orderBy('position')->pluck('name', 'id')->toArray())
                    ->columns(6)
                    ->columnSpan(12)
                    ->gridDirection('row'),
                Select::make('client_type')
                    ->label('Tipo')
                    ->options(ClientType::class)
                    ->columnSpan(3),
                TextInput::make('description')
                    ->label('Descrizione')
                    ->required()
                    ->columnSpan(6)
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->columnSpan(6)
                    ->prefix('€'),
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
            'index' => Pages\ListBiddings::route('/'),
            'create' => Pages\CreateBidding::route('/create'),
            'edit' => Pages\EditBidding::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestione';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
