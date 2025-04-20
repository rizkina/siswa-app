<?php

namespace App\Helpers;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

function uploadToGoogleDrive($file, $namaFinal)
{
    $config = \App\Models\GoogleDriveSetup::where('is_active', true)->first();
    if (!$config) return [];

    $client = new Client();
    $client->setClientId($config->client_id);
    $client->setClientSecret($config->client_secret);
    $client->refreshToken($config->refresh_token);

    $service = new Drive($client);

    $driveFile = new DriveFile();
    $driveFile->setName($namaFinal);
    $driveFile->setParents([$config->folder_id]);

    $content = file_get_contents(storage_path("app/public/{$file}"));
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
