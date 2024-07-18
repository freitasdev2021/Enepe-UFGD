<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZoomController extends Controller
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $accountId;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = env('ZOOM_CLIENT_ID');
        $this->clientSecret = env('ZOOM_CLIENT_SECRET');
        $this->accountId = env('ZOOM_ACCOUNT_ID');
    }

    public function getAccessToken()
    {
        $response = $this->client->post('https://zoom.us/oauth/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ],
        ]);


        $data = json_decode($response->getBody()->getContents(), true);
        Cache::put('zoom_access_token', $data['access_token'], $data['expires_in']);
        Cache::put('zoom_token_expires_at', now()->addSeconds($data['expires_in']));

        return $data['access_token'];
    }

    public function getStoredOrRenewZoomAccessToken()
    {
        // Verificar se o token de acesso está armazenado no cache
        $accessToken = Cache::get('zoom_access_token');
        $expiresAt = Cache::get('zoom_token_expires_at');

        // Se o token expirou ou não está presente, renovar o token
        if (!$accessToken || !$expiresAt || $expiresAt <= now()) {
            $accessToken = $this->renewZoomAccessToken();
        }

        return $accessToken;
    }

    public function renewZoomAccessToken()
    {
        $clientId = env('ZOOM_CLIENT_ID');
        $clientSecret = env('ZOOM_CLIENT_SECRET');

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        $data = $response->json();
        $accessToken = $data['access_token'];
        $expiresIn = $data['expires_in'];

        // Armazenar token de acesso no cache
        Cache::put('zoom_access_token', $accessToken, $expiresIn);
        Cache::put('zoom_token_expires_at', now()->addSeconds($expiresIn));

        return $accessToken;
    }

    public function createMeeting($token, $meetingData)
    {
        $response = $this->client->post('https://api.zoom.us/v2/users/me/meetings', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => $meetingData,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function generateSignature($meetingNumber, $role)
    {
        $key = $this->clientId;
        $secret = $this->clientSecret;
        $iat = time();
        $exp = $iat + 60 * 60 * 2; // Expira em 2 horas

        $payload = [
            'sdkKey' => $key,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'appKey' => $key,
            'tokenExp' => $exp
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

}
