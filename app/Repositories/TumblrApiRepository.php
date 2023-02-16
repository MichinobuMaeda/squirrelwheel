<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TumblrApiRepository
{
    /**
     * Generate redirect url
     *
     * @param string  $state
     * @return string
     */
    public function getRedirectUrl($state)
    {
        return 'https://www.tumblr.com/oauth2/authorize' .
            '?response_type=code' .
            '&client_id=' . config('sqwh.tumblr.consumer_key') .
            '&redirect_uri=' . route('auth.tumblr') .
            '&scope=basic offline_access' .
            '&state=' . $state;
    }

    /**
     * Obtain a token
     *
     * @param string  $code
     * @return mixed
     */
    public function getToken($code)
    {
        Log::info('tumblr obtain token: ' . $code);
        $response = Http::asForm()->post(
            'https://api.tumblr.com/v2/oauth2/token',
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('sqwh.tumblr.consumer_key'),
                'client_secret' => config('sqwh.tumblr.consumer_secret'),
                'redirect_uri' => route('auth.tumblr'),
            ]
        );

        if ($response->successful()) {
            $token = $response->json();
            Log::info('tumblr token: ' . $token['access_token']);
            return $token;
        } else {
            Log::error(
                'tumblr failed to obtain a token: ' .
                    json_encode($response->json())
            );
            return null;
        }
    }

    /**
     * Refresh a token
     *
     * @param string  $refresh_token
     * @return mixed
     */
    public function refreshToken($refresh_token)
    {
        Log::info('tumblr refresh token: ' . $refresh_token);
        $response = Http::asForm()->post(
            'https://api.tumblr.com/v2/oauth2/token',
            [
                'grant_type' => 'refresh_token',
                'client_id' => config('sqwh.tumblr.consumer_key'),
                'client_secret' => config('sqwh.tumblr.consumer_secret'),
                'refresh_token' => $refresh_token,
            ]
        );

        if ($response->successful()) {
            $token = $response->json();
            Log::info('tumblr token: ' . $token['access_token']);
            return $token;
        } else {
            Log::error(
                'tumblr failed to refresh a token: ' .
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
        Log::info('tumblr get user info: ' . $access_token);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get('https://api.tumblr.com/v2/user/info');

        if ($response->successful()) {
            $user = $response->json('response')['user'];

            if (in_array($user['name'], config('sqwh.tumblr.users'))) {
                Log::info('tumblr user: ' . $user['name']);
                return $user;
            } else {
                Log::error('tumblr unregistered account: ' . $user['name']);
                return null;
            }
        } else {
            Log::error(
                'tumblr failed to verify account credentials: ' .
                    json_encode($response->json())
            );
            return null;
        }
    }
}
