<?php

namespace App\Helpers;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use App\Models\GoogleDriveSetup;
use Illuminate\Support\Facades\Log;

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

        $driveFile = new DriveFile();
        $driveFile->setName($namaFinal);
        $driveFile->setParents([$folderId ?? $defaultFolderId]);

        $fullLocalPath = storage_path("app/public/{$localFilePath}");
        if (!file_exists($fullLocalPath)) {
            throw new \Exception("File {$localFilePath} not found in storage.");
        }

        $content = file_get_contents($fullLocalPath);

        $uploadedFile = $service->files->create($driveFile, [
            'data' => $content,
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
        ]);

        return [
            'file_id' => $uploadedFile->id,
            'web_view_link' => $uploadedFile->webViewLink,
        ];
    }

    public static function createFolder($folderName, $parentId = null)
    {
        [$client, $defaultFolderId] = self::getGoogleClient();
        $service = new Drive($client);

        $folderMetadata = new DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId ?? $defaultFolderId],
        ]);

        $folder = $service->files->create($folderMetadata, [
            'fields' => 'id',
        ]);

        return $folder->id ?? null;
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
