<?php

namespace App\Filament\User\Resources\PrefecturalDecreeResource\Pages;

use App\Filament\User\Resources\PrefecturalDecreeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreatePrefecturalDecree extends CreateRecord
{
    protected static string $resource = PrefecturalDecreeResource::class;

    protected function afterCreate(): void
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

        // Se esisteva già un file (caso raro in create, ma per sicurezza)
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
                ->title('Decreto caricato con successo')
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
