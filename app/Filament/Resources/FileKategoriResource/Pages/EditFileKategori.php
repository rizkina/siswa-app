<?php

namespace App\Filament\Resources\FileKategoriResource\Pages;

use App\Filament\Resources\FileKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\GoogleDriveSetup;
use Google\Client;
use Google\Service\Drive;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\GoogleDriveHelper;

class EditFileKategori extends EditRecord
{
    protected static string $resource = FileKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Model $record) {
                    // Panggil handleRecordDeletion() secara manual dari dalam EditFileKategori
                    // karena ini closure di luar class scope
                    $page = app(static::class); // buat instance dari EditFileKategori
                    $page->record = $record;
                    $page->handleGoogleDriveFolderDeletion($record);
                })
                ->after(function () {
                    Notification::make()
                        ->title('Data berhasil dihapus')
                        ->body('Folder Google Drive juga telah dihapus.')
                        ->success()
                        ->send();
                }),
        ];
    }


    protected function handleGoogleDriveFolderDeletion(Model $record): void
    {
        $folderId = $record->folder_id;

        logger()->info("Memasuki handleGoogleDriveFolderDeletion()");
        logger()->info("Folder ID: " . $folderId);

        if ($folderId) {
            $setup = GoogleDriveSetup::where('is_active', true)->first();

            if ($setup) {
                $client = new \Google\Client();
                $client->setClientId($setup->client_id);
                $client->setClientSecret($setup->client_secret);
                $client->setRedirectUri($setup->redirect_uri);
                $client->setAccessType('offline');
                $client->setScopes([\Google\Service\Drive::DRIVE]);

                $client->setAccessToken([
                    'access_token' => $setup->access_token,
                    'refresh_token' => $setup->refresh_token,
                    'expires_in' => $setup->expires_in,
                    'token_type' => $setup->token_type,
                ]);

                if ($client->isAccessTokenExpired()) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

                    if (isset($newToken['access_token'])) {
                        $setup->access_token = $newToken['access_token'];
                        $setup->expires_in = $newToken['expires_in'] ?? null;
                        $setup->token_type = $newToken['token_type'] ?? null;
                        $setup->save();
                        logger()->info("Token berhasil diperbarui");
                    } else {
                        logger()->error("Gagal refresh token: " . json_encode($newToken));
                    }
                }

                $driveService = new \Google\Service\Drive($client);

                try {
                    $driveService->files->delete($folderId);
                    logger()->info("Folder berhasil dihapus dari Google Drive: " . $folderId);
                } catch (\Google\Service\Exception $e) {
                    logger()->error("Gagal menghapus folder Google Drive: " . $e->getMessage());
                } catch (\Exception $e) {
                    logger()->error("Exception umum saat hapus folder: " . $e->getMessage());
                }
            } else {
                logger()->error("Pengaturan GoogleDriveSetup tidak ditemukan.");
            }
        }
    }
    protected function afterSave(): void
    {
        
        $record = $this->record;
        
        if ($record->folder_id && $record->nama) {
            try {
                GoogleDriveHelper::renameFolder($record->folder_id, $record->nama);
                logger()->info("Folder Google Drive berhasil diubah namanya ke: " . $record->nama);
                
                Notification::make()
                    ->title('Data berhasil diperbarui')
                    ->body('Folder Google Drive telah diperbarui.')
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                logger()->error("Gagal mengganti nama folder Google Drive: " . $e->getMessage());

                Notification::make()
                    ->title('Gagal mengganti nama folder')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

}
