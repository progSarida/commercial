<?php

namespace App\Filament\User\Resources\BiddingResource\Pages;

use App\Filament\User\Resources\BiddingResource;
use App\Models\Bidding;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class EditBidding extends EditRecord
{
    protected static string $resource = BiddingResource::class;

    protected function getHeaderActions(): array
    {
        $currentBidding = $this->record;
        // Precedente per deadline_date: data precedente O stessa data con ID minore
        $previousDeadline = Bidding::where(function ($query) use ($currentBidding) {
                $query->where('deadline_date', '<', $currentBidding->deadline_date)
                    ->orWhere(function ($q) use ($currentBidding) {
                        $q->where('deadline_date', '=', $currentBidding->deadline_date)
                          ->where('id', '<', $currentBidding->id);
                    });
            })
            ->orderBy('deadline_date', 'desc')->orderBy('id', 'desc')->first();
        // Successivo per deadline_date: data successiva O stessa data con ID maggiore
        $nextDeadline = Bidding::where(function ($query) use ($currentBidding) {
                $query->where('deadline_date', '>', $currentBidding->deadline_date)
                    ->orWhere(function ($q) use ($currentBidding) {
                        $q->where('deadline_date', '=', $currentBidding->deadline_date)
                          ->where('id', '>', $currentBidding->id);
                    });
            })
            ->orderBy('deadline_date', 'asc')->orderBy('id', 'asc')->first();
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
            // Scorrimento in base a data di scadenza gara
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

            // Cancellazione gara
            // Actions\DeleteAction::make(),
        ];
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

    protected function afterSave(): void
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
            Storage::disk($livewireDisk)->makeDirectory($extractPath);

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

                        Storage::disk($livewireDisk)->put(
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
