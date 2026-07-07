<?php

namespace App\Filament\User\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class PrefecturalDecreesRelationManager extends RelationManager
{
    protected static string $relationship = 'prefecturalDecrees';

    protected static ?string $title = 'Decreti Prefettizi Collegati';

    protected static ?string $modelLabel = 'Decreto Prefettizio';

    public function form(Form $form): Form
    {
        return $form
            ->disabled()
            ->columns(3)
            ->schema([
                // 1. Relazione standard BelongsTo con Province
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name') // Cerca automaticamente sulla relazione
                    ->label('Provincia Emittente')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->columnSpan(1),

                // 2. Relazione BelongsToMany con Cities (Comuni)
                Forms\Components\Select::make('cities')
                    ->label('Comuni') 
                    ->relationship(
                        name: 'cities', 
                        titleAttribute: 'name',
                        // Usiamo una query personalizzata per la relazione
                        modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
                            ->when(
                                $get('province_id'),
                                fn ($query, $provinceId) => $query->where('province_id', $provinceId),
                                fn ($query) => $query->whereRaw('1 = 0') // Se non c'è una provincia selezionata, non mostra nulla
                            )
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Textarea::make('note')
                    ->label('Note')
                    ->columnSpanFull(),

                // // 3. Relazione BelongsToMany con Clients (Clienti)
                // Forms\Components\Select::make('clients')
                //     ->relationship('clients', 'name') // Mappa la relazione belongsToMany() del modello
                //     ->multiple()
                //     ->searchable()
                //     ->preload(),

                // 4. Sezione Dinamica per le Strade (Relazione HasMany)
                Forms\Components\Section::make('Strade Interessate')
                    // ->description('Aggiungi le strade coinvolte in questo decreto')
                    ->collapsed(false) // Collassa se ci sono già strade
                    ->schema([
                        Repeater::make('streets')
                            // ->relationship('streets')
                            ->relationship(
                                name: 'streets',
                                // FILTRO CRUCIALE: Mostra nel repeater solo le strade del city_id di questo cliente
                                modifyQueryUsing: function (Builder $query) {
                                    // $this->getOwnerRecord() restituisce l'istanza del Client corrente
                                    $client = $this->getOwnerRecord();
                                    
                                    return $query->where('city_id', $client->city_id);
                                }
                            )
                            ->label('')
                            // ->columns(3)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nome Strada / Via')
                                    ->placeholder('Es. Via Roma o S.S. 16')
                                    ->required(),
                                // Select::make('city_id')
                                //     ->label('Comune della strada')
                                //     ->relationship(
                                //         name: 'city', 
                                //         titleAttribute: 'name',
                                //         // Filtriamo la query basandoci sui comuni selezionati sopra
                                //         modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
                                //             ->whereIn(
                                //                 'id', 
                                //                 // '../../cities' permette di risalire fuori dal repeater per leggere il campo 'cities'
                                //                 $get('../../cities') ?? [] 
                                //             )
                                //     )
                                //     ->searchable()
                                //     ->preload()
                                //     ->required(),
                                TextInput::make('note')
                                    ->label('Note / Tratto interessato')
                                    ->placeholder('Es. dal km 10 al km 15 o intero tratto'),
                            ])
                            ->addActionLabel('Aggiungi Strada')
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('province.name')
            ->columns([
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provincia Emittente')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cities.name')
                    ->label('Comuni nel Decreto')
                    ->badge()
                    ->separator(', '),

                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Inserimento')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('7xl'),
            ])
            ->actions([
                Tables\Actions\Action::make('viewAttachment')
                    ->label('')
                    ->tooltip('Visualizza decreto')
                    ->icon('hugeicons-pdf-02')
                    ->size('xl')
                    ->color('primary')
                    ->visible(fn ($record) => filled($record->attachment_path))
                    ->url(function ($record) {
                        $disk = Storage::disk(config('filesystems.default', 'public'));
                        try {
                            return $disk->temporaryUrl($record->attachment_path, now()->addMinutes(5));
                        } catch (\Exception $e) {
                            return $disk->url($record->attachment_path);
                        }
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
