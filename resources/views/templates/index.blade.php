<x-layout>
    <x-slot:title>{{ ucfirst(__('list templates')) }}</x-slot:title>
    <a class="btn-create" href="{{ route('templates.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list templates')) }}</h2>
    @forelse ($templates as $template)
    <div class="card {{ $template->deleted_at ? 'card-disabled' : '' }}">
        <div class="card-body">
            <div class="item">
                <label>{{ ucfirst(__('ID')) }}:</label>
                <span class="value">{{ $template->id }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('category')) }}:</label>
                <span class="value">{{ $template->category->name }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('name')) }}:</label>
                <span class="value">{{ $template->name }}</span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('body')) }}:</label>
                <span class="value-multi-line"><x-multi-line :text="$template->body" /></span>
            </div>
            <div class="item">
                <label>{{ ucfirst(__('used at')) }}:</label>
                <span class="value">{{ $template->used_at }}</span>
            </div>
        </div>
        <x-item-actions route="templates" item="template" :model="$template" />
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
