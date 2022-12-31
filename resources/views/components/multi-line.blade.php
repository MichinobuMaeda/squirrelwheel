<div>
@foreach (explode("\n", $text) as $line)
    <div>{{ $line }}</div>
@endforeach
</div>
