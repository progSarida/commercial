<?php

namespace App\Filament\User\Resources\PrefecturalDecreeResource\Pages;

use App\Filament\User\Resources\PrefecturalDecreeResource;
use Filament\Actions;
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
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncAutomaticClients();

        static::handleAttachmentUpload($this->record, $this->data);
    }

    protected static function handleAttachmentUpload($record, array $data): void
    {
        if (empty($data['attachment_upload'])) return;

        $filePath = is_array($data['attachment_upload'])
            ? array_values($data['attachment_upload'])[0]
            : $data['attachment_upload'];

        $sourceDisk = Storage::disk(config('filament.default_filesystem_disk', 'public'));
        $targetDisk = Storage::disk(config('filesystems.default', 'public'));

        if (!$sourceDisk->exists($filePath)) return;

        // Cancella il vecchio PDF, se presente, prima di sostituirlo
        if ($record->attachment_path && $targetDisk->exists($record->attachment_path)) {
            $targetDisk->delete($record->attachment_path);
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $finalPath = "prefectural_decrees/{$record->id}/decreto.{$extension}";

        try {
            $stream = $sourceDisk->readStream($filePath);
            $targetDisk->put($finalPath, $stream);
            if (is_resource($stream)) fclose($stream);

            $record->update(['attachment_path' => $finalPath]);

            Notification::make()
                ->title('Decreto aggiornato con successo')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error("Errore caricamento decreto prefettizio: " . $e->getMessage());

            Notification::make()
                ->title('Errore durante il caricamento')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $sourceDisk->delete($filePath);
        }
    }
}
