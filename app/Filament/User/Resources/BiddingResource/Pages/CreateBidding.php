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

    protected static function handleZipUploadOld($record, array $data): void
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

    protected static function handleZipUpload($record, array $data): void
    {
        // Se non c'è ZIP caricato o già processato → esci
        if (empty($data['temp_zip']) || $record->attachment_path) {
            return;
        }

        $zipPath = is_array($data['temp_zip'])
            ? array_values($data['temp_zip'])[0]
            : $data['temp_zip'];

        // Livewire salva i file temporanei nel disco 'local' nella cartella 'livewire-tmp'
        $livewireDisk = config('livewire.temporary_file_upload.disk', 'local');

        // Verifica l'esistenza nel disco di Livewire
        if (!Storage::disk($livewireDisk)->exists($zipPath)) {
            return;
        }

        // Leggi il contenuto del file ZIP dal disco di Livewire
        $zipContents = Storage::disk($livewireDisk)->get($zipPath);

        // Crea un file temporaneo locale per ZipArchive
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip_');
        file_put_contents($tempZipPath, $zipContents);

        try {
            // CARTELLA FINALE CON L'ID
            $extractPath = "biddings_attach/{$record->id}";
            Storage::disk('public')->makeDirectory($extractPath);

            $zip = new ZipArchive();
            if ($zip->open($tempZipPath) === true) {
                // Crea una directory temporanea per l'estrazione
                $tempExtractPath = sys_get_temp_dir() . '/extract_' . uniqid();
                mkdir($tempExtractPath, 0777, true);

                // Estrai nella cartella temporanea locale
                $zip->extractTo($tempExtractPath);
                $zip->close();

                // Carica tutti i file estratti su Storage
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempExtractPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $relativePath = str_replace($tempExtractPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                        // Normalizza i separatori per compatibilità cross-platform e S3
                        $relativePath = str_replace('\\', '/', $relativePath);
                        $destinationPath = $extractPath . '/' . $relativePath;

                        Storage::disk('public')->put(
                            $destinationPath,
                            file_get_contents($file->getPathname())
                        );
                    }
                }

                // Pulisci la cartella temporanea di estrazione
                self::deleteDirectory($tempExtractPath);

                // Cancella lo ZIP temporaneo dal disco di Livewire
                Storage::disk($livewireDisk)->delete($zipPath);

                // SALVA IL PERCORSO NEL DATABASE
                $record->update([
                    'attachment_path' => $extractPath,
                ]);
            }
        } finally {
            // Pulisci il file ZIP temporaneo locale
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
        }
    }

    // Helper per cancellare directory ricorsivamente
    private static function deleteDirectory($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? self::deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
