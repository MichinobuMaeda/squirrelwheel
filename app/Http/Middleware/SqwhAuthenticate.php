<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SqwhAuthenticate
{
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
        if (!isset($_SESSION)) {
            session_start();
        }

        $mstdnUser = isset($_SESSION['mstdn']) ? $_SESSION['mstdn'] : null;
        if (!$mstdnUser) {
            return null;
        }

        $mstdn = json_decode($mstdnUser);
        Log::info('mastodon id: ' . $mstdn->id);

        return User::make([
            'name' => $mstdn->username,
            'email' => 'unknown',
            'client_id' => $mstdn->id,
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
        if (!isset($_SESSION)) {
            session_start();
        }

        $mstdnUser = isset($_SESSION['tumblr']) ? $_SESSION['tumblr'] : null;
        if (!$mstdnUser) {
            return null;
        }

        $tumblr = json_decode($mstdnUser);
        Log::info('tumblr name: ' . $tumblr->name);

        return User::make([
            'name' => $tumblr->name,
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
