<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SwAuthenticate
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
        $user = $this->getDokuUser();

        if ($user) {
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
        $clientId = null;
        $name = null;
        $email = null;
        $scopes = null;

        if (config('app.env') === 'local') {

            $clientId = 'test_user_id';
            $name = 'User Name';
            $email = 'user@example.com';
            $scopes = 'admin,user';
        } else {

            session_name("DokuWiki");
            if (!isset($_SESSION)) {
                session_start();
            }
            $dokuRel = config('sw.doku.base_path');
            $dokuCookie = 'DW' . md5($dokuRel . ((1 /*$conf['securecookie']*/) ? $_SERVER['SERVER_PORT'] : ''));
            $clientId = $_SESSION[$dokuCookie]['auth']['user'];
            $name = $_SESSION[$dokuCookie]['auth']['info']['name'];
            $email = $_SESSION[$dokuCookie]['auth']['info']['mail'];
            $dokuGroups = $_SESSION[$dokuCookie]['auth']['info']['grps'];

            if ($clientId && $name && $email && $dokuGroups) {
                foreach (config('sw.doku.groups') as $group) {
                    if (in_array($group, $dokuGroups)) {
                        $scopes = $scopes === null ? $group : ($scopes . ',' . $group);
                    }
                }
            }
        }

        return $scopes ? User::make([
            'name' => $name,
            'email' => $email,
            'client_id' => $clientId,
            'scopes' => $scopes,
        ]) : null;
    }
}
