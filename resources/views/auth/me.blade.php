<x-layout>
    <x-slot:title>{{ ucfirst(__('account')) }}</x-slot:title>
    <h2>{{ ucfirst(__('account')) }}</h2>
    <div class="item">
        <label>{{ ucfirst(__('auth. provider')) }}:</label>
        @if (config('sw.auth_provider') === 'doku')
        <span class="value sm-1/2">{{ preg_replace('/\?.*/', '', config('sw.doku.login_url')) }}</span>
        @elseif (config('sw.auth_provider') === 'mstdn')
        <span class="value sm-1/2">{{ config('sw.mstdn.server') }}</span>
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
    @if (config('sw.auth_provider') === 'mstdn')
    <div class="mt-4">
        <a class="btn btn-secondary" href="{{ route('login') }}">
            {{ ucfirst(__('logout')) }}
        </a>
    </div>
    @endif
</x-layout>
