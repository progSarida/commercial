<?php

namespace App\Filament\User\Resources\ClientResource\RelationManagers;

use App\Enums\EstimateState;
use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EstimatesRelationManager extends RelationManager
{
    protected static string $relationship = 'estimates';

    protected static ?string $title = 'Preventivi';

    public static ?string $modelLabel = 'Preventivo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contact_type')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contact_type')
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y'),
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
                    ->disabled(fn(?Estimate $record) => $record?->path === null || !Auth::user()->close_estimate),
                ToggleColumn::make('done')
                    ->label('Chiuso')
                    ->onIcon('heroicon-s-check-circle')
                    ->offIcon('heroicon-s-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn(?Estimate $record) => $record?->path === null || !Auth::user()->close_estimate),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('upload_file')
                    ->label('Carica File')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('path')
                            ->label('File')
                            // ->disk('public')
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
                    // ->url(fn(Estimate $record) => $record->path ? asset('storage/' . $record->path) : null)
                    ->url(fn($record): ?string => $record && $record->path ? Storage::temporaryUrl($record->path,now()->addMinutes(1)) : null)
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
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
