<x-layout>
    <x-slot:title>{{ ucfirst($category->id ? __('edit categories') :  __('create categories')) }}</x-slot>
    <h2>{{ ucfirst($category->id ? __('edit categories') :  __('create categories'))}}</h2>
    <x-form-errors />
    @if ($category->id)
    <form action="{{ route('categories.update', ['category' => $category]) }}" method="POST">
        @method('PUT')
    @else
    <form action="{{ route('categories.store') }}" method="POST">
    @endif
        @csrf
        <div class="item">
            <label for="id" >{{ ucfirst(__('ID')) }}:</label>
            @if ($category->id)
            <span class="value">{{ $category->id }}</span>
            <input type="hidden" id="id" name="id" value="{{ $category->id }}">
            @else
            <input type="text" id="id" name="id" placeholder="{{ ucfirst(__('ID')) }}" value="{{ old('id') ?: $category->id }}" required>
            @endif
        </div>
        <div class="item">
            <label for="name" >{{ ucfirst(__('name')) }}:</label>
            <input type="text" id="name" name="name" placeholder="{{ ucfirst(__('name')) }}" value="{{ old('name') ?: $category->name }}" required>
        </div>
        <div class="item">
            <label for="update_only" >{{ ucfirst(__('target')) }}:</label>
            <select id="update_only" name="update_only" required>
                <option value="0" {{ old('update_only') === "0" || $category->update_only ? '' : 'selected' }}>{{ ucfirst(__('article')) }}</option>
                <option value="1" {{ old('update_only') === "1" || $category->update_only ? 'selected' : '' }}>{{ ucfirst(__('update only')) }}</option>
            </select>
        </div>
        <div class="item">
            <label for="priority" >{{ ucfirst(__('priority')) }}:</label>
            <select id="priority" name="priority" required>
                @for ($i = 0; $i < 10; $i++)
                <option value="{{ $i }}" {{ old('priority') === strval($i) || $category->priority === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="item">
            <label for="checked_at" >{{ ucfirst(__('checked at')) }}:</label>
            <input type="datetime-local" id="checked_at" name="checked_at" value="{{ old('checked_at') ?: $category->checked_at }}" step="1" required>
        </div>
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{route('categories.index')}}">
                {{ ucfirst(__('stop')) }}
            </a>
            <button class="btn btn-primary" type="submit">
                {{ ucfirst(__('save')) }}
            </button>
        </div>
    </form>
</x-layout>
