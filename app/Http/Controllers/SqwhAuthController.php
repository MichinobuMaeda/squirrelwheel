<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\MastodonApiRepository;
use App\Repositories\TumblrApiRepository;

class SqwhAuthController extends Controller
{
    /**
     * The mastodon API repository implementation.
     *
     * @var MastodonApiRepository
     */
    protected $mstdnApi;

    /**
     * The tunblr API repository implementation.
     *
     * @var TumblrApiRepository
     */
    protected $tumblrApi;

    /**
     * Create a new controller instance.
     *
     * @param  MastodonApiRepository  $mstdnApi
     * @param  TumblrApiRepository  $tumblrApi
     * @return void
     */
    public function __construct(
        MastodonApiRepository  $mstdnApi,
        TumblrApiRepository $tumblrApi,
    ) {
        $this->mstdnApi = $mstdnApi;
        $this->tumblrApi = $tumblrApi;
    }

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
            return redirect($this->mstdnApi->getRedirectUrl());
        } else if (config('sqwh.auth_provider') === 'tumblr') {
            $state = hash('sha256', config('app.key') . (new DateTime())->format(DateTime::ATOM));
            $_SESSION['tumblr'] = $state;
            return redirect($this->tumblrApi->getRedirectUrl($state));
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
            $token = $this->mstdnApi->getToken($code);

            if (!$token) {
                unset($_SESSION['mstdn']);
                return view('auth.failed');
            }

            // verify account credentials
            $user = $this->mstdnApi->getUserInfo($token['access_token']);

            if (!$user) {
                unset($_SESSION['mstdn']);
                return view('auth.failed');
            }

            $_SESSION['mstdn'] = json_encode([
                'token' => $token,
                'user' => $user,
                'refreshed_at' => time(),
            ]);

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

            // validate state
            $state = $request->query('state');

            if (!(isset($_SESSION['tumblr']) && $_SESSION['tumblr'] === $state)) {
                Log::error('tumblr invalid state');
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $code = $request->query('code');
            Log::info('tumblr code: ' . $code);

            // obtain a token
            $token = $this->tumblrApi->getToken($code);

            if (!$token) {
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            // verify account credentials
            $user = $this->tumblrApi->getUserInfo($token['access_token']);

            if (!$user) {
                unset($_SESSION['tumblr']);
                return view('auth.failed');
            }

            $_SESSION['tumblr'] = json_encode([
                'token' => $token,
                'user' => $user,
                'refreshed_at' => time(),
            ]);

            return redirect('/');
        } else {
            return view('auth.failed');
        }
    }
}
