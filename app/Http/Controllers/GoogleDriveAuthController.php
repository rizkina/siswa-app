<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoogleDriveSetup;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Redirect;

class GoogleDriveAuthController extends Controller
{
    public function redirect()
    {
        $setup = GoogleDriveSetup::where('is_active', true)->firstOrFail();

        $client = new Client();
        $client->setClientId($setup->client_id);
        $client->setClientSecret($setup->client_secret);
        $client->setRedirectUri($setup->redirect_uri); // ✅ HARUS ADA
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope(Drive::DRIVE);

        $authUrl = $client->createAuthUrl();

        return Redirect::to($authUrl);
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return response()->json(['error' => 'Invalid code'], 400);
        }

        $setup = GoogleDriveSetup::where('is_active', true)->firstOrFail();

        $client = new Client();
        $client->setClientId($setup->client_id);
        $client->setClientSecret($setup->client_secret);
        $client->setRedirectUri($setup->redirect_uri); // ✅ WAJIB DISERTAKAN

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return response()->json(['error' => $token['error_description'] ?? 'Gagal mendapatkan token'], 400);
        }

        $setup->refresh_token = $token['refresh_token'] ?? $setup->refresh_token;
        $setup->access_token = $token['access_token'] ?? null;
        $setup->token_type = $token['token_type'] ?? null;
        $setup->expires_in = $token['expires_in'] ?? null;
        $setup->save();

        return redirect('/admin/google-drive-setups')->with('success', 'Token berhasil disimpan');
    }
}
