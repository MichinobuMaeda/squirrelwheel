<x-layout>
    <x-slot:title>{{ ucfirst(__('list categories')) }}</x-slot>
    <a class="btn-create" href="{{ route('categories.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list categories')) }}</h2>
    @forelse ($categories as $category)
    <div class="card">
        <div class="card-body">
            <div class="item">
                <label>{{ ucfirst(__('ID')) }}:</label>
                <span class="value sm-1/2">{{ $category->id }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('name')) }}:</label>
                <span class="value">{{ $category->name }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('target')) }}:</label>
                @if ($category->update_only)
                <span class="value">{{ ucfirst(__('update only')) }}</span>
                @else
                <span class="value">{{ ucfirst(__('articles')) }}</span>
                @endif
            </div>
            <div class="item">
                <label>{{ ucfirst(__('priority')) }}:</label>
                <span class="value">{{ $category->priority }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('checked at')) }}:</label>
                @if ($category->checked_at)
                <span class="value">{{ $category->checked_at->setTimezone(config('app.timezone')) }}</span>
                @else
                <span class="value">-</span>
                @endif
            </div>
        </div>
        <div class="card-actions">
            <a class="btn btn-primary" href="{{ route('categories.edit', ['category' => $category]) }}">
                {{ ucfirst(__('edit')) }}
            </a>
        </div>
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
