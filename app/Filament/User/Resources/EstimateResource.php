<?php

namespace App\Filament\User\Resources;

use App\Enums\EstimateState;
use App\Filament\User\Resources\EstimateResource\Pages;
use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    public static ?string $pluralModelLabel = 'Preventivi';

    public static ?string $modelLabel = 'Preventivo';

    protected static ?string $navigationIcon = 'heroicon-s-document-magnifying-glass';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('clientServices')
                    ->label('Servizi')
                    ->getStateUsing(function (Estimate $record) {
                        return $record->getFormattedClientServices()['label'];
                    })
                    ->tooltip(function (Estimate $record) {
                        return $record->getFormattedClientServices()['tooltip'];
                    }),
                SelectColumn::make('estimate_state')
                    ->label('Stato')
                    ->options(EstimateState::class)
                    ->sortable()
                    ->disabled(fn(?Estimate $record) => $record?->path === null || !Auth::user()->close_estimate),
                ToggleColumn::make('done')
                    ->label('Chiuso')
                    ->onIcon('heroicon-s-check-circle')
                    ->offIcon('heroicon-s-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn(?Estimate $record) => $record?->path === null || !Auth::user()->close_estimate),
            ])
            ->filtersFormWidth('md')
            ->filters([
                SelectFilter::make('file_status')
                    ->label('Documento')
                    ->options([
                        'requested' => 'Richiesto',
                        'uploaded' => 'Caricato',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value === 'uploaded') {
                            $query->whereNotNull('path');
                        } elseif ($value === 'requested') {
                            $query->whereNull('path');
                        }
                    }),
                SelectFilter::make('estimate_state')->label('Stato preventivo')
                    ->options(EstimateState::class)
                    ->multiple()->preload(),
                Filter::make('date_range')
                    ->columns(2)
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Da data')
                            ->columnSpan(1),
                        DatePicker::make('to_date')
                            ->label('A data')
                            ->columnSpan(1),
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
                            return "Preventivi dal {$data['from_date']} al {$data['to_date']}";
                        }
                        if ($data['from_date']) {
                            return "Preventivi dal {$data['from_date']}";
                        }
                        if ($data['to_date']) {
                            return "Preventivi al {$data['to_date']}";
                        }
                        return null;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('upload_file')
                    ->label('Carica File')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('path')
                            ->label('File')
                            ->disk('public')
                            ->directory('estimates')
                            ->preserveFilenames()
                            ->required(),
                    ])
                    ->action(function (Estimate $record, array $data): void {
                        $record->update($data);
                        // Forza il refresh della tabella
                        $record->refresh();
                    })
                    ->visible(fn(Estimate $record) => $record->estimate_state !== EstimateState::APPROVED),

                Tables\Actions\Action::make('view_file')
                    ->label('Visualizza')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Estimate $record) => $record->path ? asset('storage/' . $record->path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Estimate $record) => $record->path !== null),

                Tables\Actions\Action::make('delete_file')
                    ->label('Elimina File')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Elimina File')
                    ->modalDescription('Sei sicuro di voler eliminare il file caricato?')
                    ->modalSubmitActionLabel('Elimina')
                    ->modalCancelActionLabel('Annulla')
                    ->action(function (Estimate $record): void {
                        if ($record->path && Storage::disk('public')->exists($record->path)) {
                            Storage::disk('public')->delete($record->path);
                        }
                        $record->update(['path' => null]);
                        // Forza il refresh della tabella
                        $record->refresh();
                    })
                    ->visible(fn(Estimate $record) => $record->path !== null),
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
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
            'view' => Pages\ViewEstimate::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestione';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }
}
