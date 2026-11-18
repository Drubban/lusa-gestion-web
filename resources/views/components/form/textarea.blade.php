@props(['name', 'label' => null, 'value' => null, 'rows' => 3, 'hint' => null, 'required' => false])
@php $id = str_replace(['[', ']', '.'], '_', $name); @endphp
<div class="{{ $attributes->get('wrapper', 'mb-3') }}">
    @if ($label)<label class="form-label" for="{{ $id }}">{{ $label }}@if($required) <span class="text-danger">*</span>@endif</label>@endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @if($required) required @endif
        {{ $attributes->except('wrapper')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($hint)<div class="form-text">{{ $hint }}</div>@endif
</div>
