<x-layout>
    <x-slot:title>{{ucfirst(__('list templates'))}}</x-slot>
    <h2>{{ucfirst(__('list templates'))}}</h2>
    @forelse ($templates as $template)
    <div class="card">
        <div class="item">
            <span class="name">{{ucfirst(__('ID'))}}:</span>
            <span class="value">{{ $template->id }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('name'))}}:</span>
            <span class="value">{{ $template->name }}</span>
        </div>
        <div class="item">
            <span class="name">{{ucfirst(__('body'))}}:</span>
            <span class="multiline-value">
                <pre>{{ $template->body }}</pre>
            </span>
        </div>
    </div>
    @empty
    <p>{{ucfirst(__('no data to list.'))}}</p>
    @endforelse
</x-layout>
