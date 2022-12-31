<div>
@foreach (explode("\n", $text) as $line)
    @if (trim($line))
    <div>{{ $line }}</div>
    @else
    <br>
    @endif
@endforeach
</div>
