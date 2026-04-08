<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use DB;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CreateBidding extends CreateRecord
{
    protected static string $resource = BiddingResource::class;

    // protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    // {
    //     // 1. Preparazione ID Servizi
    //     $serviceTypeIds = array_map('intval', $data['serviceTypes'] ?? []);
    //     sort($serviceTypeIds);

    //     // 2. Costruzione query di controllo duplicati
    //     $query = Bidding::where('client_type', $data['client_type'])
    //         ->where('client_id', $data['client_id']);

    //     // 3. Controllo Date: Solo se il valore è presente in $data, lo aggiungiamo al filtro.
    //     // Se in $data è null, cerchiamo record che abbiano null nel database.
    //     $dateFields = [
    //         'interest_deadline_date',
    //         'inspection_deadline_date',
    //         'deadline_date'
    //     ];

    //     foreach ($dateFields as $field) {
    //         if (!empty($data[$field])) {
    //             $query->whereDate($field, $data[$field]);
    //         } else {
    //             $query->whereNull($field);
    //         }
    //     }

    //     // 4. Esecuzione query con controllo sulla relazione Many-to-Many
    //     $exists = $query->withCount('serviceTypes')
    //         ->having('service_types_count', '=', count($serviceTypeIds))
    //         ->get()
    //         ->filter(function ($bidding) use ($serviceTypeIds) {
    //             $currentIds = $bidding->serviceTypes()->pluck('service_type_id')->toArray();
    //             sort($currentIds);

    //             return $currentIds == $serviceTypeIds;
    //         })
    //         ->first();

    //     if ($exists) {
    //         Notification::make()
    //             ->title('Gara già esistente')
    //             ->body("È stata trovata una gara (Id: {$exists->id}) con questi dati")
    //             ->warning()
    //             ->persistent() // La notifica non sparisce finché non si interagisce
    //             ->send();

    //         $this->halt();
    //     }

    //     DB::beginTransaction();
    //     try {

    //         $record = parent::handleRecordCreation($data);
    //         DB::commit();
    //         return $record;

    //     } catch (Halt $e) {
    //         throw $e; // Importante: rilanciala per permettere a Filament/Livewire di gestire l'arresto
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Notification::make()
    //             ->title('Errore durante la creazione della gara')
    //             ->body($e->getMessage())
    //             ->danger()
    //             ->send();

    //         throw $e;
    //     }
    // }

    protected bool $forceCreate = false;

    protected function beforeCreate(): void
    {
        // Se l'utente ha cliccato su "Forza salvataggio", saltiamo il controllo
        if ($this->forceCreate) {
            return;
        }

        $data = $this->data; // Nelle pagine Create, i dati sono in $this->data

        // Logica di controllo duplicati
        $serviceTypeIds = array_map('intval', $data['serviceTypes'] ?? []);
        sort($serviceTypeIds);

        $query = Bidding::where('client_type', $data['client_type'])
            ->where('client_id', $data['client_id']);

        $dateFields = ['interest_deadline_date', 'inspection_deadline_date', 'deadline_date'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $query->whereDate($field, $data[$field]);
            } else {
                $query->whereNull($field);
            }
        }

        $exists = $query->withCount('serviceTypes')
            ->having('service_types_count', '=', count($serviceTypeIds))
            ->get()
            ->filter(function ($bidding) use ($serviceTypeIds) {
                $currentIds = $bidding->serviceTypes()->pluck('service_type_id')->toArray();
                sort($currentIds);
                return $currentIds == $serviceTypeIds;
            })
            ->first();

        if ($exists) {
            Notification::make()
                ->title('Gara già esistente')
                ->body("Esiste già una gara (ID: {$exists->id}) con gli stessi dati.")
                ->warning()
                ->persistent()
                ->actions([
                    Action::make('force')
                        ->label('Forza salvataggio')
                        ->color('danger')
                        ->icon('heroicon-o-arrow-right')
                        ->dispatch('forceCreateEvent'),
                    Action::make('cancel')
                        ->label('Annulla')
                        ->color('gray')
                        ->close(),
                ])
                ->send();

            // Blocca la creazione nativa di Filament
            $this->halt();
        }
    }

    protected function getListeners(): array
    {
        return array_merge(parent::getListeners(), [
            'forceCreateEvent' => 'forceCreateAndSave',
        ]);
    }

    public function forceCreateAndSave(): void
    {
        $this->forceCreate = true;
        // Ora il dd scatterà

        $this->create();
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::beginTransaction();
        try {
            $record = parent::handleRecordCreation($data);
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Errore durante la creazione')
                ->body($e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }

    protected function afterCreate(): void
    {
        $this->handleZipUpload($this->record, $this->data);
    }

    protected static function handleZipUpload($record, array $data): void
    {
        if (empty($data['temp_zip'])) return;

        $zipField = $data['temp_zip'];
        $zipPath = is_array($zipField) ? array_values($zipField)[0] : $zipField;

        // 1. Determina i dischi dinamicamente
        // 'livewire-tmp' è spesso un disco separato, ma Filament di solito usa il default
        $sourceDiskName = config('filament.default_filesystem_disk', 'public');
        $targetDiskName = config('filesystems.default', 'public');

        $sourceDisk = Storage::disk($sourceDiskName);
        $targetDisk = Storage::disk($targetDiskName);

        if (!$sourceDisk->exists($zipPath)) return;

        // 2. Crea un file temporaneo LOCALE (indispensabile per ZipArchive)
        $tempZipFile = tempnam(sys_get_temp_dir(), 'zip_');
        file_put_contents($tempZipFile, $sourceDisk->get($zipPath));

        // 3. Cartella temporanea locale per l'estrazione
        $tempExtractDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'extract_' . uniqid();
        mkdir($tempExtractDir, 0777, true);

        try {
            $zip = new ZipArchive();
            if ($zip->open($tempZipFile) === true) {
                $zip->extractTo($tempExtractDir);
                $zip->close();

                // 4. Scansione file estratti
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                $extractSubPath = "biddings_attach/{$record->id}";

                foreach ($files as $file) {
                    if ($file->isFile()) {
                        // 1. Prendi solo il nome del file (es: "documento.pdf")
                        // ignorando il percorso della sottocartella originale nello ZIP
                        $fileName = $file->getFilename();

                        // 2. Definisci il percorso finale (tutti nella stessa cartella)
                        $finalPath = $extractSubPath . '/' . $fileName;

                        // 3. Upload sul disco target
                        $stream = fopen($file->getPathname(), 'r');
                        $targetDisk->put($finalPath, $stream);
                        if (is_resource($stream)) fclose($stream);
                    }
                }

                // 6. Aggiorna il DB
                $record->update(['attachment_path' => $extractSubPath]);

                // 7. Pulizia: cancella lo ZIP caricato originariamente
                $sourceDisk->delete($zipPath);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Errore estrazione ZIP: " . $e->getMessage());
        } finally {
            // Pulizia file temporanei locali al server
            self::deleteDirectory($tempExtractDir);
            if (file_exists($tempZipFile)) @unlink($tempZipFile);
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

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }
}
