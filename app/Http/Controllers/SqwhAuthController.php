<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SqwhAuthController extends Controller
{
    /**
     * Handle login.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (config('sqwh.auth_provider') === 'doku') {
            return redirect(config('sqwh.doku.login_url'));
        } else if (config('sqwh.auth_provider') === 'mstdn') {
            return redirect(
                config('sqwh.mstdn.server') . '/oauth/authorize' .
                    '?response_type=code&client_id=' . config('sqwh.mstdn.client_key') .
                    '&redirect_uri=' . route('auth.mastodon') .
                    '&scope=read write' .
                    '&force_login=true' .
                    '&lang=' . config('app.locale')
            );
        } else if (config('sqwh.auth_provider') === 'tumblr') {
            $state = hash('sha256', config('app.key') . (new DateTime())->format(DateTime::ATOM));
            $_SESSION['tumblr'] = $state;
            return redirect(
                'https://www.tumblr.com/oauth2/authorize' .
                    '?response_type=code&client_id=' . config('sqwh.tumblr.consumer_key') .
                    '&redirect_uri=' . route('auth.tumblr') .
                    '&scope=basic' .
                    '&state=' . $state
            );
        } else if (config('sqwh.auth_provider') === 'test') {
            return redirect('/');
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
            $response = Http::asForm()->post(config('sqwh.mstdn.server') . '/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('sqwh.mstdn.client_key'),
                'client_secret' => config('sqwh.mstdn.client_secret'),
                'redirect_uri' => route('auth.mastodon'),
                'scope' => 'read write',
            ]);

            if (!$response->successful()) {
                Log::error(
                    'mastodon failed to obtain a token: ' .
                        json_encode($response->json())
                );
                unset($_SESSION['mstdn']);
                return view('auth.failed');
            }

            $token = $response->json('access_token');
            Log::info('mastodon token: ' . $token);

            // verify account credentials
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get(config('sqwh.mstdn.server') . '/api/v1/accounts/verify_credentials');

            if (!$response->successful()) {
                Log::error(
                    'mastodon failed to verify account credentials: ' .
                        json_encode($response->json())
                );
                unset($_SESSION['mstdn']);
                return view('auth.failed');
            }

            $user = $response->json('username');
            $url = $response->json('url');
            if (
                !in_array($user, config('sqwh.mstdn.users')) &&
                $url !== config('sqwh.mstdn.server') . '/@' . $user
            ) {
                Log::error('mastodon user is invalid: ' . $user);
                unset($_SESSION['mstdn']);
                return view('auth.failed');
            }

            Log::info('mastodon user: ' . $user);
            $_SESSION['mstdn'] = json_encode($response->json());

            return redirect('/');
        } else {
            return view('auth.failed');
        }
    }

    /**
     * Handle redirection from the OAuth 2 response from Tumblr.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function tumblr(Request $request)
    {
        // API: https://www.tumblr.com/docs/en/api/v2#authentication
        if ($request->has('code')) {
            // handle authorization
            if (!$request->has('state')) {
                Log::error('tumblr failed to obtain a state');
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $state = $request->query('state');

            if (!(isset($_SESSION['tumblr']) && $_SESSION['tumblr'] === $state)) {
                Log::error('tumblr invalid state');
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $code = $request->query('code');
            Log::info('tumblr code: ' . $code);

            // obtain a token
            $response = Http::asForm()->post('https://api.tumblr.com/v2/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('sqwh.tumblr.consumer_key'),
                'client_secret' => config('sqwh.tumblr.consumer_secret'),
                'redirect_uri' => route('auth.tumblr'),
            ]);

            if (!$response->successful()) {
                Log::error(
                    'tumblr failed to obtain a token: ' .
                        json_encode($response->json())
                );
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $token = $response->json('access_token');
            Log::info('tumblr token: ' . $token);

            // verify account credentials
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('https://api.tumblr.com/v2/user/info');

            if (!$response->successful()) {
                Log::error(
                    'tumblr failed to verify account credentials: ' .
                        json_encode($response->json())
                );
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $user = $response->json('user')->name;
            if (!in_array($user, config('sqwh.tumblr.users'))) {
                Log::error('tumblr user is invalid: ' . $user);
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            Log::info('tumblr user: ' . $user);
            $_SESSION['tumblr'] = json_encode($response->json());

            return redirect('/');
        } else {
            return view('auth.failed');
        }
    }
}
