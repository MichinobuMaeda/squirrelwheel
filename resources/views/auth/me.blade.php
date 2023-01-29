<x-layout>
    <x-slot:title>{{ ucfirst(__('account')) }}</x-slot:title>
    <h2>{{ ucfirst(__('account')) }}</h2>
    <div class="item">
        <label>{{ ucfirst(__('auth. provider')) }}:</label>
        @if (config('sqwh.auth_provider') === 'doku')
        <span class="value sm-1/2">{{ preg_replace('/\?.*/', '', config('sqwh.doku.login_url')) }}</span>
        @elseif (config('sqwh.auth_provider') === 'mstdn')
        <span class="value sm-1/2">{{ config('sqwh.mstdn.server') }}</span>
        @elseif (config('sqwh.auth_provider') === 'tumblr')
        <span class="value sm-1/2">Tumblr</span>
        @else
        <span class="value sm-1/2">Test</span>
        @endif
    </div>
    <div class="item">
        <label>{{ ucfirst(__('user ID')) }}:</label>
        <span class="value sm-1/2">{{ Auth::user()->client_id }}</span>
    </div>
    <div class="item">
        <label>{{ ucfirst(__('user name')) }}:</label>
        <span class="value sm-1/2">{{ Auth::user()->name }}</span>
    </div>
    <div class="item">
        <label>{{ ucfirst(__('email address')) }}:</label>
        <span class="value sm-1/2">{{ Auth::user()->email }}</span>
    </div>
    @if (config('sqwh.auth_provider') === 'mstdn' || config('sqwh.auth_provider') ==='tumblr')
    <div class="mt-4">
        <a class="btn btn-secondary" href="{{ route('login') }}">
            {{ ucfirst(__('logout')) }}
        </a>
    </div>
    @endif
</x-layout>
