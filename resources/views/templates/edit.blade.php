<x-layout>
    <x-slot:title>{{ ucfirst($template->id ? __('edit templates') :  __('create templates')) }}</x-slot>
    <h2>{{ ucfirst($template->id ? __('edit templates') :  __('create templates'))}}</h2>
    <ul>
    @foreach ($errors->all() as $error)
        <li class="error">{{ $error }}</li>
    @endforeach
    </ul>
    @if ($template->id)
    <form action="{{ route('templates.update', ['template' => $template]) }}" method="POST">
    @method('PUT')
    @else
    <form action="{{ route('templates.store') }}" method="POST">
    @endif
        @csrf
        <div class="item">
            <label for="id" >{{ ucfirst(__('ID')) }}:</label>
            @if ($template->id)
            <span class="value">{{ $template->id }}</span>
            <input type="hidden" id="id" name="id" value="{{ $template->id }}">
            @else
            <span class="value">-</span>
            @endif
        </div>
        <div class="item">
            <label for="category_id" >{{ ucfirst(__('category')) }}:</label>
            <select id="category_id" name="category_id" required>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') === $category->id || $template->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="item">
            <label for="name" >{{ ucfirst(__('name')) }}:</label>
            <input type="text" id="name" name="name" placeholder="{{ ucfirst(__('name')) }}" value="{{ old('name') ? old('name') : $template->name }}" required>
        </div>
        <div class="item">
            <label for="body" >{{ ucfirst(__('body')) }}:</label>
            <textarea id="body" name="body" placeholder="{{ ucfirst(__('body')) }}" required>{{ old('body') ? old('body') : $template->body }}</textarea>
        </div>
        <div class="item">
            <label for="used_at" >{{ ucfirst(__('used at')) }}:</label>
            <input type="datetime-local" id="used_at" name="used_at" value="{{ old('used_at') ? old('used_at') : ($template->used_at ? $template->used_at->format('Y-m-d H:i:s') : '') }}" step="1" required>
        </div>
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{route('templates.index')}}">
                {{ ucfirst(__('stop')) }}
            </a>
            <button class="btn btn-primary" type="submit">
                {{ ucfirst(__('save')) }}
            </button>
        </div>
    </form>
</x-layout>
