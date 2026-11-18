@props(['name', 'label', 'checked' => false, 'hint' => null])
<div class="{{ $attributes->get('wrapper', 'mb-3') }}">
    <div class="form-check form-switch">
        <input type="hidden" name="{{ $name }}" value="0">
        <input class="form-check-input" type="checkbox" role="switch" id="{{ $name }}" name="{{ $name }}" value="1" @checked(old($name, $checked))>
        <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
    </div>
    @if ($hint)<div class="form-text">{{ $hint }}</div>@endif
</div>
