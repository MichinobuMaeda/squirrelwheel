<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
        <link rel="manifest" href="/webmanifest">
        <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <title>{{ $title }} - {{ config('app.name') }}</title>
    </head>
    <body>
        <div class="header">
            <div class="title">
                <img class="logo" src="/images/android-chrome-192x192.png" alt="logo">
                <h1>
                    {{ config('app.name') }}
                </h1>
            </div>
            <ul class="menu">
                <li><a href="{{route('articles.index')}}">{{ucfirst(__('articles'))}}</a></li>
                <li><a href="{{route('templates.index')}}">{{ucfirst(__('templates'))}}</a></li>
                <li><a href="{{route('categories.index')}}">{{ucfirst(__('categories'))}}</a></li>
            </ul>
        </div>
        <div class="content">
{{ $slot }}
        </div>
        <div class="footer">
            &copy; 2022-2023
            <a href="https://computer-union.jp">Conputer union</a>
            -
            <a href="https://github.com/MichinobuMaeda/squirrelwheel">GitHub</a>
        </div>
    </body>
</html>
