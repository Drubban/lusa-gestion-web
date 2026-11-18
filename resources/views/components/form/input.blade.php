@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])
@php $id = $attributes->get('id', str_replace(['[', ']', '.'], '_', $name)); $clave = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div class="{{ $attributes->get('wrapper', 'mb-3') }}">
    @if ($label)<label class="form-label" for="{{ $id }}">{{ $label }}@if($required) <span class="text-danger">*</span>@endif</label>@endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($clave, $value) }}"
        @if($required) required @endif
        {{ $attributes->except(['wrapper', 'id'])->class(['form-control', 'is-invalid' => $errors->has($clave)]) }}>
    @error($clave)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($hint)<div class="form-text">{{ $hint }}</div>@endif
</div>
