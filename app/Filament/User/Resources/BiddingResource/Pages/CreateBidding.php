<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CreateBidding extends CreateRecord
{
    protected static string $resource = BiddingResource::class;

    protected function afterCreate(): void
    {
        $this->handleZipUpload($this->record, $this->data);
    }

    protected static function handleZipUpload($record, array $data): void
    {
        // Se non c'è ZIP caricato o già processato → esci
        if (empty($data['temp_zip']) || $record->attachment_path) {
            return;
        }

        $zipPath = array_values($data['temp_zip'])[0];
        $fullZipPath = storage_path('app/public/' . $zipPath);

        if (!file_exists($fullZipPath)) {
            return;
        }

        // CARTELLA FINALE CON L'ID
        $extractPath = "biddings_attach/{$record->id}";
        Storage::disk('public')->makeDirectory($extractPath);

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath) === true) {
            $zip->extractTo(storage_path('app/public/' . $extractPath));
            $zip->close();

            // Cancella lo ZIP temporaneo
            Storage::disk('public')->delete($zipPath);

            // SALVA IL PERCORSO NEL DATABASE
            $record->update([
                'attachment_path' => $extractPath,
            ]);

            // Opzionale: svuota il campo temp così non riappare
            // (non serve se usi ->visible() sopra)
        }
    }
}
