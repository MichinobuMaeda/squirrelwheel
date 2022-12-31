<ul>
@foreach ($errors->all() as $error)
    <li class="form-error">{{ $error }}</li>
@endforeach
</ul>
