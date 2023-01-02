<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DokuAuthenticate
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
        // テスト環境
        if (!config('doku.login_url')) {
            $request->merge([
                'login_user_id' => 'test_user_id',
                'login_user_name' => 'User Name',
                'login_user_email' => 'user@example.com',
            ]);

            return $next($request);
        }

        // DokuWiki のアカウントの取得
        session_name("DokuWiki");
        if(!isset($_SESSION)) {
            session_start();
        }
        $dokuRel = config('doku.base_path');
        $dokuCookie = 'DW'.md5($dokuRel.((1 /*$conf['securecookie']*/) ? $_SERVER['SERVER_PORT'] : ''));
        $dokuUser = $_SESSION[$dokuCookie]['auth']['user'];
        $dokuName = $_SESSION[$dokuCookie]['auth']['info']['name'];
        $dokuEmail = $_SESSION[$dokuCookie]['auth']['info']['mail'];
        $dokuGroups = $_SESSION[$dokuCookie]['auth']['info']['grps'];

        // DokuWiki のグループの確認
        if ($dokuUser && $dokuName && $dokuEmail && $dokuGroups) {
            foreach (config('doku.groups') as $group) {
                if (in_array($group, $dokuGroups)) {
                    $request->merge([
                        'login_user_id' => $dokuUser,
                        'login_user_name' => $dokuName,
                        'login_user_email' => $dokuEmail,
                    ]);

                    return $next($request);
                }
            }
        }

        return redirect(config('doku.login_url'));
    }
}
