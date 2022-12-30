<x-layout>
    <x-slot:title>{{ ucfirst(__('list templates')) }}</x-slot>
    <a class="btn-create" href="{{ route('templates.create') }}">
        {{ ucfirst(__('create')) }}
    </a>
    <h2>{{ ucfirst(__('list templates')) }}</h2>
    @forelse ($templates as $template)
    <div class="card">
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
                <span class="value">
                    <pre>{{ $template->body }}</pre>
                </span>
            </div>
        </div>
        <div class="card-actions">
            <a class="btn btn-primary" href="{{ route('templates.edit', ['template' => $template]) }}">
                {{ ucfirst(__('edit')) }}
            </a>
        </div>
    </div>
    @empty
    <div class="nodata">{{ ucfirst(__('no data to list.')) }}</div>
    @endforelse
</x-layout>
