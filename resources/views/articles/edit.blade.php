<x-layout>
    <x-slot:title>{{ ucfirst(isset($article) ? __('edit articles') :  __('create articles')) }}</x-slot:title>
    <h2>{{ ucfirst(isset($article) ? __('edit articles') :  __('create articles'))}}</h2>
    <x-form-errors />
    @if (isset($article))
    <form action="{{ route('articles.update', ['article' => $article]) }}" method="POST">
        @method('PUT')
    @else
    <form action="{{ route('articles.store') }}" method="POST">
    @endif
        @csrf
        <div class="item">
            <label for="id" >{{ ucfirst(__('ID')) }}:</label>
            <span class="value">{{ isset($article) ? $article->id : ucfirst(__('auto number')) }}</span>
            @if (isset($article))
            <input type="hidden" id="id" name="id" value="{{ $article->id }}">
            @endif
        </div>
        @unless (isset($article))
        <div class="item">
            <label for="template_id" >{{ ucfirst(__('template')) }}:</label>
            <select id="template_id" name="template_id" required>
                @foreach ($templates as $template)
                <option value="{{ $template->id }}" {{ old('template_id') === $template->id || (isset($article) ? $article->template_id : 2) === $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                @endforeach
            </select>
        </div>
        @endunless
        <div class="item">
            <label for="content" >{{ ucfirst(__('content')) }}:</label>
            <textarea id="content" name="content" placeholder="{{ ucfirst(__('content')) }}" required rows="5" oninput="updateContentLength()">{{ old('content') ?: (isset($article) ? $article->content : '') }}</textarea>
        </div>
        <div class="item">
            <label for="link" >{{ ucfirst(__('link')) }}:</label>
            <input type="url" id="link" name="link" placeholder="{{ ucfirst(__('link')) }}" value="{{ old('link') ?: (isset($article) ? $article->link : '') }}" oninput="updateContentLength()">
        </div>
        <div class="item">
            <label for="contentLength" >{{ ucfirst(__('text length')) }}:</label>
            <div id="contentLength" class="textLength"></div>
        </div>
        <div class="item">
            <label>{{ ucfirst(__('target')) }}:</label>
            <div id="target" class="options">
                @foreach (config('sqwh.post_targets') as $target)
                <label for="post_target_{{ $target }}">
                    <input type="checkbox" id="post_target_{{ $target }}" name="post_targets[]" value="{{ $target }}" {{ (!isset($article) || in_array($target, old('post_targets') ?: $article->post_targets)) ? 'checked' : '' }}/>
                    {{ getMediaName($target) }}
                </label>
                @endforeach
            </div>
        </div>
        @if (isset($article))
        <div class="item">
            <label for="priority" >{{ ucfirst(__('priority')) }}:</label>
            <x-select-priority :value="$article->priority" />
        </div>
        @endif
        <div class="item">
            <label for="reserved_at" >{{ ucfirst(__('reserved at')) }}:</label>
            <input type="datetime-local" id="reserved_at" name="reserved_at" value="{{ old('reserved_at') ?: (isset($article) ? $article->reserved_at : (new DateTime)->format(DATETIME_LOCAL)) }}" step="1" required>
        </div>
        <div class="form-actions">
            <a class="btn btn-secondary" href="{{route('articles.index')}}">
                {{ ucfirst(__('stop')) }}
            </a>
            <button class="btn btn-primary" type="submit">
                {{ ucfirst(__('save')) }}
            </button>
        </div>
    </form>
    <script>
function updateContentLength() {
    content = document.getElementById('content')
    link = document.getElementById('link')
    trg = document.getElementById('contentLength')
    trg.innerHTML = content.value.length + (link.value ? 24 : 0)

}
updateContentLength()
    </script>
</x-layout>
