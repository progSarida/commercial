<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PrefecturalDecreeResource\Pages;
use App\Filament\User\Resources\PrefecturalDecreeResource\RelationManagers;
use App\Models\City;
use App\Models\Client;
use App\Models\PrefecturalDecree;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PrefecturalDecreeResource extends Resource
{
    protected static ?string $model = PrefecturalDecree::class;

    public static ?string $pluralModelLabel = 'Decreti Prefettizi';

    public static ?string $modelLabel = 'Decretto Prefettiziale';

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationGroup = 'Gestione';

    protected static ?int $navigationSort = 2;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->columns(3)
    //         ->schema([
    //             // 1. Relazione standard BelongsTo con Province
    //             Forms\Components\Select::make('province_id')
    //                 ->relationship('province', 'name') // Cerca automaticamente sulla relazione
    //                 ->searchable()
    //                 ->preload()
    //                 ->required()
    //                 ->live()
    //                 ->columnSpan(1),

    //             // 2. Relazione BelongsToMany con Cities (Comuni)
    //             Forms\Components\Select::make('cities')
    //                 ->label('Comuni') 
    //                 ->relationship(
    //                     name: 'cities', 
    //                     titleAttribute: 'name',
    //                     // Usiamo una query personalizzata per la relazione
    //                     modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
    //                         ->when(
    //                             $get('province_id'),
    //                             fn ($query, $provinceId) => $query->where('province_id', $provinceId),
    //                             fn ($query) => $query->whereRaw('1 = 0') // Se non c'è una provincia selezionata, non mostra nulla
    //                         )
    //                 )
    //                 ->multiple()
    //                 ->searchable()
    //                 ->preload()
    //                 ->required()
    //                 ->columnSpan(2),

    //             Forms\Components\Textarea::make('note')
    //                 ->label('Note')
    //                 ->columnSpanFull(),

    //             // // 3. Relazione BelongsToMany con Clients (Clienti)
    //             // Forms\Components\Select::make('clients')
    //             //     ->relationship('clients', 'name') // Mappa la relazione belongsToMany() del modello
    //             //     ->multiple()
    //             //     ->searchable()
    //             //     ->preload(),

    //             // Forms\Components\Placeholder::make('attachment_current')
    //             //     ->label('File attuale')
    //             //     ->visible(fn ($record) => $record && $record->attachment_path)
    //             //     ->content(function ($record) {
    //             //         if (!$record || !$record->attachment_path) return '';

    //             //         $disk = config('filesystems.default', 'public');
    //             //         $storage = \Illuminate\Support\Facades\Storage::disk($disk);

    //             //         try {
    //             //             $url = $storage->temporaryUrl($record->attachment_path, now()->addMinutes(5));
    //             //         } catch (\Exception $e) {
    //             //             // Fallback per dischi che non supportano temporaryUrl (es. locale)
    //             //             $url = $storage->url($record->attachment_path);
    //             //         }

    //             //         $name = basename($record->attachment_path);

    //             //         return new \Illuminate\Support\HtmlString(
    //             //             "<a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 hover:underline font-medium\">📄 {$name}</a>"
    //             //         );
    //             //     }),

    //             Forms\Components\FileUpload::make('attachment_upload')
    //                 ->label(fn ($record) => $record && $record->attachment_path ? 'Sostituisci decreto' : 'Carica decreto')
    //                 ->hintAction(
    //                     Forms\Components\Actions\Action::make('viewCurrentAttachment')
    //                         ->label('Visualizza decreto')
    //                         ->icon('heroicon-o-document-text')
    //                         ->color('primary')
    //                         ->visible(fn ($record) => $record && $record->attachment_path)
    //                         ->url(function ($record) {
    //                             $disk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'));
    //                             try {
    //                                 return $disk->temporaryUrl($record->attachment_path, now()->addMinutes(5));
    //                             } catch (\Exception $e) {
    //                                 return $disk->url($record->attachment_path);
    //                             }
    //                         })
    //                         ->openUrlInNewTab()
    //                 )
    //                 ->acceptedFileTypes(['application/pdf'])
    //                 ->directory('temp_uploads')
    //                 ->preserveFilenames()
    //                 ->maxSize(20480) // 20MB
    //                 ->dehydrated(false) // gestito manualmente in afterCreate/afterSave, non va nel record via mass-fill
    //                 ->helperText('Caricare un nuovo file sostituirà quello esistente.')
    //                 ->columnSpanFull(),

    //             // 4. Sezione Dinamica per le Strade (Relazione HasMany)
    //             Forms\Components\Section::make('Strade Interessate')
    //                 ->description('Aggiungi le strade coinvolte in questo decreto')
    //                 ->collapsed(fn($record) => $record && $record->streets()->count() > 0) // Collassa se ci sono già strade
    //                 ->schema([
    //                     Repeater::make('streets')
    //                         ->relationship('streets')
    //                         ->label('')
    //                         ->columns(3)
    //                         ->schema([
    //                             TextInput::make('name')
    //                                 ->label('Nome Strada / Via')
    //                                 ->placeholder('Es. Via Roma o S.S. 16')
    //                                 ->required(),
    //                             Select::make('city_id')
    //                                 ->label('Comune della strada')
    //                                 ->relationship(
    //                                     name: 'city', 
    //                                     titleAttribute: 'name',
    //                                     // Filtriamo la query basandoci sui comuni selezionati sopra
    //                                     modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
    //                                         ->whereIn(
    //                                             'id', 
    //                                             // '../../cities' permette di risalire fuori dal repeater per leggere il campo 'cities'
    //                                             $get('../../cities') ?? [] 
    //                                         )
    //                                 )
    //                                 ->searchable()
    //                                 ->preload()
    //                                 ->required(),
    //                             TextInput::make('note')
    //                                 ->label('Note / Tratto interessato')
    //                                 ->placeholder('Es. dal km 10 al km 15 o intero tratto'),
    //                         ])
    //                         ->addActionLabel('Aggiungi Strada')
    //                 ])->columnSpanFull(),
    //         ]);
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                // 1. Relazione standard BelongsTo con Province
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(fn($set, $state) => $set(
                        'region_name',
                        Province::find($state)?->region?->name
                    ))
                    ->live()
                    ->columnSpan(1),
                    
                Forms\Components\Placeholder::make('region_name')
                    ->label('Regione')
                    ->content(function (Forms\Get $get, $record) {
                        $provinceId = $get('province_id');
                        if (! $provinceId) {
                            return 'Seleziona prima una provincia';
                        }
                        
                        // Cerca la regione collegata alla provincia selezionata
                        $province = \App\Models\Province::with('region')->find($provinceId);
                        return $province?->region?->name ?? '—';
                    })
                    ->columnSpan(1),

                // 2. Relazione BelongsToMany con Cities (Comuni) - Modificata per la reattività passiva
                Forms\Components\Select::make('cities')
                    ->label('Comuni') 
                    ->relationship(
                        name: 'cities', 
                        titleAttribute: 'name',
                        // Filtra comunque per provincia se selezionata, altrimenti mostra tutti o lascia libero
                        modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
                            ->when(
                                $get('province_id'),
                                fn ($query, $provinceId) => $query->where('province_id', $provinceId)
                            )
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live() // Manteniamo live per aggiornamenti consistenti
                    ->columnSpan(2),

                Forms\Components\Textarea::make('note')
                    ->label('Note')
                    ->columnSpanFull(),

                // Forms\Components\FileUpload::make('attachment_upload')
                //     ->label(fn ($record) => $record && $record->attachment_path ? 'Sostituisci decreto' : 'Carica decreto')
                //     ->hintAction(
                //         Forms\Components\Actions\Action::make('viewCurrentAttachment')
                //             ->label('Visualizza decreto')
                //             ->icon('heroicon-o-document-text')
                //             ->color('primary')
                //             ->visible(fn ($record) => $record && $record->attachment_path)
                //             ->url(function ($record) {
                //                 $disk = \Illuminate\Support\Facades\Storage::disk(config('filesystems.default', 'public'));
                //                 try {
                //                     return $disk->temporaryUrl($record->attachment_path, now()->addMinutes(5));
                //                 } catch (\Exception $e) {
                //                     return $disk->url($record->attachment_path);
                //                 }
                //             })
                //             ->openUrlInNewTab()
                //     )
                //     ->acceptedFileTypes(['application/pdf'])
                //     ->directory('temp_uploads')
                //     ->preserveFilenames()
                //     ->maxSize(20480) // 20MB
                //     ->dehydrated(false) // gestito manualmente in afterCreate/afterSave, non va nel record via mass-fill
                //     ->helperText('Caricare un nuovo file sostituirà quello esistente.')
                //     ->columnSpanFull(),

                Forms\Components\FileUpload::make('attachment_upload')
                    ->label('Carica decreto/i')
                    ->multiple()
                    ->reorderable()
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('temp_uploads')
                    ->preserveFilenames()
                    ->maxSize(20480) // 20MB per file
                    ->dehydrated(false) // gestito manualmente in afterCreate/afterSave
                    ->helperText('Puoi caricare più file PDF. I nuovi file si aggiungono a quelli esistenti.')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Decreti caricati')
                    ->collapsed(fn ($record) => $record && $record->attachment_path)
                    ->visible(fn ($record) => $record && $record->attachment_path)
                    ->schema([
                        Forms\Components\Placeholder::make('current_attachments')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->attachment_path) {
                                    return 'Nessun decreto caricato.';
                                }

                                $disk = Storage::disk(config('filesystems.default', 'public'));
                                $files = $disk->files($record->attachment_path);

                                if (empty($files)) {
                                    return 'Nessun decreto caricato.';
                                }

                                return new \Illuminate\Support\HtmlString(
                                    collect($files)->map(function ($file) use ($disk) {
                                        $name = basename($file);
                                        try {
                                            $url = $disk->temporaryUrl($file, now()->addMinutes(15));
                                        } catch (\Exception $e) {
                                            $url = $disk->url($file);
                                        }
                                        return <<<HTML
                                        <div class="flex items-center gap-2 py-1">
                                            <span class="text-gray-400 text-xs">📄</span>
                                            <a href="{$url}" target="_blank" class="text-sm text-primary-600 hover:underline">
                                                {$name}
                                            </a>
                                        </div>
                                        HTML;
                                    })->implode('')
                                );
                            })
                            ->columnSpan('full'),
                    ]),

                // Forms\Components\Section::make('Decreti caricati')
                //     ->collapsed(fn ($record) => $record && $record->attachment_path)
                //     ->visible(fn ($record) => $record && $record->attachment_path)
                //     ->schema(function ($record) {
                //         if (!$record || !$record->attachment_path) {
                //             return [
                //                 Forms\Components\Placeholder::make('none')
                //                     ->label('')
                //                     ->content('Nessun decreto caricato.'),
                //             ];
                //         }

                //         $disk = Storage::disk(config('filesystems.default', 'public'));
                //         $files = $disk->files($record->attachment_path);

                //         if (empty($files)) {
                //             return [
                //                 Forms\Components\Placeholder::make('none')
                //                     ->label('')
                //                     ->content('Nessun decreto caricato.'),
                //             ];
                //         }

                //         return collect($files)->map(function ($file) use ($disk, $record) {
                //             $name = basename($file);
                //             $key = md5($file);

                //             return Forms\Components\Actions::make([
                //                 Forms\Components\Actions\Action::make("view_{$key}")
                //                     ->label($name)
                //                     ->icon('heroicon-o-document-text')
                //                     ->color('gray')
                //                     ->url(function () use ($disk, $file) {
                //                         try {
                //                             return $disk->temporaryUrl($file, now()->addMinutes(15));
                //                         } catch (\Exception $e) {
                //                             return $disk->url($file);
                //                         }
                //                     })
                //                     ->openUrlInNewTab(),

                //                 Forms\Components\Actions\Action::make("delete_{$key}")
                //                     ->label('Elimina')
                //                     ->icon('heroicon-o-trash')
                //                     ->color('danger')
                //                     ->requiresConfirmation()
                //                     ->modalHeading("Eliminare {$name}?")
                //                     ->modalDescription('Questa azione non può essere annullata.')
                //                     ->modalSubmitActionLabel('Elimina')
                //                     ->action(function () use ($disk, $file, $record) {
                //                         $disk->delete($file);

                //                         // se non restano più file, azzero il path
                //                         if (empty($disk->files($record->attachment_path))) {
                //                             $record->update(['attachment_path' => null]);
                //                         }

                //                         Notification::make()
                //                             ->title('Decreto eliminato')
                //                             ->success()
                //                             ->send();
                //                     }),
                //             ])->key($key);
                //         })->toArray();
                //     }),

                // 4. Sezione Dinamica per le Strade (Relazione HasMany)
                Forms\Components\Section::make('Strade Interessate')
                    // ->description('Aggiungi le strade coinvolte in questo decreto')
                    ->collapsed(fn($record) => $record && $record->streets()->count() > 0)
                    ->schema([
                        Repeater::make('streets')
                            ->relationship('streets')
                            ->label('')
                            ->columns(3)
                            ->live() // Rende l'intero repeater reattivo ai cambiamenti (aggiunte/rimozioni di righe)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                // Quando una riga viene eliminata o lo stato del repeater cambia, ricalcoliamo i comuni
                                $streets = $get('streets') ?? [];
                                $cityIds = collect($streets)
                                    ->pluck('city_id')
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->toArray();
                                
                                $set('cities', $cityIds);
                            })
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nome Strada / Via')
                                    ->placeholder('Es. Via Roma o S.S. 16')
                                    ->required(),
                                    
                                Select::make('city_id')
                                    ->label('Comune della strada')
                                    ->relationship(
                                        name: 'city', 
                                        titleAttribute: 'name',
                                        // Filtra le città del repeater solo per la provincia selezionata a monte
                                        modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
                                            ->when(
                                                $get('../../province_id'),
                                                fn ($query, $provinceId) => $query->where('province_id', $provinceId)
                                            )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live() // Rende la select interna reattiva appena cambia il valore
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        // Risaliamo al livello superiore del form per recuperare lo stato attuale delle strade
                                        $streets = $get('../../streets') ?? [];
                                        
                                        // Estraiamo tutti i city_id unici attualmente selezionati nel repeater
                                        $cityIds = collect($streets)
                                            ->pluck('city_id')
                                            ->filter() // Rimuove eventuali valori nulli
                                            ->unique()
                                            ->values()
                                            ->toArray();
                                        
                                        // Aggiorna lo stato della select multi-select 'cities' globale
                                        $set('../../cities', $cityIds);
                                    }),
                                    
                                TextInput::make('note')
                                    ->label('Note / Tratto interessato')
                                    ->placeholder('Es. dal km 10 al km 15 o intero tratto'),
                            ])
                            ->addActionLabel('Aggiungi Strada')
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Regione')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provincia')
                    ->sortable()
                    ->searchable(),

                // Mostra l'elenco dei comuni separati da virgola in automatico e supporta i badge
                Tables\Columns\TextColumn::make('cities.name')
                    ->label('Comuni')
                    ->badge() // Opzionale: rende i comuni dei comodi "tag" visivi
                    ->separator(', '),

                // Conta quante strade ci sono in quel decreto direttamente in tabella
                Tables\Columns\TextColumn::make('streets_count')
                    ->label('N. Strade')
                    ->counts('streets')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tables\Columns\TextColumn::make('attachment_path')
                //     ->label('Decreto')
                //     ->formatStateUsing(fn ($state) => $state ? '📄 PDF' : '—')
                //     ->color(fn ($state) => $state ? 'primary' : 'gray')
                //     ->url(function ($record) {
                //         if (!$record->attachment_path) return null;
                //         $disk = Storage::disk(config('filesystems.default', 'public'));
                //         try {
                //             return $disk->temporaryUrl($record->attachment_path, now()->addMinutes(5));
                //         } catch (\Exception $e) {
                //             return $disk->url($record->attachment_path);
                //         }
                //     })
                //     ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label('Regione')
                    ->relationship(name: 'region', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('province_id')->label('Provincia')
                    ->relationship(name: 'province', titleAttribute: 'name')
                    ->searchable()
                    ->preload()->optionsLimit(5),
                SelectFilter::make('cities')
                    ->label('Comuni')
                    ->relationship(name: 'cities', titleAttribute: 'name') // Usa la relazione belongsToMany del modello
                    ->multiple() // Abilita la selezione multipla
                    ->searchable() // Permette di cercare tra i clienti se sono molti
                    ->preload() // Carica i primi record per velocizzare l'interfaccia
            ])
            ->actions([
                // Tables\Actions\Action::make('viewAttachment')
                //     ->label('')
                //     ->tooltip('Visualizza decreto')
                //     ->icon('hugeicons-pdf-02')
                //     ->size('xl')
                //     ->color('primary')
                //     ->visible(fn ($record) => filled($record->attachment_path))
                //     ->url(function ($record) {
                //         $disk = Storage::disk(config('filesystems.default', 'public'));
                //         try {
                //             return $disk->temporaryUrl($record->attachment_path, now()->addMinutes(5));
                //         } catch (\Exception $e) {
                //             return $disk->url($record->attachment_path);
                //         }
                //     })
                //     ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPrefecturalDecrees::route('/'),
            'create' => Pages\CreatePrefecturalDecree::route('/create'),
            'view' => Pages\ViewPrefecturalDecree::route('/{record}'),
            'edit' => Pages\EditPrefecturalDecree::route('/{record}/edit'),
        ];
    }
}
