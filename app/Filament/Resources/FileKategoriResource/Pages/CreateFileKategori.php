<?php

namespace App\Filament\Resources\FileKategoriResource\Pages;

use App\Filament\Resources\FileKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\FileKategori;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use App\Models\GoogleDriveSetup;


class CreateFileKategori extends CreateRecord
{
    protected static string $resource = FileKategoriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $setup = GoogleDriveSetup::where('is_active', true)->firstOrFail();

        // Inisialisasi dulu $client
        $client = new Client();
        $client->setClientId($setup->client_id);
        $client->setClientSecret($setup->client_secret);
        $client->setRedirectUri($setup->redirect_uri);
        $client->setAccessType('offline');

        // Set token dari database
        $client->setAccessToken([
            'access_token' => $setup->access_token,
            'refresh_token' => $setup->refresh_token,
            'expires_in' => $setup->expires_in,
            'token_type' => $setup->token_type,
        ]);

        // Refresh token jika expired
        if ($client->isAccessTokenExpired()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            // Simpan token baru ke database
            $setup->access_token = $newToken['access_token'];
            $setup->expires_in = $newToken['expires_in'] ?? null;
            $setup->token_type = $newToken['token_type'] ?? null;
            $setup->save();
        }

        // Buat service Drive
        $driveService = new Drive($client);

        // Nama folder dari input form
        $folderName = $data['nama'];
        $parentFolderId = $setup->folder_id;

        // Metadata folder
        $fileMetadata = new Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => ['$parentFolderId'] // opsional jika mau nested
        ]);

        // Buat folder
        $folder = $driveService->files->create($fileMetadata, [
            'fields' => 'id',
        ]);

        // Tambahkan ID folder ke form data sebelum disimpan
        $data['folder_id'] = $folder->id;

        return $data;
    }

}
