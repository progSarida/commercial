<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class EditBidding extends EditRecord
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        $currentBidding = $this->record;
        $currentDate = $currentBidding->deadline_date ?? $currentBidding->interest_deadline_date;
        $currentTime = $currentBidding->deadline_time ?? $currentBidding->interest_deadline_time;
        $dateField = "COALESCE(deadline_date, interest_deadline_date)";
        $timeField = "COALESCE(deadline_time, interest_deadline_time)";
        // Precedente per deadline_date: data precedente O stessa data con ID minore
        $previousDeadline = Bidding::query()
            ->where('id', '!=', $currentBidding->id)
            ->where(function ($q) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                $q->whereRaw("$dateField < ?", [$currentDate])
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField < ?", [$currentTime]);
                })
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField = ?", [$currentTime])
                        ->where('id', '<', $currentBidding->id);
                });
            })
            ->orderByRaw("$dateField DESC")
            ->orderByRaw("$timeField DESC")
            ->orderBy('id', 'desc')
            ->first();
        // Successivo per deadline_date: data successiva O stessa data con ID maggiore
        $nextDeadline = Bidding::query()
            ->where('id', '!=', $currentBidding->id)
            ->where(function ($q) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                $q->whereRaw("$dateField > ?", [$currentDate])
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField > ?", [$currentTime]);
                })
                ->orWhere(function ($sub) use ($currentDate, $currentTime, $currentBidding, $dateField, $timeField) {
                    $sub->whereRaw("$dateField = ?", [$currentDate])
                        ->whereRaw("$timeField = ?", [$currentTime])
                        ->where('id', '>', $currentBidding->id);
                });
            })
            ->orderByRaw("$dateField ASC")
            ->orderByRaw("$timeField ASC")
            ->orderBy('id', 'asc')
            ->first();
        // Precedente per inspection_deadline_date: data precedente O stessa data con ID minore
        $previousInspection = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '<', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '<', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'desc')->orderBy('id', 'desc')->first();
        // Successivo per inspection_deadline_date: data successiva O stessa data con ID maggiore
        $nextInspection = Bidding::whereNotNull('inspection_deadline_date')
            ->when($currentBidding->inspection_deadline_date, function ($query, $date) use ($currentBidding) {
                return $query->where(function ($q) use ($date, $currentBidding) {
                    $q->where('inspection_deadline_date', '>', $date)
                        ->orWhere(function ($subQ) use ($date, $currentBidding) {
                            $subQ->where('inspection_deadline_date', '=', $date)
                                 ->where('id', '>', $currentBidding->id);
                        });
                });
            })
            ->orderBy('inspection_deadline_date', 'asc')->orderBy('id', 'asc')->first();

        return [
            Actions\Action::make('back')
                ->label('Indietro')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
            // Scorrimento in base a data di scadenza
            Actions\Action::make('previous_deadline')
                ->label('Scadenza Prec.')
                ->color('success')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $previousDeadline !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('edit', ['record' => $previousDeadline->id]))),

            Actions\Action::make('next_deadline')
                ->label('Scadenza Succ.')
                ->color('success')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $nextDeadline !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('edit', ['record' => $nextDeadline->id]))),

            // Scorrimento in base a data di sopralluogo
            Actions\Action::make('previous_inspection')
                ->label('Sopralluogo Prec.')
                ->color('info')
                ->icon('heroicon-o-arrow-left-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $previousInspection !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('edit', ['record' => $previousInspection->id]))),

            Actions\Action::make('next_inspection')
                ->label('Sopralluogo Succ.')
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle')
                ->visible(fn() => $currentBidding->inspection_deadline_date !== null && $nextInspection !== null)
                ->action(fn() => $this->redirect(BiddingResource::getUrl('edit', ['record' => $nextInspection->id]))),

            Actions\ActionGroup::make([
                Actions\Action::make('uploadFile')
                    ->label('Carica allegati')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('info')
                    ->modalSubmitActionLabel('Carica')
                    ->visible(function($record) {
                        return $record->attachment_path
                            && Storage::exists($record->attachment_path);
                    })
                    ->form([
                        FileUpload::make('attachments')
                            ->label('Seleziona File (anche ZIP)')
                            ->multiple()
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', 'image/*', 'application/pdf', '*/*'])
                            ->directory('temp_uploads')
                            ->preserveFilenames()
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $file->getClientOriginalExtension();

                                $finalName = $filename . '.' . $extension;
                                $counter = 1;

                                while (Storage::disk(config('filament.default_filesystem_disk', 'public'))->exists('temp_uploads/' . $finalName)) {
                                    $finalName = $filename . '_' . $counter . '.' . $extension;
                                    $counter++;
                                }

                                return $finalName;
                            })
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $sourceDiskName = config('filament.default_filesystem_disk', 'public');
                        $targetDiskName = config('filesystems.default', 'public');

                        $sourceDisk = Storage::disk($sourceDiskName);
                        $targetDisk = Storage::disk($targetDiskName);

                        $extractSubPath = $record->attachment_path;
                        $processedFiles = 0;

                        foreach ($data['attachments'] as $filePath) {
                            if (!$sourceDisk->exists($filePath)) continue;

                            $filename = basename($filePath);
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                            // Se è uno ZIP, estraiamo i file
                            if ($extension === 'zip') {
                                // Crea file temporaneo locale
                                $tempZipFile = tempnam(sys_get_temp_dir(), 'zip_');
                                file_put_contents($tempZipFile, $sourceDisk->get($filePath));

                                $tempExtractDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'extract_' . uniqid();
                                mkdir($tempExtractDir, 0777, true);

                                try {
                                    $zip = new ZipArchive();
                                    if ($zip->open($tempZipFile) === true) {
                                        $zip->extractTo($tempExtractDir);
                                        $zip->close();

                                        // Scansione file estratti
                                        $files = new \RecursiveIteratorIterator(
                                            new \RecursiveDirectoryIterator($tempExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                                            \RecursiveIteratorIterator::SELF_FIRST
                                        );

                                        foreach ($files as $file) {
                                            if ($file->isFile()) {
                                                $originalName = $file->getFilename();
                                                $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
                                                $fileExtension = $file->getExtension();

                                                $finalName = $originalName;
                                                $counter = 1;

                                                // Anti-sovrascrittura
                                                while ($targetDisk->exists($extractSubPath . '/' . $finalName)) {
                                                    $finalName = $filenameOnly . '_' . $counter . '.' . $fileExtension;
                                                    $counter++;
                                                }

                                                $finalPath = $extractSubPath . '/' . $finalName;

                                                // Upload sul disco target usando Stream
                                                $stream = fopen($file->getPathname(), 'r');
                                                $targetDisk->put($finalPath, $stream);
                                                if (is_resource($stream)) fclose($stream);

                                                $processedFiles++;
                                            }
                                        }
                                    }
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error("Errore estrazione ZIP: " . $e->getMessage());

                                    Notification::make()
                                        ->title('Errore durante l\'estrazione dello ZIP')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                } finally {
                                    // Pulizia
                                    self::deleteDirectory($tempExtractDir);
                                    if (file_exists($tempZipFile)) @unlink($tempZipFile);
                                }

                                // Cancella lo ZIP temporaneo
                                $sourceDisk->delete($filePath);

                            } else {
                                // File normale: copia diretta con anti-sovrascrittura
                                $filenameOnly = pathinfo($filename, PATHINFO_FILENAME);
                                $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

                                $finalName = $filename;
                                $counter = 1;

                                while ($targetDisk->exists($extractSubPath . '/' . $finalName)) {
                                    $finalName = $filenameOnly . '_' . $counter . '.' . $fileExtension;
                                    $counter++;
                                }

                                $finalPath = $extractSubPath . '/' . $finalName;

                                // Copia il file usando Stream per compatibilità S3
                                $stream = $sourceDisk->readStream($filePath);
                                $targetDisk->put($finalPath, $stream);
                                if (is_resource($stream)) fclose($stream);

                                // Cancella il file temporaneo
                                $sourceDisk->delete($filePath);

                                $processedFiles++;
                            }
                        }

                        Notification::make()
                            ->title('Caricamento completato')
                            ->body($processedFiles . ' file caricati con successo.')
                            ->success()
                            ->send();
                    }),

                // Actions\Action::make('uploadFile')
                //         ->label('Carica allegati')
                //         ->icon('heroicon-o-document-arrow-up')
                //         ->color('info')
                //         ->modalSubmitActionLabel('Carica')
                //         ->visible(function($record) {
                //                 return $record->attachment_path
                //                         && Storage::exists($record->attachment_path);
                //             }
                //         )
                //         ->form([
                //             FileUpload::make('attachments')
                //                 ->label('Seleziona File')
                //                 ->multiple()
                //                 ->directory(fn ($record) => $record->attachment_path)
                //                 ->preserveFilenames()
                //                 ->getUploadedFileNameForStorageUsing(function ($file, $record) {
                //                     $disk = config('filesystems.default');
                //                     $directory = $record->attachment_path;

                //                     $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                //                     $extension = $file->getClientOriginalExtension();

                //                     $finalName = $filename . '.' . $extension;
                //                     $counter = 1;

                //                     while (Storage::disk($disk)->exists($directory . '/' . $finalName)) {
                //                         $finalName = $filename . '_' . $counter . '.' . $extension;
                //                         $counter++;
                //                     }

                //                     return $finalName;
                //                 })
                //                 ->required(),
                //         ])
                //         ->action(function (array $data) {
                //             Notification::make()
                //                 ->title('Caricamento completato')
                //                 ->success()
                //                 ->send();
                //         }),

                Actions\Action::make('deleteFile')
                    ->label('Elimina file')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(function($record) {
                            return $record->attachment_path
                                    && Storage::exists($record->attachment_path)
                                    && !empty(Storage::files($record->attachment_path));
                        }
                    )
                    ->form([
                        Select::make('file_to_delete')
                            ->label('Seleziona il file da eliminare')
                            ->options(function ($record) {
                                if (!$record || !$record->attachment_path) {
                                    return [];
                                }
                                $files = Storage::files($record->attachment_path);
                                return collect($files)->mapWithKeys(function ($file) {
                                    return [$file => basename($file)];
                                })->toArray();
                            })
                            ->required()
                            ->native(false)
                            ->searchable(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Elimina allegato')
                    ->modalDescription('Questa azione non può essere annullata.')
                    ->modalSubmitActionLabel('Elimina')
                    ->modalCancelActionLabel('Annulla')
                    ->action(function (array $data) {
                        $file = $data['file_to_delete'];

                        if (Storage::exists($file)) {
                            Storage::delete($file);

                            Notification::make()
                                ->title('File eliminato con successo')
                                ->body('Il file ' . basename($file) . ' è stato eliminato.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('File non trovato')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->label('Operazioni')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('info')
            ->button(),

            // Cancellazione gara
            // Actions\DeleteAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->color('success'),
            $this->getCancelFormAction(),
            $this->getResetFormAction(),
            $this->getDeleteFormAction()
                ->extraAttributes([
                    'class' => ' md:ml-auto md:w-auto ',
                ]),
        ];
    }

    protected function getDeleteFormAction()
    {
        return Actions\DeleteAction::make('delete')
                ->requiresConfirmation()
                ->modalHeading('Conferma eliminazione gara')
                ->modalDescription('Sei sicuro di voler eliminare questa gara? Questa azione non può essere annullata.')
                ->modalSubmitActionLabel('Elimina')
                ->modalCancelActionLabel('Annulla');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return Actions\Action::make('cancel')
            ->label('Indietro')
            ->color('gray')
            ->url(function () {
                if ($this->previousUrl && str($this->previousUrl)->contains('/biddings?')) {
                    return $this->previousUrl;
                }
                return BiddingResource::getUrl('index');
            });
    }

    protected function getResetFormAction(): Actions\Action
    {
        return Actions\Action::make('reset')
            ->label('Annulla')
            ->color('gray')
            ->action(function () {
                $this->data = $this->getRecord()->toArray();
                $this->fillForm();
            });
    }

    // protected function afterSave(): void
    // {
    //     $this->handleZipUpload($this->record, $this->data);
    // }

    // protected static function handleZipUploadOld($record, array $data): void
    // {
    //     // Se non c'è ZIP caricato o già processato → esci
    //     if (empty($data['temp_zip']) || $record->attachment_path) {
    //         return;
    //     }

    //     $zipPath = array_values($data['temp_zip'])[0];
    //     $fullZipPath = storage_path('app/public/' . $zipPath);

    //     if (!file_exists($fullZipPath)) {
    //         return;
    //     }

    //     // CARTELLA FINALE CON L'ID
    //     $extractPath = "biddings_attach/{$record->id}";
    //     Storage::disk('public')->makeDirectory($extractPath);

    //     $zip = new ZipArchive();
    //     if ($zip->open($fullZipPath) === true) {
    //         $zip->extractTo(storage_path('app/public/' . $extractPath));
    //         $zip->close();

    //         // Cancella lo ZIP temporaneo
    //         Storage::disk('public')->delete($zipPath);

    //         // SALVA IL PERCORSO NEL DATABASE
    //         $record->update([
    //             'attachment_path' => $extractPath,
    //         ]);
    //     }
    // }

    // protected static function handleZipUpload($record, array $data): void
    // {
    //     // 1. Usa il nome del campo corretto della tua pagina Edit (restore_zip)
    //     if (empty($data['restore_zip'])) return;

    //     $zipField = $data['restore_zip'];
    //     $zipPath = is_array($zipField) ? array_values($zipField)[0] : $zipField;

    //     // 2. Determina i dischi dinamicamente
    //     $sourceDiskName = config('filament.default_filesystem_disk', 'public');
    //     $targetDiskName = config('filesystems.default', 'public');

    //     $sourceDisk = Storage::disk($sourceDiskName);
    //     $targetDisk = Storage::disk($targetDiskName);

    //     if (!$sourceDisk->exists($zipPath)) return;

    //     // 3. Crea un file temporaneo LOCALE per ZipArchive
    //     $tempZipFile = tempnam(sys_get_temp_dir(), 'zip_');
    //     file_put_contents($tempZipFile, $sourceDisk->get($zipPath));

    //     // 4. Cartella temporanea locale per l'estrazione
    //     $tempExtractDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'extract_' . uniqid();
    //     mkdir($tempExtractDir, 0777, true);

    //     try {
    //         $zip = new ZipArchive();
    //         if ($zip->open($tempZipFile) === true) {
    //             $zip->extractTo($tempExtractDir);
    //             $zip->close();

    //             // 5. Scansione file estratti
    //             $files = new \RecursiveIteratorIterator(
    //                 new \RecursiveDirectoryIterator($tempExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
    //                 \RecursiveIteratorIterator::SELF_FIRST
    //             );

    //             $extractSubPath = "biddings_attach/{$record->id}";

    //             foreach ($files as $file) {
    //                 if ($file->isFile()) {
    //                     // LOGICA FLATTEN: Prendi solo il nome del file
    //                     $originalName = $file->getFilename();
    //                     $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
    //                     $extension = $file->getExtension();

    //                     $finalName = $originalName;
    //                     $counter = 1;

    //                     // ANTI-SOVRASCRITTURA: Controlla se il file esiste già sul target (S3 o Locale)
    //                     while ($targetDisk->exists($extractSubPath . '/' . $finalName)) {
    //                         $finalName = $filenameOnly . '_' . $counter . '.' . $extension;
    //                         $counter++;
    //                     }

    //                     $finalPath = $extractSubPath . '/' . $finalName;

    //                     // 6. Upload sul disco target (Locale o S3) via Stream
    //                     $stream = fopen($file->getPathname(), 'r');
    //                     $targetDisk->put($finalPath, $stream);
    //                     if (is_resource($stream)) fclose($stream);
    //                 }
    //             }

    //             // 7. Aggiorna il DB
    //             $record->update(['attachment_path' => $extractSubPath]);

    //             // 8. Pulizia: cancella lo ZIP caricato originariamente
    //             $sourceDisk->delete($zipPath);
    //         }
    //     } catch (\Exception $e) {
    //         \Illuminate\Support\Facades\Log::error("Errore estrazione ZIP in Edit: " . $e->getMessage());
    //     } finally {
    //         // Pulizia finale file temporanei locali al server
    //         self::deleteDirectory($tempExtractDir);
    //         if (file_exists($tempZipFile)) @unlink($tempZipFile);
    //     }
    // }

    // // Helper per cancellare directory ricorsivamente
    // private static function deleteDirectory($dir): void
    // {
    //     if (!is_dir($dir)) {
    //         return;
    //     }

    //     $files = array_diff(scandir($dir), ['.', '..']);
    //     foreach ($files as $file) {
    //         $path = $dir . DIRECTORY_SEPARATOR . $file;
    //         is_dir($path) ? self::deleteDirectory($path) : unlink($path);
    //     }
    //     rmdir($dir);
    // }

    protected function afterSave(): void
    {
        $this->handleAttachmentsUpload($this->record, $this->data);
    }

    protected static function handleAttachmentsUpload($record, array $data): void
    {
        if (empty($data['attachments'])) return;

        $sourceDiskName = config('filament.default_filesystem_disk', 'public');
        $targetDiskName = config('filesystems.default', 'public');

        $sourceDisk = Storage::disk($sourceDiskName);
        $targetDisk = Storage::disk($targetDiskName);

        $extractSubPath = "biddings_attach/{$record->id}";
        $processedFiles = 0;

        foreach ($data['attachments'] as $filePath) {
            if (!$sourceDisk->exists($filePath)) continue;

            $filename = basename($filePath);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Se è uno ZIP, estraiamo i file
            if ($extension === 'zip') {
                $processedFiles += self::extractAndUploadZip($sourceDisk, $targetDisk, $filePath, $extractSubPath);
            } else {
                // File normale: copia diretta con anti-sovrascrittura
                if (self::uploadSingleFile($sourceDisk, $targetDisk, $filePath, $extractSubPath, $filename)) {
                    $processedFiles++;
                }
            }

            // Cancella il file temporaneo
            $sourceDisk->delete($filePath);
        }

        // Aggiorna il DB solo se ci sono file processati
        if ($processedFiles > 0) {
            $record->update(['attachment_path' => $extractSubPath]);

            Notification::make()
                ->title('Caricamento completato')
                ->body("{$processedFiles} file caricati con successo.")
                ->success()
                ->send();
        }
    }

    private static function extractAndUploadZip($sourceDisk, $targetDisk, $zipPath, $extractSubPath): int
    {
        $tempZipFile = tempnam(sys_get_temp_dir(), 'zip_');
        file_put_contents($tempZipFile, $sourceDisk->get($zipPath));

        $tempExtractDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'extract_' . uniqid();
        mkdir($tempExtractDir, 0777, true);

        $processedFiles = 0;

        try {
            $zip = new ZipArchive();
            if ($zip->open($tempZipFile) === true) {
                $zip->extractTo($tempExtractDir);
                $zip->close();

                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tempExtractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $originalName = $file->getFilename();
                        $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
                        $fileExtension = $file->getExtension();

                        $finalName = $originalName;
                        $counter = 1;

                        // Anti-sovrascrittura
                        while ($targetDisk->exists($extractSubPath . '/' . $finalName)) {
                            $finalName = $filenameOnly . '_' . $counter . '.' . $fileExtension;
                            $counter++;
                        }

                        $finalPath = $extractSubPath . '/' . $finalName;

                        // Upload usando Stream
                        $stream = fopen($file->getPathname(), 'r');
                        $targetDisk->put($finalPath, $stream);
                        if (is_resource($stream)) fclose($stream);

                        $processedFiles++;
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Errore estrazione ZIP: " . $e->getMessage());

            Notification::make()
                ->title('Errore durante l\'estrazione dello ZIP')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            self::deleteDirectory($tempExtractDir);
            if (file_exists($tempZipFile)) @unlink($tempZipFile);
        }

        return $processedFiles;
    }

    private static function uploadSingleFile($sourceDisk, $targetDisk, $filePath, $extractSubPath, $filename): bool
    {
        try {
            $filenameOnly = pathinfo($filename, PATHINFO_FILENAME);
            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

            $finalName = $filename;
            $counter = 1;

            // Anti-sovrascrittura
            while ($targetDisk->exists($extractSubPath . '/' . $finalName)) {
                $finalName = $filenameOnly . '_' . $counter . '.' . $fileExtension;
                $counter++;
            }

            $finalPath = $extractSubPath . '/' . $finalName;

            // Copia usando Stream
            $stream = $sourceDisk->readStream($filePath);
            $targetDisk->put($finalPath, $stream);
            if (is_resource($stream)) fclose($stream);

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Errore caricamento file {$filename}: " . $e->getMessage());
            return false;
        }
    }

    private static function deleteDirectory($dir): void
    {
        if (!is_dir($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? self::deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
