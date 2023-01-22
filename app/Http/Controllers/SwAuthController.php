<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SwAuthController extends Controller
{
    /**
     * Handle login.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (config('sw.auth_provider') === 'doku') {
            return redirect(config('sw.doku.login_url'));
        } else if (config('sw.auth_provider') === 'mstdn') {
            return redirect(
                config('sw.mstdn.server') . '/oauth/authorize' .
                    '?response_type=code&client_id=' . config('sw.mstdn.client_key') .
                    // '&redirect_uri=urn:ietf:wg:oauth:2.0:oob' .
                    '&redirect_uri=' . route('auth.mastodon') .
                    '&scope=read write' .
                    '&force_login=true' .
                    '&lang=' . config('app.locale')
            );
        } else {
            return view('auth.failed');
        }
    }

    /**
     * Handle redirection from the OAuth response from the Mastodon server.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mastodon(Request $request)
    {
        // API: https://docs.joinmastodon.org/methods/oauth/#token
        if ($request->has('code')) {
            // handle authorization
            $code = $request->query('code');
            Log::info('mastodon code: ' . $code);

            // obtain a token
            $response = Http::asForm()->post(config('sw.mstdn.server') . '/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('sw.mstdn.client_key'),
                'client_secret' => config('sw.mstdn.client_secret'),
                'redirect_uri' => route('auth.mastodon'),
                'scope' => 'read write',
            ]);

            if (!$response->successful()) {
                Log::error(
                    'mastodon failed to obtain a token: ' .
                        json_encode($response->json())
                );
                $request->session()->forget('mstdn');
                return view('auth.failed');
            }

            $token = $response->json('access_token');
            Log::info('mastodon token: ' . $token);

            // verify account credentials
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get(config('sw.mstdn.server') . '/api/v1/accounts/verify_credentials');

            if (!$response->successful()) {
                Log::error(
                    'mastodon failed to verify account credentials: ' .
                        json_encode($response->json())
                );
                $request->session()->forget('mstdn');
                return view('auth.failed');
            }

            $user = $response->json('username');
            if ($user !== config('sw.mstdn.user')) {
                Log::error('mastodon user is invalid: ' . $user);
                $request->session()->forget('mstdn');
                return view('auth.failed');
            }

            Log::info('mastodon user: ' . $user);
            $mstdnCookie = 'MSTDN' . md5(route('login'));
            $_SESSION[$mstdnCookie] = json_encode($response->json());

            return redirect('/');
        } else {
            return view('auth.failed');
        }
    }
}
