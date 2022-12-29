<x-layout>
    <x-slot:title>{{ucfirst(__('list articles'))}}</x-slot>
    <h2>{{ucfirst(__('list articles'))}}</h2>
    @forelse ($articles as $article)
    <div class="card">
        <div class="item">
            <span class="name">{{ucfirst(__('ID'))}}:</span>
            <span class="value">{{ $article->id }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('priority'))}}:</span>
            <span class="value">{{ $article->priority }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('reserved at'))}}:</span>
            <span class="value">{{ $article->reserved_at->setTimezone(config('app.timezone')) }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('content'))}}:</span>
            <span class="multiline-value">
                <pre>{{ $article->body }}</pre>
            </span>
        </div>
    </div>
    @empty
    <p>{{ucfirst(__('no data to list.'))}}</p>
    @endforelse
</x-layout>
