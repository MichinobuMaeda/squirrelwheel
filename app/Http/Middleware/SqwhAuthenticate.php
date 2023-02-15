<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Repositories\TumblrApiRepository;

class SqwhAuthenticate
{
    /**
     * The tunblr API repository implementation.
     *
     * @var TumblrApiRepository
     */
    protected $tumblrApi;

    /**
     * Create a new controller instance.
     *
     * @param  TumblrApiRepository  $tumblrApi
     * @return void
     */
    public function __construct(TumblrApiRepository $tumblrApi)
    {
        $this->tumblrApi = $tumblrApi;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('sqwh.auth_provider') === 'doku') {
            $user = $this->getDokuUser();
        } else if (config('sqwh.auth_provider') === 'mstdn') {
            $user = $this->getMstdnUser();
        } else if (config('sqwh.auth_provider') === 'tumblr') {
            $user = $this->getTumblr();
        } else if (config('sqwh.auth_provider') === 'test') {
            $user = $this->getTestUser();
        }

        if (isset($user) && $user) {
            Auth::login($user);
        }

        return $next($request);
    }

    /**
     * Get DokuWiki user from session.
     *
     * @return \App\Models\User
     */
    public function getDokuUser()
    {
        try {
            session_name("DokuWiki");
            session_set_cookie_params(config('sqwh.auth_session_life_time'));
            if (!isset($_SESSION)) {
                session_start();
            }
            $dokuRel = config('sqwh.doku.base_path');
            $dokuCookie = 'DW' . md5($dokuRel . ((1 /*$conf['securecookie']*/) ? $_SERVER['SERVER_PORT'] : ''));
            $clientId = $_SESSION[$dokuCookie]['auth']['user'];
            $name = $_SESSION[$dokuCookie]['auth']['info']['name'];
            $email = $_SESSION[$dokuCookie]['auth']['info']['mail'];
            $dokuGroups = $_SESSION[$dokuCookie]['auth']['info']['grps'];

            if ($clientId && $name && $email && $dokuGroups) {
                foreach (config('sqwh.doku.groups') as $group) {
                    if (in_array($group, $dokuGroups)) {
                        $scopes = isset($scopes) ? ($scopes . ',' . $group) : $group;
                    }
                }
            }

            return $scopes ? User::make([
                'name' => $name,
                'email' => $email,
                'client_id' => $clientId,
                'scopes' => $scopes,
            ]) : null;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Get Mastodon user from session.
     *
     * @return \App\Models\User
     */
    public function getMstdnUser()
    {
        session_name("SWMSTDN");
        session_set_cookie_params(config('sqwh.auth_session_life_time'));
        if (!isset($_SESSION)) {
            session_start();
        }

        $json = isset($_SESSION['mstdn']) ? $_SESSION['mstdn'] : null;
        if (!$json) {
            return null;
        }

        $info = json_decode($json);
        Log::info('mastodon id: ' . $info->user->id);

        return User::make([
            'name' => $info->user->username,
            'email' => 'unknown',
            'client_id' => $info->user->id,
            'scopes' => 'read write',
        ]);
    }

    /**
     * Get Tumblr user from session.
     *
     * @return \App\Models\User
     */
    public function getTumblr()
    {
        session_name("SWTUMBLR");
        session_set_cookie_params(config('sqwh.auth_session_life_time'));
        if (!isset($_SESSION)) {
            session_start();
        }

        $json = isset($_SESSION['tumblr']) ? $_SESSION['tumblr'] : null;
        if (!$json || !str_starts_with($json, '{')) {
            return null;
        }

        $info = json_decode($json, true);
        Log::info('tumblr name: ' . $info['user']['name']);

        $ts = time();
        if (($ts - $info['refreshed_at']) > config('sqwh.auth_session_refresh_time')) {

            // Refresh token grant
            if (!isset($info['token']['refresh_token'])) {
                unset($_SESSION['tumblr']);
                return null;
            }

            $token = $this->tumblrApi->refreshToken($info['token']['refresh_token']);

            if (!$token) {
                unset($_SESSION['tumblr']);
                return null;
            }

            // verify account credentials
            $user = $this->tumblrApi->getUserInfo($token['access_token']);

            if (!$user) {
                unset($_SESSION['tumblr']);
                return null;
            }

            Log::info('tumblr user: ' . $user['name']);
            $info = [
                'token' => $token,
                'user' => $user,
                'refreshed_at' => time(),
            ];

            $_SESSION['tumblr'] = json_encode($info);
        }

        return User::make([
            'name' => $info['user']['name'],
            'email' => 'unknown',
            'client_id' => 'unknown',
            'scopes' => 'read write',
        ]);
    }

    /**
     * Get test user from session.
     *
     * @return \App\Models\User
     */
    public function getTestUser()
    {
        return (config('app.env') === 'local' || config('app.env') === 'test')
            ? User::make([
                'name' => 'User Name',
                'email' => 'user@example.com',
                'client_id' => 'test_user_id',
                'scopes' => 'read write',
            ])
            : null;
    }
}
