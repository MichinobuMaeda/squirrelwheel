<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MastodonApiRepository
{
    /**
     * Generate redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return config('sqwh.mstdn.server') . '/oauth/authorize' .
            '?response_type=code&client_id=' . config('sqwh.mstdn.client_key') .
            '&redirect_uri=' . route('auth.mastodon') .
            '&scope=read write' .
            '&force_login=true' .
            '&lang=' . config('app.locale');
    }

    /**
     * Obtain a token
     *
     * @param string  $code
     * @return mixed
     */
    public function getToken($code)
    {
        Log::info('mstdn obtain token: ' . $code);
        $response = Http::asForm()->post(
            config('sqwh.mstdn.server') . '/oauth/token',
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('sqwh.mstdn.client_key'),
                'client_secret' => config('sqwh.mstdn.client_secret'),
                'redirect_uri' => route('auth.mastodon'),
                'scope' => 'read write',
            ]
        );

        if ($response->successful()) {
            $token = $response->json();
            Log::info('mstdn token: ' . $token['access_token']);
            return $token;
        } else {
            Log::error(
                'mstdn failed to obtain a token: ' .
                    json_encode($response->json())
            );
            return null;
        }
    }

    /**
     * Get user info
     *
     * @param string  $access_token
     * @return mixed
     */
    public function getUserInfo($access_token)
    {
        Log::info('mstdn get user info: ' . $access_token);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get(
            config('sqwh.mstdn.server') . '/api/v1/accounts/verify_credentials',
        );

        if ($response->successful()) {
            $user = $response->json();

            if (in_array($user['username'], config('sqwh.mstdn.users'))) {
                Log::info('mstdn user: ' . $user['username']);
                return $user;
            } else {
                Log::error('mstdn unregistered account: ' . $user['username']);
                return null;
            }
        } else {
            Log::error(
                'mstdn failed to verify account credentials: ' .
                    json_encode($response->json())
            );
            return null;
        }
    }

    /**
     * Post the article.
     *
     * @param string  $status
     * @return void
     */
    public function post(string $status)
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . config('sqwh.mstdn.access_token'),
            'Idempotency-Key' => hash('sha256', $status),
        ])->asForm()->post(config('sqwh.mstdn.server') . '/api/v1/statuses', [
            'status' => $status,
            'sensitive' => 'false',
            'visibility' => 'public',
            'language' => config('app.locale'),
        ]);
    }
}
