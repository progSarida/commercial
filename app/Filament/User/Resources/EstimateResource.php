<?php

namespace App\Filament\User\Resources;

use App\Enums\EstimateState;
use App\Filament\User\Resources\EstimateResource\Pages;
use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    public static ?string $pluralModelLabel = 'Preventivi';

    public static ?string $modelLabel = 'Preventivo';

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

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
                        $services = $record->client->clientServices()
                            ->whereNotNull('service_state')
                            ->with('serviceType')
                            ->get()
                            ->map(function ($service) {
                                return $service->serviceType->name . ' - ' . ($service->note ?? 'No note');
                            })
                            ->toArray();
                        return implode(', ', $services) ?: 'Nessun servizio';
                    }),
                SelectColumn::make('estimate_state')
                    ->label('Stato')
                    ->options(EstimateState::class)
                    ->sortable()
                    ->disabled(fn(?Estimate $record) => $record && $record->path === null),
                ToggleColumn::make('done')
                    ->label('Chiuso')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->visible(fn(?Estimate $record) => $record && $record->path !== null),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon(''),
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
                    })
                    ->visible(fn(Estimate $record) => $record->estimate_state !== EstimateState::APPROVED),

                Tables\Actions\Action::make('view_file')
                    ->label('Visualizza')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Estimate $record) => $record->path ? asset('storage/' . $record->path) : null)
                    ->openUrlInNewTab()
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
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestione';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
