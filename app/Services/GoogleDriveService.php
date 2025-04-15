<?php

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use App\Models\GoogleDriveSetup;


class GoogleDriveService
{
    public static function getClient(): Google_Client
    {
        $config = \App\Models\GoogleDriveSetup::where('is_active', true)->firstOrFail();

        $client = new Google_Client();
        $client->setClientId($config->client_id);
        $client->setClientSecret($config->client_secret);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setScopes(['https://www.googleapis.com/auth/drive']);
        $client->setAccessToken([
            'refresh_token' => $config->refresh_token
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        }

        return $client;
    }

    public static function getDriveService(): Google_Service_Drive
    {
        return new Google_Service_Drive(self::getClient());
    }

    public static function getFolderId(): ?string
    {
        return \App\Models\GoogleDriveSetup::where('is_active', true)->value('folder_id');
    }
}
