<!DOCTYPE html>
<html lang="ja" data-theme="cupcake">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
        <link rel="manifest" href="/webmanifest">
        <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} - {{ config('app.name') }}</title>
    </head>
    <body>
        <div class="navbar">
            <h1>
                <img src="{{ url('/images/android-chrome-192x192.png') }}" alt="logo">
                {{ config('app.name') }}
            </h1>
        </div>
        <div class="main-menu">
            <div class="tabs">
                <a class="tab" href="{{route('articles.index')}}">{{ ucfirst(__('articles')) }}</a>
                <a class="tab" href="{{route('templates.index')}}">{{ ucfirst(__('templates')) }}</a>
                <a class="tab" href="{{route('categories.index')}}">{{ ucfirst(__('categories')) }}</a>
            </div>
        </div>
        <div class="content">
{{ $slot }}
        </div>
        <div class="footer">
            <div class="copyright">
                <p>
                    &copy; 2022-2023
                    <a class="link" href="https://computer-union.jp">Conputer union</a>
                    -
                    <a class="link" href="https://github.com/MichinobuMaeda/squirrelwheel">GitHub</a>
                </p>
            </div>
        </div>
    </body>
</html>
