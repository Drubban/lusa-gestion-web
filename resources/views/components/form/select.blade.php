@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'placeholder' => 'Selecciona...', 'hint' => null, 'required' => false])
@php
    $clave = str_replace(['[', ']'], ['.', ''], $name);
    $id = $attributes->get('id', str_replace(['[', ']', '.'], '_', $name));
    $actual = old($clave, $selected instanceof \BackedEnum ? $selected->value : $selected);
@endphp
<div class="{{ $attributes->get('wrapper', 'mb-3') }}">
    @if ($label)<label class="form-label" for="{{ $id }}">{{ $label }}@if($required) <span class="text-danger">*</span>@endif</label>@endif
    <select id="{{ $id }}" name="{{ $name }}" @if($required) required @endif
        {{ $attributes->except(['wrapper', 'id'])->class(['form-select', 'is-invalid' => $errors->has($clave)]) }}>
        @if ($placeholder !== false)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $valor => $texto)
            <option value="{{ $valor }}" @selected((string) $actual === (string) $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error($clave)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($hint)<div class="form-text">{{ $hint }}</div>@endif
</div>
