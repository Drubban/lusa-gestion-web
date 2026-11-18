@props(['label', 'value', 'icon' => 'bi-circle', 'tone' => 'primary', 'href' => null, 'hint' => null])
@php $tag = $href ? 'a' : 'div'; @endphp
<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class('card stat text-reset') }}>
    <span class="stat-icon tone-{{ $tone }}"><i class="bi {{ $icon }}"></i></span>
    <span>
        <span class="stat-value d-block">{{ $value }}</span>
        <span class="stat-label">{{ $label }}</span>
        @if ($hint)<span class="d-block small text-muted">{{ $hint }}</span>@endif
    </span>
</{{ $tag }}>
