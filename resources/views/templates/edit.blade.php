<x-layout>
    <x-slot:title>{{ ucfirst($template->id ? __('edit templates') :  __('create templates')) }}</x-slot:title>
    <h2>{{ ucfirst($template->id ? __('edit templates') :  __('create templates'))}}</h2>
    <x-form-errors />
    @if ($template->id)
    <form action="{{ route('templates.update', ['template' => $template]) }}" method="POST">
        @method('PUT')
    @else
    <form action="{{ route('templates.store') }}" method="POST">
    @endif
        @csrf
        <div class="item">
            <label for="id" >{{ ucfirst(__('ID')) }}:</label>
            <span class="value">{{ $template->id ?: ucfirst(__('auto number')) }}</span>
            @if ($template->id)
            <input type="hidden" id="id" name="id" value="{{ $template->id }}">
            @endif
        </div>
        <div class="item">
            <label for="category_id" >{{ ucfirst(__('category')) }}:</label>
            <select id="category_id" name="category_id" required>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') === strval($category->id) || $template->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="item">
            <label for="name" >{{ ucfirst(__('name')) }}:</label>
            <input type="text" id="name" name="name" placeholder="{{ ucfirst(__('name')) }}" value="{{ old('name') ?: $template->name }}" required>
        </div>
        <div class="item">
            <label for="body" >{{ ucfirst(__('body')) }}:</label>
            <textarea id="body" name="body" placeholder="{{ ucfirst(__('body')) }}" required rows="3">{{ old('body') ?: $template->body }}</textarea>
        </div>
        <div class="item">
            <label for="used_at" >{{ ucfirst(__('used at')) }}:</label>
            <input type="datetime-local" id="used_at" name="used_at" value="{{ old('used_at') ?: $template->used_at }}" step="1" required>
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
