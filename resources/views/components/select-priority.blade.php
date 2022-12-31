<select id="{{ $name }}" name="{{ $name }}" required class="{{ $class }}">
    @for ($i = 0; $i < 10; $i++)
    <option value="{{ $i }}" {{ old($name) === strval($i) || $value === $i ? 'selected' : '' }}>{{ $i }}</option>
    @endfor
</select>
