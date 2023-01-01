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
            <span class="value">{{ $category->id ?: ucfirst(__('auto number')) }}</span>
            @if ($category->id)
            <input type="hidden" id="id" name="id" value="{{ $category->id }}">
            @endif
        </div>
        <div class="item">
            <label for="name" >{{ ucfirst(__('name')) }}:</label>
            <input type="text" id="name" name="name" placeholder="{{ ucfirst(__('name')) }}" value="{{ old('name') ?: $category->name }}" required>
        </div>
        <div class="item">
            <label for="feed" >{{ ucfirst(__('feed')) }}:</label>
            <input type="url" id="feed" name="feed" placeholder="{{ ucfirst(__('feed')) }}" value="{{ old('feed') ?: $category->feed }}">
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
            <x-select-priority :value="$category->priority" />
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
