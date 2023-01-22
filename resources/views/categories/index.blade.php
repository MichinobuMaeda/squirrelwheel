<x-layout>
    <x-slot:title>{{ ucfirst(__('list categories')) }}</x-slot:title>
    <a class="btn-create" href="{{ route('categories.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list categories')) }}</h2>
    @forelse ($categories as $category)
    <div class="card {{ $category->deleted_at ? 'card-disabled' : '' }}">
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
                <label>{{ ucfirst(__('feed')) }}:</label>
                <span class="value">{{ $category->feed }}</span>
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
                <span class="value">{{ $category->checked_at }}</span>
            </div>
        </div>
        <x-item-actions route="categories" item="category" :model="$category" />
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
