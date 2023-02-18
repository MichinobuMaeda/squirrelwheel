<x-layout>
    <x-slot:title>{{ ucfirst(__('list articles')) }}</x-slot:title>
    <a class="btn-create" href="{{ route('articles.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list articles')) }}</h2>
    @forelse ($articles as $article)
    <div class="card {{ $article->deleted_at ? 'card-disabled' : '' }}">
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
            <div class="item">
                <label>{{ ucfirst(__('link')) }}:</label>
                <span class="value">{{ $article->link }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('text length')) }}:</label>
                <span class="value">{{ strlen($article->content) + ($article->link ? 24 : 0) }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('target')) }}:</label>
                <span class="value">
                    @foreach (config('sqwh.post_targets') as $target)
                        {{ in_array($target, $article->post_targets) ? getMediaName($target) : '' }}
                    @endforeach
                </span>
            </div>
<!--
            <div class="item">
                <label>{{ ucfirst(__('queued at')) }}:</label>
                <span class="value">{{ $article->queued_at }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('posted at')) }}:</label>
                <span class="value">{{ $article->posted_at }}</span>
            </div>
-->
            <div class="item">
                <label>{{ ucfirst(__('reserved at')) }}:</label>
                <span class="value">{{ $article->reserved_at }}</span>
            </div>
        </div>
        <x-item-actions route="articles" item="article" :model="$article" />
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
