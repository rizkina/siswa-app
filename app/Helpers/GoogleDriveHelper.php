<?php

namespace App\Helpers;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use App\Models\GoogleDriveSetup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleDriveHelper
{
    protected static function getGoogleClient()
    {
        $config = GoogleDriveSetup::where('is_active', true)->first();
        if (!$config) {
            throw new \Exception('Google Drive setup not found or inactive.');
        }

        $client = new Client();
        $client->setClientId($config->client_id);
        $client->setClientSecret($config->client_secret);
        $client->setAccessToken([
            'access_token' => $config->access_token,
            'expires_in' => 3600, // default 1 jam
            'created' => time() - 4000, // anggap kadang sudah expired
            'refresh_token' => $config->refresh_token,
        ]);

        // Cek kalau access token expired
        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($config->refresh_token);

            if (isset($newToken['access_token'])) {
                $config->access_token = $newToken['access_token'];
                $config->save();

                $client->setAccessToken($newToken);
            } else {
                throw new \Exception('Gagal refresh access token: ' . json_encode($newToken));
            }
        }

        return [$client, $config->folder_id];
    }

    protected static function getDriveService()
    {
        [$client, ] = self::getGoogleClient();
        return new Drive($client);
    }

    public static function uploadFile($localFilePath, $namaFinal, $folderId = null)
    {
        [$client, $defaultFolderId] = self::getGoogleClient();
        $service = new Drive($client);
    
        // Siapkan metadata file
        $driveFile = new DriveFile();
        $driveFile->setName($namaFinal);
        $driveFile->setParents([$folderId ?? $defaultFolderId]);
    
        // Deteksi apakah path lokal berasal dari Storage Laravel atau path asli (realpath)
        $fullLocalPath = file_exists($localFilePath)
            ? $localFilePath
            : Storage::disk('public')->path($localFilePath);
    
        if (!file_exists($fullLocalPath)) {
            throw new \Exception("File {$localFilePath} tidak ditemukan.");
        }
    
        $content = file_get_contents($fullLocalPath);
    
        // Upload file ke Google Drive
        $uploadedFile = $service->files->create($driveFile, [
            'data' => $content,
            'uploadType' => 'multipart',
            'fields' => 'id, name',
        ]);
    
        // Tambahkan permission agar file dapat diakses publik
        $permission = new \Google\Service\Drive\Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');
        $service->permissions->create($uploadedFile->id, $permission);
    
        // Ambil link tampilan
        $file = $service->files->get($uploadedFile->id, [
            'fields' => 'webViewLink',
        ]);
    
        return [
            'file_id' => $uploadedFile->id,
            'web_view_link' => $file->webViewLink,
        ];
    }

    public static function isFolderNameExists(Drive $driveService, string $folderName, ?string $parentId = null): bool
    {
        $escapedName = addslashes($folderName); // untuk hindari tanda kutip rusak
        $query = "mimeType='application/vnd.google-apps.folder' and name='{$escapedName}' and trashed = false";
        
        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        }

        $response = $driveService->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        return count($response->getFiles()) > 0;
    }

    public static function createFolder(string $folderName): ?array
    {
        $service = self::getDriveService();

        // Cek apakah folder sudah ada
        $query = sprintf(
            "mimeType='application/vnd.google-apps.folder' and name='%s' and trashed=false",
            addslashes($folderName)
        );

        $response = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        if (count($response->getFiles()) > 0) {
            $existingFolder = $response->getFiles()[0];

            return [
                'id' => $existingFolder->getId(),
                'is_existing' => true,
            ];
        }

        // Buat folder baru jika tidak ada
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);

        $folder = $service->files->create($fileMetadata, [
            'fields' => 'id',
        ]);

        return [
            'id' => $folder->id,
            'is_existing' => false,
        ];
    }

    public static function renameDriveItem(Drive $driveService, string $fileId, string $newName): void
    {
        $fileMetadata = new DriveFile([
            'name' => $newName,
        ]);

        $driveService->files->update($fileId, $fileMetadata);
    }

    public static function renameFolder(string $folderId, string $newName): void
    {
        $service = self::getDriveService();

        try {
            // Cek apakah sudah ada folder lain dengan nama yang sama (selain folder yang sedang diganti)
            $query = sprintf(
                "mimeType='application/vnd.google-apps.folder' and name='%s' and trashed=false",
                addslashes($newName)
            );

            $existingFolders = $service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id, name)'
            ]);

            foreach ($existingFolders->getFiles() as $folder) {
                // Jika ada folder lain dengan nama sama tapi ID berbeda, tolak perubahan
                if ($folder->getId() !== $folderId) {
                    throw new \Exception("Folder dengan nama '{$newName}' sudah ada di Google Drive.");
                }
            }

            // Lakukan perubahan nama
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $newName
            ]);

            $service->files->update($folderId, $fileMetadata);

        } catch (\Google\Service\Exception $e) {
            throw new \Exception("Google Drive error: " . $e->getMessage());
        } catch (\Exception $e) {
            throw $e; // lempar ulang untuk ditangani di luar
        }
    }

    public static function deleteFileOrFolder($fileId)
    {
        try {
            $service = self::getDriveService();
            $service->files->delete($fileId);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus file/folder dari Google Drive: ' . $e->getMessage());
        }
    }
}
