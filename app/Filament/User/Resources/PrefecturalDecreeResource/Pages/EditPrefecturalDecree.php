<?php

namespace App\Filament\User\Resources\PrefecturalDecreeResource\Pages;

use App\Filament\User\Resources\PrefecturalDecreeResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditPrefecturalDecree extends EditRecord
{
    protected static string $resource = PrefecturalDecreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('deleteAttachment')
                ->label('Elimina allegato')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(function ($record) {
                    if (!$record->attachment_path) return false;
                    $disk = Storage::disk(config('filesystems.default', 'public'));
                    return count($disk->files($record->attachment_path)) > 0;
                })
                ->form(function ($record) {
                    $disk = Storage::disk(config('filesystems.default', 'public'));
                    $files = $disk->files($record->attachment_path);

                    $options = collect($files)->mapWithKeys(fn ($file) => [
                        $file => basename($file),
                    ])->toArray();

                    return [
                        Select::make('file')
                            ->label('Seleziona il decreto da eliminare')
                            ->options($options)
                            ->required()
                            ->searchable(),
                    ];
                })
                ->requiresConfirmation()
                ->modalHeading('Elimina decreto')
                ->modalDescription('Seleziona il file da eliminare. L\'operazione non può essere annullata.')
                ->modalSubmitActionLabel('Elimina')
                ->action(function (array $data, $record) {
                    $disk = Storage::disk(config('filesystems.default', 'public'));
                    $file = $data['file'];

                    if (!$disk->exists($file)) {
                        Notification::make()
                            ->title('File non trovato')
                            ->danger()
                            ->send();
                        return;
                    }

                    $disk->delete($file);

                    // se non restano più file, azzero il path
                    if (empty($disk->files($record->attachment_path))) {
                        $record->update(['attachment_path' => null]);
                    }

                    Notification::make()
                        ->title('Decreto eliminato con successo')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncAutomaticClients();

        static::handleAttachmentUpload($this->record, $this->data);
    }

    // protected static function handleAttachmentUpload($record, array $data): void
    // {
    //     if (empty($data['attachment_upload'])) return;

    //     $filePath = is_array($data['attachment_upload'])
    //         ? array_values($data['attachment_upload'])[0]
    //         : $data['attachment_upload'];

    //     $sourceDisk = Storage::disk(config('filament.default_filesystem_disk', 'public'));
    //     $targetDisk = Storage::disk(config('filesystems.default', 'public'));

    //     if (!$sourceDisk->exists($filePath)) return;

    //     // Cancella il vecchio PDF, se presente, prima di sostituirlo
    //     if ($record->attachment_path && $targetDisk->exists($record->attachment_path)) {
    //         $targetDisk->delete($record->attachment_path);
    //     }

    //     $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    //     $finalPath = "prefectural_decrees/{$record->id}/decreto.{$extension}";

    //     try {
    //         $stream = $sourceDisk->readStream($filePath);
    //         $targetDisk->put($finalPath, $stream);
    //         if (is_resource($stream)) fclose($stream);

    //         $record->update(['attachment_path' => $finalPath]);

    //         Notification::make()
    //             ->title('Decreto aggiornato con successo')
    //             ->success()
    //             ->send();
    //     } catch (\Exception $e) {
    //         Log::error("Errore caricamento decreto prefettizio: " . $e->getMessage());

    //         Notification::make()
    //             ->title('Errore durante il caricamento')
    //             ->body($e->getMessage())
    //             ->danger()
    //             ->send();
    //     } finally {
    //         $sourceDisk->delete($filePath);
    //     }
    // }

    protected static function handleAttachmentUpload($record, array $data): void
    {
        if (empty($data['attachment_upload'])) return;

        $files = is_array($data['attachment_upload'])
            ? $data['attachment_upload']
            : [$data['attachment_upload']];

        $sourceDisk = Storage::disk(config('filament.default_filesystem_disk', 'public'));
        $targetDisk = Storage::disk(config('filesystems.default', 'public'));

        $directory = "prefectural_decrees/{$record->id}";

        if (!$targetDisk->exists($directory)) {
            $targetDisk->makeDirectory($directory);
        }

        $uploaded = 0;

        foreach ($files as $filePath) {
            if (!$sourceDisk->exists($filePath)) continue;

            $fileName = basename($filePath);
            $pathInfo = pathinfo($fileName);
            $finalPath = "{$directory}/{$fileName}";

            // evita di sovrascrivere un file già esistente con lo stesso nome
            $counter = 1;
            while ($targetDisk->exists($finalPath)) {
                $finalPath = "{$directory}/{$pathInfo['filename']}_{$counter}.{$pathInfo['extension']}";
                $counter++;
            }

            try {
                $stream = $sourceDisk->readStream($filePath);
                $targetDisk->writeStream($finalPath, $stream, ['visibility' => 'private']);
                if (is_resource($stream)) fclose($stream);
                $uploaded++;
            } catch (\Exception $e) {
                Log::error("Errore caricamento decreto prefettizio: " . $e->getMessage());
            } finally {
                $sourceDisk->delete($filePath);
            }
        }

        if ($uploaded > 0) {
            $record->update(['attachment_path' => $directory]);

            Notification::make()
                ->title($uploaded === 1 ? 'Decreto caricato con successo' : "{$uploaded} decreti caricati con successo")
                ->success()
                ->send();
        }
    }
}
