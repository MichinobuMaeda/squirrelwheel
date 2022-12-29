<x-layout>
    <x-slot:title>{{ucfirst(__('list categories'))}}</x-slot>
    <h2>{{ucfirst(__('list categories'))}}</h2>
    @forelse ($categories as $category)
    <div class="card">
        <div class="item">
            <span class="name">{{ucfirst(__('ID'))}}:</span>
            <span class="value">{{ $category->id }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('name'))}}:</span>
            <span class="value">{{ $category->name }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('target'))}}:</span>
            @if ($category->update_only)
            <span class="value">{{ucfirst(__('update only'))}}</span>
            @else
            <span class="value">{{ucfirst(__('articles'))}}</span>
            @endif
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('priority'))}}:</span>
            <span class="value">{{ $category->priority }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('checked at'))}}:</span>
            @if ($category->checked_at)
            <span class="value">{{ $category->checked_at->setTimezone(config('app.timezone')) }}</span>
            @else
            <span class="value">-</span>
            @endif
        </div>
    </div>
    @empty
    <p>{{ucfirst(__('no data to list.'))}}</p>
    @endforelse
</x-layout>
