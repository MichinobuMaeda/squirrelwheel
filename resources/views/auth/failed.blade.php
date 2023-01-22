<x-layout>
    <x-slot:title>{{ ucfirst(__('error')) }}</x-slot:title>
    <h2>{{ ucfirst(__('error')) }}</h2>
    <div class="alert alert-error shadow-lg">
        <div>
            {{ ucfirst(__('authentication failed.')) }}
        </div>
        <div class="flex-none">
            <a class="btn btn-secondary" href="{{ route('login') }}">
                {{ ucfirst(__('retry')) }}
            </a>
        </div>
    </div>
</x-layout>
