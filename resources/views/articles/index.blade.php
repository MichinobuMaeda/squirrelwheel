<x-layout>
    <x-slot:title>{{ ucfirst(__('list articles')) }}</x-slot>
    <a class="btn-create" href="{{ route('articles.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list articles')) }}</h2>
    @forelse ($articles as $article)
    <div class="card">
        <div class="card-body">
            <div class="item">
                <label>{{ ucfirst(__('ID')) }}:</label>
                <span class="value">{{ $article->id }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('priority')) }}:</label>
                <span class="value">{{ $article->priority }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('reserved at')) }}:</label>
                <span class="value">{{ $article->reserved_at }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('content')) }}:</label>
                <span class="value-multi-line"><x-multi-line :text="$article->content" /></span>
            </div>
        </div>
        <div class="card-actions">
            <a class="btn btn-primary" href="{{ route('articles.edit', ['article' => $article]) }}">
                {{ ucfirst(__('edit')) }}
            </a>
        </div>
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
