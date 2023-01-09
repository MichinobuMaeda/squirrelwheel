<div class="card-actions">
    @if ($model->deleted_at)
    <button class="btn btn-accent" type="submit" form="enable{{ $model->id }}">
        {{ ucfirst(__('enable')) }}
    </button>
    <button class="btn btn-error" type="submit" form="delete{{ $model->id }}">
        {{ ucfirst(__('delete permanently')) }}
    </button>
    @else
    <button class="btn btn-accent" type="submit" form="disable{{ $model->id }}">
        {{ ucfirst(__('disable')) }}
    </button>
    <a class="btn btn-primary" href="{{ route($route . '.edit', [$item => $model]) }}">
        {{ ucfirst(__('edit')) }}
    </a>
    @endif
    <form
        id="delete{{ $model->id }}"
        style="display: none;"
        action="{{ route($route . '.destroy', [$item => $model]) }}"
        method="POST"
    >
        @method('DELETE')
        @csrf
    </form>
    <form
        id="enable{{ $model->id }}"
        style="display: none;"
        action="{{ route($route . '.enable', [$item => $model]) }}"
        method="POST"
    >
        @method('PUT')
        @csrf
    </form>
    <form
        id="disable{{ $model->id }}"
        style="display: none;"
        action="{{ route($route . '.disable', [$item => $model]) }}"
        method="POST"
    >
        @method('PUT')
        @csrf
    </form>
</div>
