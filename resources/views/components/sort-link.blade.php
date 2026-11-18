@props(['column', 'label'])
@php
    $activa = request('sort') === $column;
    $dir = $activa && request('direction') === 'asc' ? 'desc' : 'asc';
@endphp
<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir, 'page' => null]) }}">
    {{ $label }} @if($activa)<i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>@endif
</a>
