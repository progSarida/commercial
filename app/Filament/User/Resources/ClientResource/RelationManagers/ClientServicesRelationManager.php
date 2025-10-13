<?php

namespace App\Filament\User\Resources\ClientResource\RelationManagers;

use App\Enums\ServiceState;
use App\Models\ClientService;
use App\Models\ServiceType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'clientServices';

    protected static ?string $title = 'Servizi';

    public static ?string $modelLabel = 'Servizio';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Select::make('service_type_id')
                    ->label('Tipo di Servizio')
                    ->options(ServiceType::pluck('name', 'id'))
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpan(3),
                Select::make('service_state')
                    ->label('Stato del Servizio')
                    ->options(ServiceState::class)
                    ->required()
                    ->columnSpan(3),
                Textarea::make('note')
                    ->label('Note')
                    ->columnSpan(6),
                TextInput::make('referent')
                    ->label('Referente')
                    ->required()
                    ->columnSpan(5),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->tel()
                    ->required()
                    ->columnSpan(3),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->columnSpan(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('serviceType.name')
                    ->label('Tipo di Servizio')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('service_state')
                    ->label('Stato del Servizio')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('referent')
                    ->label('Referente')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('note')
                    ->label('Note')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_state')
                    ->label('Stato del Servizio')
                    ->options(ServiceState::class),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make()
                //     ->modalWidth('6xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('6xl'),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
